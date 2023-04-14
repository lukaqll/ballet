<?php 
namespace App\Services\Api;

use App\Models\ClassModel;
use App\Models\Contract;
use App\Models\Student;
use App\Models\User;
use App\Services\ContractsService;
use App\Services\InvoicesService;
use App\Services\ParametersService;
use App\Services\UsersService;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClicksignService extends AbstractApiService
{
    private $accesToken;
    protected $baseUrl;
    protected $basePath = '/api/v1';

    public function __construct()
    {
        $this->accesToken = env('CLICKSIGN_TOKEN');
        $this->baseUrl = env('CLICKSIGN_HOST');
        parent::__construct();
    }

    /**
     * create signatory
     */
    public function createSignatory( $user ){

        $data = [
            'signer' => [
                'name'          => $user->name,
                'email'         => $user->email,
                'phone_number'  => $user->phone,
                'auths'         => $user->is_whatsapp ? ['whatsapp'] : ['sms'],
                'documentation' => $user->cpf,

                'official_document_enabled' => true
            ]
        ];

        $url = '/signers';
        $response = $this->request('post', $url, [
            'query' => [
                'access_token' => $this->accesToken
            ],
            'json' => $data
        ]);

        if( $response['status'] == 'success' ){
            # code
            $signer = $response['data']->signer;

            if( $user instanceof User ){
                $user->update(['signer_key' => $signer->key]);
            } else {
                $user->signer_key = $signer->key;
            }

            return $user;
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * delete signatory
     */
    public function deleteSignatory( $sign_key ){

        $url = "/signers/".$sign_key;

        $userService = new UsersService;
        $response = $this->request('delete', $url, [
            'query' => [
                'access_token' => $this->accesToken
            ]
        ]);

        if( $response['status'] == 'success' ){

            $user = $userService->get(['sign_key' => $sign_key]);
            if( !empty($user) ){
                $user->update(['signer_key' => null]);
            }

            return $user;
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * create a document
     */
    public function createDocument( Student $student, string $base64, ClassModel $class ){

        $user = $student->user;

        $parametersService = new ParametersService;

        if( empty($user->signer_key) )
            throw ValidationException::withMessages(['O usuário responsável não está cadastrado como signatário']);

        $rand = strtotime(date('YmdHis'));

        $fileName = "contrato-{$student->id}-{$student->name}-{$rand}.pdf";
        $path = '/students/contracts/'.$fileName;

        $deadlineAt = date('Y-m-d', strtotime('+90 days', strtotime(date('Y-m-d')))) . 'T00:00:00-03:00';
        $data = [
            'document' => [
                'path' => $path,
                'content_base64' => $base64,
                'deadline_at' => $deadlineAt,
                'remind_interval' => 1,
            ]
        ];

        $response = $this->request('post', '/documents', [
            'query' => [
                'access_token' => $this->accesToken
            ],
            'form_params' => $data
        ]);
        
        if( $response['status'] == 'success' ){
            
            $contractResponse = $response['data']->document;

            $contractsService = new ContractsService;
            $contract = $contractsService->create([
                'id_student' => $student->id,
                'id_class' => $class->id,
                'path' => $contractResponse->path,
                'key' => $contractResponse->key,
                'status' => $contractResponse->status,
            ]);
            $student->update(['status' => 'CP']);

            $result = $this->addSignatoryInDoc($student->user, $contract, 'contractor');

            // add default signer in doc
            $defaultSigner = $parametersService->getSigner(true);
            if( empty($defaultSigner->signer_key) )
                throw ValidationException::withMessages(['Signatário padrão não configurado']);

            $this->addSignatoryInDoc($defaultSigner, $contract, 'contractee');


            return $contract;
            
        } else {
            throw ValidationException::withMessages($response['message']);
        }

        return $response;
    }

    /**
     * add signatory in document
     */
    public function addSignatoryInDoc( $user, Contract $contract, $signAs = 'contractor'){

        $data = [
            'list' => [
                'document_key' => $contract->key,
                'signer_key' => $user->signer_key,
                'sign_as' => $signAs,
                'message' => "Olá {$user->name}, assine este contrato para concluir sua matrícula na Ellegance Ballet."
            ]
        ];

        $response = $this->request('post', '/lists', [
            'query' => [
                'access_token' => $this->accesToken
            ],
            'form_params' => $data
        ]);
        
        if( $response['status'] == 'success' ){
            $result = $response['data'];

            $this->sendNotification( $result->list->request_signature_key );
            
            return $result;
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * remove a signer from a document
     */
    public function removeDocSigner(User $user, Contract $contract) {
        if (empty($user->signer_key))
            throw ValidationException::withMessages(['O usuário responsável não está cadastrado como signatário']);
        
        $doc = $this->getDocument($contract);
        $signer = array_filter($doc->document->signers, function($signer) use($user) {
            return $signer->key == $user->signer_key && (empty($signer->signature) || $signer->signature->validation->status != 'conferred');
        });

        if (empty($signer))
            throw ValidationException::withMessages(['Signatário não encontrado']);

        $signer = array_values($signer)[0];
        $url = "/lists/{$signer->list_key}";
        $response = $this->request('delete', $url, [
            'query' => [
                'access_token' => $this->accesToken
            ]
        ]);

        if( $response['status'] != 'success' )
            throw ValidationException::withMessages($response['message']);
    }

    public function replaceDocSigner(User $user) {
        foreach ($user->contracts as $contract) {
            if ($contract->status != 'running') continue;
            
            try {
                $this->removeDocSigner($user, $contract);
                $newSigner = $this->createSignatory($user);
                $this->addSignatoryInDoc($newSigner, $contract);
            } catch (ValidationException $e) {
                continue;
            }
        }
    }

    /**
     * list documents
     */
    public function listDocuments(){

        $url = '/documents';
        $response = $this->request('get', $url, [
            'verify' => false,
            'query' => [
                'access_token' => $this->accesToken
            ]
        ]);
            
        if( $response['status'] == 'success' ){
            # code
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * get contract
     */
    public function getDocument( Contract $contract ){

        $url = '/documents/'.$contract->key;

        $response = $this->request('get', $url, [
            'verify' => false,
            'query' => [
                'access_token' => $this->accesToken,
            ]
        ]);
            
        if( $response['status'] == 'success' ){
            return $response['data'];
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * cancel contract
     */
    public function cancelContract( Contract $contract ){

        $url = '/documents/'.$contract->key.'/cancel';

        $response = $this->request('patch', $url, [
            'verify' => false,
            'query' => [
                'access_token' => $this->accesToken,
            ]
        ]);
            
        if( $response['status'] == 'success' ){
            $result = $response['data'];
            $contract->update(['status' => $result->document->status]);
            return $contract;
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * send notification
     */
    public function sendNotification( string $request_signature_key ){

        $url = '/notifications';
        $response = $this->request('post', $url, [
            'json' => [
                'request_signature_key' => $request_signature_key
            ],
            'verify' => false,
            'query' => [
                'access_token' => $this->accesToken
            ]
        ]);
            
        if( $response['status'] == 'success' ){
            return $response['data'];            
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * notify all
     */
    public function notifyAll( Contract $contract ){

        $document = $this->getDocument($contract);
        $result = [];
        foreach( $document->document->signers as $signer ){
            try {
                $this->sendNotification( $signer->request_signature_key );
                $result[$signer->key] = [
                    'name' => $signer->name,
                    'success' => true
                ];
            } catch (ValidationException $e) {
                $result[$signer->key] = [
                    'name' => $signer->name,
                    'success' => false,
                    'messages' => $e->errors()[0]
                ];
            }
        }

        return [$contract->student->user->name => $result];
    }

    public function onContractCloseHandle(Contract $contract, $status){

        $invoicesService = new InvoicesService;

        if( $contract->status != $status ){

            $student = $contract->student;

            $contract->update(['status' => $status]);
            $student->update(['status' => 'A']);
    
            $studentClass = $contract->studentClass;
    
            if( !empty($studentClass) ){

                if( empty( $studentClass->approved_at ) ){
                    $studentClass->update(['approved_at' => date('Y-m-d H:i:s')]);
                }

                // cria fatura de proporcional
                // $invoicesService->generateClassInvoice($studentClass);
            }
        }

    }
}

