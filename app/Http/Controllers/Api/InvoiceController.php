<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list by user
     * 
     * @return  json
     */
    public function listByUser( $id )
    {
        try {

            $result = $this->invoicesService->list( ['id_user' => $id], ['expires_at'] );
            $response = [ 'status' => 'success', 'data' => InvoiceResource::collection($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * list by self
     * 
     * @return  json
     */
    public function listSelf()
    {
        try {

            $user = auth('api')->user();
            $result = $this->invoicesService->list( ['id_user' => $user->id], ['expires_at'] );
            $response = [ 'status' => 'success', 'data' => InvoiceResource::collection($result) ];
            
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
            $result = $this->invoicesService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($result) ];
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

            $result = $this->invoicesService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($result) ];

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
                'id_user' => 'required|integer|exists:users,id',
                'expires_at' => 'required|date',
                'value' => 'required|string'
            ]);

            if( $validData['expires_at'] < date('Y-m-d'))
                throw ValidationException::withMessages(['A data de vencimento não pode ser menor que hoje']);

            $validData['value'] = $this->unMaskMoney($validData['value']);            
            $validData['expires_at'] = date('Y-m-d 23:59:59', strtotime($validData['expires_at']));
            $validData['status'] = 'A';

            $created = $this->invoicesService->create( $validData );
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($created) ];

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
            
            $invoice = $this->invoicesService->find($id);
            if($invoice->status != 'A')
                throw ValidationException::withMessages(['Esta fatura não está mais aberta']);

            $validData = $request->validate([
                'expires_at' => 'required|date',
                'value' => 'required|string'
            ]);

            if( $validData['expires_at'] < date('Y-m-d'))
                throw ValidationException::withMessages(['A data de vencimento não pode ser menor que hoje']);
                
            $validData['expires_at'] = date('Y-m-d 23:59:59', strtotime($validData['expires_at']));
            $validData['value'] = $this->unMaskMoney($validData['value']);            

            $updated = $this->invoicesService->updateById( $id, $validData);
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($updated) ];

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
    public function cancelInvoice( $id ){

        try {

            $invoice = $this->invoicesService->find($id);
            if($invoice->status != 'A')
                throw ValidationException::withMessages(['Esta fatura não está mais aberta']);

            $canceled = $this->invoicesService->updateById( $id, ['status' => 'C'] );
            $response = [ 'status' => 'success', 'data' => new InvoiceResource($canceled) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}