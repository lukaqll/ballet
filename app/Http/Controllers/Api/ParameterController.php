<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ParameterController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all
     * 
     * @return  json
     */
    public function list( Request $request )
    {
        try {

            $dataFilter = $request->all();
            $result = $this->parametersService->list( $dataFilter, ['id'] );

            $response = [ 'status' => 'success', 'data' => ($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * get by key and value
     * 
     * @return  json
     */
    public function get( Request $request ){

        try {

            $dataFilter = $request->all();
            $result = $this->parametersService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => ($result) ];
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * get by id
     * 
     * @return  json
     */
    public function getById( $id ){

        try {

            $result = $this->parametersService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => ($result) ];

        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * create
     * 
     * @return  json
     */
    public function create( Request $request ){

        try {

            $validData = $request->validate([
                'name' => 'required|string|unique:table',
            ]);
            
            $created = $this->parametersService->create( $validData );
            $response = [ 'status' => 'success', 'data' => ($created) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * update
     * 
     * @return  json
     */
    public function update( Request $request, $id ){

        try {
            
            $validData = $request->validate([
                'name' => 'required|string|unique:table,name,'.$id,
            ]);
            $updated = $this->parametersService->updateById( $id, $validData);
            $response = [ 'status' => 'success', 'data' => ($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * delete
     * 
     * @return  json
     */
    public function delete( $id ){

        try {

            $deleted = $this->parametersService->deleteById( $id );
            $response = [ 'status' => 'success', 'data' => ($deleted) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }


    /**
     * get contract
     */
    public function getContract(){
        try {

            $contract = $this->parametersService->getContract();
            $response = [ 'status' => 'success', 'data' => $contract ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }


    /**
     * save signer
     */
    public function saveSigner(Request $request){
        try {

            DB::beginTransaction();

            $validData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string',
                'phone' => 'required|string',
                'cpf' => 'required|string',
            ]);

            $result = $this->parametersService->saveSigner($validData);
            $response = [ 'status' => 'success', 'data' => $result ];

            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * get signer
     */
    public function getSigner( Request $request ){
        try {

            $result = $this->parametersService->getSigner();
            $response = [ 'status' => 'success', 'data' => $result ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    
    /**
     * get config
     */
    public function getConfig( Request $request ){
        try {

            $result = $this->parametersService->list(['operation' => 'general-config']);
            $response = [ 'status' => 'success', 'data' => $result ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * save config
     */
    public function saveConfig( Request $request ){
        try {

            $invoice_allow = $request->input('invoice_allow');
            $send_invoice_mail = $request->input('send_invoice_mail');

            $this->parametersService->saveConfig('invoice_allow', $invoice_allow);
            $this->parametersService->saveConfig('send_invoice_mail', $send_invoice_mail);

            $response = [ 'status' => 'success', 'data' => true ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
    
}