<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SaleResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
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
            $result = $this->salesService->list( $dataFilter, ['id'] );

            $response = [ 'status' => 'success', 'data' => SaleResource::collection($result) ];
            
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
            $result = $this->salesService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new SaleResource($result) ];
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

            $result = $this->salesService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new SaleResource($result) ];

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
                'id_student' => 'nullable|exists:students,id',
                'id_unit' => 'nullable|exists:units,id',
                'description' => 'nullable|string',
                'color' => 'nullable|string',
                'size' => 'nullable|string',
                'payment_method' => 'nullable|string',
                'status' => 'nullable|string',
                'payment_status' => 'nullable|string',
                'price' => 'nullable|string',
                'paid_price' => 'nullable|string',
                'paid_at' => 'nullable|date',
            ]);
            
            $validData['price'] = $this->unMaskMoney($validData['price']);            
            $validData['paid_price'] = $this->unMaskMoney($validData['paid_price']);   

            $created = $this->salesService->create( $validData );
            $response = [ 'status' => 'success', 'data' => new SaleResource($created) ];

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
                'id_student' => 'nullable|exists:students,id',
                'id_unit' => 'nullable|exists:units,id',
                'description' => 'nullable|string',
                'color' => 'nullable|string',
                'size' => 'nullable|string',
                'payment_method' => 'nullable|string',
                'status' => 'nullable|string',
                'payment_status' => 'nullable|string',
                'price' => 'nullable|string',
                'paid_price' => 'nullable|string',
                'paid_at' => 'nullable|date',
            ]);

            $validData['price'] = $this->unMaskMoney($validData['price']);            
            $validData['paid_price'] = $this->unMaskMoney($validData['paid_price']);   

            $updated = $this->salesService->updateById( $id, $validData);
            $response = [ 'status' => 'success', 'data' => new SaleResource($updated) ];

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

            $deleted = $this->salesService->deleteById( $id );
            $response = [ 'status' => 'success', 'data' => new SaleResource($deleted) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}