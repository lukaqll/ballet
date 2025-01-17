<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassController extends Controller
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
            $result = $this->classesService->list( $dataFilter, ['id'] );

            $response = [ 'status' => 'success', 'data' => ClassResource::collection($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    }

    /**
     * public list
     * 
     * @return  json
     */
    public function publicList( Request $request )
    {
        try {

            $dataFilter = $request->all();
            $result = $this->classesService->list( $dataFilter, ['id'] )->where('full', '!=', 1);

            $response = [ 'status' => 'success', 'data' => ClassResource::collection($result) ];
            
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
            $result = $this->classesService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new ClassResource($result) ];
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

            $result = $this->classesService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new ClassResource($result) ];

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
                'id_unit' => 'required|integer|exists:units,id',
                'name' => 'required|string',
                'team' => 'nullable|string',
                'value' => 'required|string',
            ]);
            $validData['value'] = $this->unMaskMoney($validData['value']);
            
            $created = $this->classesService->create( $validData );
            $response = [ 'status' => 'success', 'data' => new ClassResource($created) ];

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
                'id_unit' => 'required|integer|exists:units,id',
                'name' => 'required|string',
                'team' => 'nullable|string',
                'value' => 'required|string',
            ]);
            $validData['value'] = $this->unMaskMoney($validData['value']);

            $updated = $this->classesService->updateById( $id, $validData );
            $response = [ 'status' => 'success', 'data' => new ClassResource($updated) ];

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
            DB::beginTransaction();
            $deleted = $this->classesService->delete( $id );
            $response = [ 'status' => 'success', 'data' => ($deleted) ];
            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function toggleFull( $id ){
        try {
            
            $class = $this->classesService->find($id);
            $isFull = 0;
            if( $class->full == 1 ){
                $isFull = 0;
            } else {
                $isFull = 1;
            }

            $updated = $this->classesService->updateById($id, ['full' => $isFull]);
            $response = [ 'status' => 'success', 'data' => ($updated) ];
            
        } catch ( ValidationException $e ){
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}