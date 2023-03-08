<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function list() {
        try {

            $result = $this->paymentMethodService->list( ['deleted' => 0], ['name'] );
            $response = [ 'status' => 'success', 'data' => $result ];
            
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

            $result = $this->paymentMethodService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => $result ];

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
                'name' => 'required|string|unique:payment_methods',
            ]);
            
            $created = $this->paymentMethodService->create( $validData );
            $response = [ 'status' => 'success', 'data' => $created ];

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
                'name' => 'required|string|unique:payment_methods,name,'.$id,
            ]);
            $updated = $this->paymentMethodService->updateById( $id, $validData);
            $response = [ 'status' => 'success', 'data' => $updated ];

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

            $deleted = $this->paymentMethodService->updateById( $id, ['deleted' => 1] );
            $response = [ 'status' => 'success', 'data' => $deleted ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}
