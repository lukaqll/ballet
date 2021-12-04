<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
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
            $result = $this->studentsService->list( $dataFilter, ['id'] );

            $response = [ 'status' => 'success', 'data' => StudentResource::collection($result) ];
            
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
            $result = $this->studentsService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new StudentResource($result) ];
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

            $result = $this->studentsService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new StudentResource($result) ];

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
                'id_user'   => 'required|integer|exists:users,id',
                'name'      => 'required|string',
                'nick_name' => 'required|string',
                'birthdate' => 'required|date',
                'classes'   => 'nullable|array',
                'classes.*' => 'required|integer|exists:classes,id',
                'picture'   => 'nullable|image'
            ]);
            $validData['status'] = 'A';           
            $student = $this->studentsService->create( $validData );

            if( isset($validData['classes']) )
                $this->studentsService->updateStudentClasses( $student, $validData['classes'] );            

            if( isset($validData['picture']) )
                $this->studentsService->uploadPicture($student, $validData['picture']);

            $response = [ 'status' => 'success', 'data' => new StudentResource($student) ];

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
                'id_user'   => 'required|integer|exists:users,id',
                'name'      => 'required|string',
                'nick_name' => 'required|string',
                'birthdate' => 'required|date',
                'classes'   => 'nullable|array',
                'classes.*' => 'required|integer|exists:classes,id',
            ]);
            $student = $this->studentsService->updateById( $id, $validData);

            if( isset($validData['classes']) )
                $this->studentsService->updateStudentClasses( $student, $validData['classes'] );
            
            $response = [ 'status' => 'success', 'data' => new StudentResource($student) ];

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

            $deleted = $this->studentsService->deleteById( $id );
            $response = [ 'status' => 'success', 'data' => new StudentResource($deleted) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }


    public function adminUploadPicture( Request $request, $id ) {
        try {
            
            $data = $request->validate([
                'picture' => 'required|image',
            ]);
            
            $student = $this->studentsService->find($id);
            $updated = $this->studentsService->uploadPicture($student, $data['picture']);
            
            $response = [ 'status' => 'success', 'data' => new StudentResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}