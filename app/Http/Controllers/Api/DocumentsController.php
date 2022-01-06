<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class DocumentsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all by student
     * 
     * @return  json
     */
    public function listByStudent( $idStudent )
    {
        try {

            $student = $this->studentsService->find($idStudent);
            $response = [ 'status' => 'success', 'data' => $student->contracts ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * create
     */
    public function generate( $idStudent )
    {
        try {

            $student = $this->studentsService->find( $idStudent );
            $openContract = $student->openContract;

            if( $student->status == 'MP' )
                throw ValidationException::withMessages(["Aluno com matrícula pendente, ao aprovar sua matrícula, será gerado o contrato e enviado para o E-mail {$student->user->email}"]);

            if( !empty($openContract) )
                throw ValidationException::withMessages(['Já existe um contrato para este aluno']);

            $result = $this->documentsService->generateContract( $student );

            $response = [ 'status' => 'success', 'data' => $result ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * cancel
     */
    public function cancelContract( $id ){
        
        try {

            $contract = $this->contractsService->find($id);
            $result = $this->clicksignService->cancelContract( $contract );
            $response = [ 'status' => 'success', 'data' => $result ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * notify
     */
    public function notify( $id ){
        
        try {

            $contract = $this->contractsService->find($id);
            $result = $this->clicksignService->notifyAll( $contract );
            $response = [ 'status' => 'success', 'data' => $contract->student ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * list all
     * 
     * @return  json
     */
    public function list( Request $request )
    {
        try {

            $result = $this->contractsService->list($request->all(), ['status'], 'desc');
            $response = [ 'status' => 'success', 'data' => ContractResource::collection($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    public function viewDocument( $id ){

        try {

            $contract = $this->contractsService->find($id);

            if( empty($contract) )
                abort(404);

            $document = $this->clicksignService->getDocument($contract);

            if( $document->document->status == 'closed' ){
                $url = $document->document->downloads->signed_file_url;
            } else {
                $url = $document->document->downloads->original_file_url;
            }

            return redirect($url);
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    public function signDocument( $id ){

        try {

            $contract = $this->contractsService->find($id);

            if( empty($contract) )
                abort(404);

            $document = $this->clicksignService->getDocument($contract);

            if( !empty( $document->document->signers ) ){
                $signer = $document->document->signers[0];
            } else {
                return redirect()->back();
            }

            $url = $signer->url;

            return redirect($url);
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * list all
     * 
     * @return  json
     */
    public function listSelf( Request $request )
    {
        try {

            $user = auth('api')->user();
            $result = $user->contracts;
            $response = [ 'status' => 'success', 'data' => ContractResource::collection($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }
}