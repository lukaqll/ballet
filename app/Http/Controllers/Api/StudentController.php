<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * list all
     * 
     * @return  json
     */
    public function filter( Request $request )
    {
        try {

            $dataFilter = $request->all();
            $result = $this->studentsService->filter( $dataFilter, ['id'] );

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
     * get by key and value
     * 
     * @return  json
     */
    public function listBySelf( Request $request ){

        try {

            $user = auth('api')->user();
            
            $dataFilter = $request->all();
            $dataFilter['id_user'] = $user->id;
            
            $result = $this->studentsService->list( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => StudentResource::collection($result) ];
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
     * get by self user
     * 
     * @return  json
     */
    public function getBySelf( $id ){

        try {

            $user = auth('api')->user();
            $result = $this->studentsService->get( ['id' => $id, 'id_user' => $user->id] );
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
                // 'nick_name' => 'required|string',
                'birthdate' => 'required|date',
                // 'classes'   => 'nullable|array',
                // 'classes.*' => 'required|integer|exists:classes,id',
                'picture'   => 'nullable|image',
                'health_problem'   => 'nullable|string',
                'food_restriction' => 'nullable|string',
                'in_school'        => 'nullable|string',
                'school_time'      => 'nullable|string',
            ]);
            $validData['status'] = 'A';           
            $student = $this->studentsService->create( $validData );

            // if( isset($validData['classes']) )
            //     $this->studentsService->updateStudentClasses( $student, $validData['classes'] );            

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
                // 'nick_name' => 'required|string',
                'birthdate' => 'required|date',
                // 'classes'   => 'nullable|array',
                // 'classes.*' => 'required|integer|exists:classes,id',
                'health_problem'   => 'nullable',
                'food_restriction' => 'nullable',
                'in_school'        => 'nullable',
                'school_time'      => 'nullable',
                'status'           => 'required|string',
            ]);
            $student = $this->studentsService->updateById( $id, $validData);

            // if( isset($validData['classes']) )
            //     $this->studentsService->updateStudentClasses( $student, $validData['classes'] );
            
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
    public function selfUpdate( Request $request, $id ){

        try {
            
            $user = auth('api')->user();

            $student = $this->studentsService->find($id);
            if( $student->id_user != $user->id )
                throw ValidationException::withMessages(['Falha ao editar aluno']);

            $validData = $request->validate([
                'id_user'   => 'required|integer|exists:users,id',
                'name'      => 'required|string',
                // 'nick_name' => 'required|string',
                'birthdate' => 'required|date',
                'classes'   => 'nullable|array',
                'classes.*' => 'required|integer|exists:classes,id',
                'health_problem'   => 'nullable|string',
                'food_restriction' => 'nullable|string',
                'in_school'        => 'nullable|string',
                'school_time'      => 'nullable|string',
            ]);
            $updated = $this->studentsService->updateById( $id, $validData);
            
            $response = [ 'status' => 'success', 'data' => new StudentResource($updated) ];

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

    public function uploadPicture( Request $request, $id ) {
        try {
            
            $user = auth('api')->user();

            $student = $this->studentsService->find($id);
            if( $student->id_user != $user->id )
                throw ValidationException::withMessages(['Falha ao alterar foto']);

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

    public function addClass($idStudent, $idClass){

        try {

            DB::beginTransaction();
            $student = $this->studentsService->find($idStudent);
            if( empty($student) )
                throw ValidationException::withMessages(['Falha ao encontrar aluno']);

            $class = $this->classesService->find($idClass);
            if( empty($class) )
                throw ValidationException::withMessages(['Falha ao encontrar aula']);

            $isStudentClassExists = $this->studentClassesService->get(['id_class' => $idClass, 'id_student' => $idStudent]);
            if( !empty($isStudentClassExists) )
                throw ValidationException::withMessages(['Aluno já matriculado nesta aula']);

            $studentClass = $this->studentClassesService->create([
                'id_student' => $idStudent,
                'id_class'   => $idClass
            ]);

            // create a contract if not exists
            if( empty($studentClass->notCanceledContract) ){
                $contractResult = $this->documentsService->generateContract( $student, $class );
            } else {

                // approve
                $studentClass->update(['approved_at' => date('Y-m-d H:i:s')]);
            }

            $response = [ 'status' => 'success', 'data' => new StudentResource($student) ];
            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function removeClass($idStudent, $idClass){

        try {

            DB::beginTransaction();

            $student = $this->studentsService->find($idStudent);
            if( empty($student) )
                throw ValidationException::withMessages(['Falha ao encontrar aluno']);

            $class = $this->classesService->find($idClass);
            if( empty($class) )
                throw ValidationException::withMessages(['Falha ao encontrar aula']);

            $isStudentClassExists = $this->studentClassesService->get(['id_class' => $idClass, 'id_student' => $idStudent]);
            if( empty($isStudentClassExists) )
                throw ValidationException::withMessages(['Aluno não matriculado nesta aula']);

            if( !empty($isStudentClassExists->openContract))
                throw ValidationException::withMessages(['Existe um contrato aberto deste aulo para esta aula']);

            $isStudentClassExists->delete();

            $response = [ 'status' => 'success', 'data' => new StudentResource($student) ];
            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}