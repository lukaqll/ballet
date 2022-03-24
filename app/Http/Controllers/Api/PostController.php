<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
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
            $result = $this->postsService->list( $dataFilter );

            $response = [ 'status' => 'success', 'data' => PostResource::collection($result) ];
            
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
    public function listByUser( Request $request )
    {
        try {

            $user = auth('api')->user();
            $result = $this->postsService->listByUser( $user );

            $response = [ 'status' => 'success', 'data' => PostResource::collection($result) ];
            
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response );
    
    }

    
    /**
     * list active posts
     * 
     * @return  json
     */
    public function listActive( Request $request )
    {
        try {

            $dataFilter = $request->all();
            $dataFilter['status'] = 'A';
            $result = $this->postsService->list( $dataFilter, ['updated_at'], 'desc' );

            $response = [ 'status' => 'success', 'data' => PostResource::collection($result) ];
            
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
            $result = $this->postsService->get( $dataFilter );
    
            $response = [ 'status' => 'success', 'data' => new PostResource($result) ];
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

            $result = $this->postsService->get( ['id' => $id] );
            $response = [ 'status' => 'success', 'data' => new PostResource($result) ];

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

            DB::beginTransaction();

            $validData = $request->validate([
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'files' => 'nullable|array',
                'files.*' => 'required|image',
                'classes' => 'nullable|array',
                'classes.*' => 'required|numeric|exists:classes,id',
            ]);
            $validData['status'] = 'A';
        
            $created = $this->postsService->create( $validData );

            if( !empty($validData['classes']) ){
                foreach($validData['classes'] as $idClass){
                    $created->classes()->attach( $idClass );
                }
            }

            if( !empty($validData['files']) ){
                foreach( $validData['files'] as $file ){
                    $this->postsService->uploadFile($created, $file);
                }
            }

            $response = [ 'status' => 'success', 'data' => new PostResource($created) ];

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
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'status' => 'nullable|string',
                'files' => 'nullable|array',
                'files.*' => 'required|image',
                'classes' => 'nullable|array',
                'classes.*' => 'required|numeric|exists:classes,id',
            ]);

            $updated = $this->postsService->updateById( $id, $validData);

            if( !empty($validData['files']) ){
                foreach( $validData['files'] as $file ){
                    $this->postsService->uploadFile($updated, $file);
                }
            }

            $updated->classes()->detach();
            if( !empty($validData['classes']) ){
                foreach($validData['classes'] as $idClass){
                    $updated->classes()->attach( $idClass );
                }
            }

            $response = [ 'status' => 'success', 'data' => new PostResource($updated) ];

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

            $deleted = $this->postsService->deleteById( $id );
            $response = [ 'status' => 'success', 'data' => ($deleted) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }

    /**
     * delete as image
     * 
     * @return  json
     */
    public function removeImage( $id ){

        try {

            $deleted = $this->postsService->deleteImage( $id );
            $response = [ 'status' => 'success', 'data' => new PostResource($deleted) ];

        } catch ( ValidationException $e ){
            
            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }

        return response()->json( $response );
    }
}