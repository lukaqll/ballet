<?php 
namespace App\Services\Api;

use App\Models\Contract;
use App\Models\Student;
use App\Models\User;
use App\Services\ContractsService;
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
    public function createSignatory( User $user ){

        $data = [
            'signer' => [
                'email'         => $user->email,
                'phone_number'  => $user->phone,
                'auths'         => $user->is_whatsapp ? ['whatsapp'] : ['sms'],
                'name'          => $user->name,
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
            $user->update(['signer_key' => $signer->key]);
            return $user;
        } else {
            throw ValidationException::withMessages($response['message']);
        }
    }

    /**
     * create a document
     */
    public function createDocument( Student $student, string $base64 ){

        $user = $student->user;

        if( empty($user->signer_key) )
            throw ValidationException::withMessages(['O usuário responsavel não está cadastrado como signatário']);

        $rand = strtotime(date('YmdHis'));

        $fileName = "contrato-{$student->id}-{$student->name}-{$rand}.pdf";
        $path = '/students/contracts/'.$fileName;

        $data = [
            'document' => [
                'path' => $path,
                'content_base64' => $base64,
                'deadline_at' => 30,
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
                'path' => $contractResponse->path,
                'key' => $contractResponse->key,
                'status' => $contractResponse->status,
            ]);
            $student->update(['status' => 'CP']);

            $result = $this->addSignatoryInDoc($student->user, $contract);
            return $contract;
            
        } else {
            throw ValidationException::withMessages($response['message']);
        }

        return $response;
    }

    /**
     * add signatory in document
     */
    public function addSignatoryInDoc(User $user, Contract $contract){

        $data = [
            'list' => [
                'document_key' => $contract->key,
                'signer_key' => $user->signer_key,
                'sign_as' => 'sign',
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
        foreach( $document->document->signers as $signer ){
            $this->sendNotification( $signer->request_signature_key );
        }

        return true;
    }
}

