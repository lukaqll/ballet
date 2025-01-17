<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContractResource;
use App\Http\Resources\StudentResource;
use App\Models\Contract;
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
    public function generate( $idStudent, $idClass )
    {
        try {

            $student = $this->studentsService->find( $idStudent );

            if( $student->status == 'MP' )
                throw ValidationException::withMessages(["Aluno com matrícula pendente, ao aprovar sua matrícula, será gerado o contrato e enviado para o E-mail {$student->user->email}"]);


            $class = $this->classesService->find($idClass);
            if( empty($class) )
                throw ValidationException::withMessages(['Falha ao buscar aula']);

            $isStudentClassExists = $this->studentClassesService->get(['id_class' => $idClass, 'id_student' => $idStudent]);
            if( empty($isStudentClassExists) )
                throw ValidationException::withMessages(['Aluno não matriculado nesta aula']);

            $result = $this->documentsService->generateContract( $student, $class );

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

            if( $contract->status == 'running' ){
                $result = $this->clicksignService->cancelContract( $contract );
            } else {
                $contract->update(['status' => 'canceled']);
                $result = $contract;
            }
            
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
            $response = [ 'status' => 'success', 'data' => $result ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * notify all
     */
    public function notifyAll() {
        
        try {

            $contracts = Contract::where('status', 'running')->get();
            $result = [];
            foreach ($contracts as $contract) {
                $result[] = $this->clicksignService->notifyAll( $contract );
            }
            $response = [ 'status' => 'success', 'data' => $result ];
            
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

                if( !empty($document->document->signers[1]) ){
                    $signer = $document->document->signers[1]; 
                } else {
                    $signer = $document->document->signers[0]; 
                }

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