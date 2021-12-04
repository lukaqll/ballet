<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
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
            $result = $this->usersService->listClients( $dataFilter, ['id'] );

            $response = [ 'status' => 'success', 'data' => UserResource::collection($result) ];
            
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
            $result = $this->usersService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new UserResource($result) ];
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

            $result = $this->usersService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new UserResource($result) ];

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
    public function createWithStudent( Request $request ){

        try {

            DB::beginTransaction();

            $userValidData = $request->validate([
                'user_name'     => 'required|string',
                'user_email'    => 'required|string|email|unique:users,email',
                'user_cpf'      => 'required|string|size:14|unique:users,cpf',
                'user_phone'    => 'required|string',
                'user_password' => 'required|string|min:6',
                'user_password_confirmation' => 'required|string|min:6',
                'user_picture' => 'nullable|image',
            ]);
            $userData = [
                'name'      => $userValidData['user_name'],
                'email'     => $userValidData['user_email'],
                'cpf'       => $userValidData['user_cpf'],
                'phone'     => $userValidData['user_phone'],
                'password'  => $userValidData['user_password'],
                'password_confirmation' => $userValidData['user_password_confirmation'],
                'picture' => $userValidData['user_picture'] ?? null,
                'status'  => 'A'
            ];
            $user = $this->usersService->createUser($userData);

            $studentValidData = $request->validate([
                'student_id_class'  => 'nullable|integer|exists:classes,id',
                'student_name'      => 'required|string',
                'student_nick_name' => 'required|string',
                'student_birthdate' => 'required|date',
                'student_picture'      => 'nullable|image',
            ]);
            $studentData = [
                'id_user'   => $user->id,
                'id_class'  => $studentValidData['student_id_class'] ?? null,
                'name'      => $studentValidData['student_name'],
                'nick_name' => $studentValidData['student_nick_name'],
                'birthdate' => $studentValidData['student_birthdate'],
                'picture' => $studentValidData['student_picture'] ?? null,
                'status'    => 'A',
            ];
            $student = $this->studentsService->createStudent($studentData);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($user) ];

            DB::commit();
        } catch ( ValidationException $e ){
            DB::rollBack();
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
                'name' => 'required|string|unique:users,name,'.$id,
                'email' => 'required|string|email|unique:users,email,'.$id,
                'cpf' => 'required|string|size:14|unique:users,cpf,'.$id,
                'phone' => 'required|string',
            ]);
            $updated = $this->usersService->updateById( $id, $validData);
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

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

            $deleted = $this->usersService->deleteById( $id );
            $response = [ 'status' => 'success', 'data' => new UserResource($deleted) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function adminPasswordUpdate( Request $request, $id ) {
        try {
            
            $userData = $request->validate([
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);
            
            if( $userData['password'] != $userData['password_confirmation'] )
                throw ValidationException::withMessages(['As senhas não conferem']);

            $updated = $this->usersService->updateById($id, ['password' => bcrypt($userData['password'])]);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function adminUploadPicture( Request $request, $id ) {
        try {
            
            $userData = $request->validate([
                'picture' => 'required|image',
            ]);
            
            $user = $this->usersService->find($id);
            $updated = $this->usersService->uploadPicture($user, $userData['picture']);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function adminSelfUpdate( Request $request ) {
        try {
            
            $user = auth('api')->user();
            $userData = $request->validate([
                'name' => 'required|string|min:1',
                'phone' => 'nullable|string',
                'email' => 'required|string|unique:users,email,'.$user->id,
            ]);
            
            $updated = $this->usersService->updateById($user->id, $userData);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    public function passwordUpdate( Request $request ) {
        try {
            
            $user = auth('api')->user();

            $userData = $request->validate([
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);
            
            if( $userData['password'] != $userData['password_confirmation'] )
                throw ValidationException::withMessages(['As senhas não conferem']);

            $updated = $this->usersService->updateById($user->id, ['password' => bcrypt($userData['password'])]);
            
            $response = [ 'status' => 'success', 'data' => new UserResource($updated) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}



