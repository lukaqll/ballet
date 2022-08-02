<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvoiceAddController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all by user
     * 
     * @return  json
     */
    public function list( $idUser )
    {
        try {

            $user = $this->userService->find($idUser);
            $result = $user->invoiceAdds;
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

            $result = $this->invoiceAddService->get( ['id' => $id] );
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
                'id_user' => 'required|exists:users,id',
                'description' => 'required|string',
                'value' => 'required|string',
                'month' => 'required|date'
            ]);
            $validData['value'] = $this->unMaskMoney($validData['value']);
            $validData['month'] = $validData['month'].'-01';
            
            $created = $this->invoiceAddService->create( $validData );
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
            
            $added = $this->invoiceAddService->get( ['id' => $id] );
            if(!empty($added->id_invoice))
                throw ValidationException::withMessages(['Este adicional já foi usado']);

            $validData = $request->validate([
                'description' => 'required|string',
                'value' => 'required|string',
                'month' => 'required|date'
            ]);
            $validData['value'] = $this->unMaskMoney($validData['value']);
            $validData['month'] = $validData['month'].'-01';

            $updated = $this->invoiceAddService->updateById( $id, $validData );
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

            $deleted = $this->invoiceAddService->deleteById( $id );
            $response = [ 'status' => 'success', 'data' => ($deleted) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}