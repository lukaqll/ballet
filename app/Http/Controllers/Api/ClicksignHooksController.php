<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClicksignHooksController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 
     * @return  json
     */
    public function hooksCallback( Request $request )
    {
        try {

            $req = $request->json()->all();
            
            $event = $req['event'];
            $document = $req['document'];

            if( empty($event) || empty($document) )
                return 'Falha ao obter requisição';

            // find contract
            $contract = $this->contractsService->get(['key' => $document['key']]);

            if( empty($contract) )
                return 'Contrato não encontrado';

            // get student
            $student = $contract->student;

            // get clicksign document
            $docContractObj = $this->clicksignService->getDocument( $contract );
            $docContract = $docContractObj->document;
            
            switch( $event['name'] ) {

                case 'upload': 
                    // Ocorre quando é realizado o upload de um documento.
                    # code
                break;

                case 'add_signer': 
                    // são adicionados signatários a um documento
                    # code
                break;

                case 'remove_signer': 
                    // quando são removidos signatários de um documento
                    # code
                break;

                case 'sign': 
                    // quando um signatário assina um documento
                    # code
                break;

                case 'close': case 'auto_close': 
                    // documento é finalizado automaticamente logo após a última assinatura
                    $contract->update(['status' => $docContract->status]);
                    $student->update(['status' => 'A']);
                break;

                case 'deadline': 
                    // Ocorre quando a data limite de assinatura de um documento for atingida (Se o documento contiver ao menos uma assinatura, ele é finalizado. Caso contrário, o documento é cancelado)
                    $contract->update(['status' => $docContract->status]);
                    if( $docContract->status == 'closed' ){
                        $student->update(['status' => 'A']);
                    }
                break;

                case 'cancel': 
                    // quando um documento é cancelado manualmente
                    $contract->update(['status' => $docContract->status]);
                break;

                case 'update_deadline': 
                    // quando a data limite de assinatura de um documento é alterada
                    $contract->update(['status' => $docContract->status]);
                break;

                case 'update_auto_close': 
                    // quando a opção de finalização automática de um documento é alterada
                    $contract->update(['status' => $docContract->status]);
                break;
            }
            
            $response = [ 'status' => 'success', 'data' => '' ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

}