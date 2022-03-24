<?php 
 namespace App\Services;

use App\Models\Post;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class PostsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Post;       
    }

    /**
     * upload a file
     */
    public function uploadFile( Post $post, $file ){

        $postSrcService = new PostSrcsService;

        if( empty($file) )
            return false;
        
        // upload
        $fileName = Storage::disk('public')->put('/posts', $file);
        
        if( !empty($fileName) ){
            
            $postSrcService->create([
                'id_post' => $post->id,
                'type' => 'image',
                'src' => $fileName
            ]);
        }

        return $post;
    }

    /**
     * upload a file
     */
    public function deleteImage( int $idSrc ){

        $postSrcService = new PostSrcsService;
        $file = $postSrcService->find($idSrc);
        if( empty($file) )
            return false;
        
        $post = $file->post;
        
        // delete file
        Storage::disk('public')->delete($file->src);
        $file->delete();

        return $post;
    }

    public function listByUser( User $user ){

        $result = $this->model->join('post_classes AS pc', 'pc.id_post', 'posts.id' )
                              ->join('student_classes AS sc', 'sc.id_class', 'pc.id_class')
                              ->join('students AS st', function($join) use($user){
                                  $join->on('st.id', 'sc.id_student')
                                       ->where('st.id_user', $user->id);
                              })
                              ->where('posts.status', 'A')
                              ->groupBy('posts.id')
                              ->orderBy('posts.id', 'desc')
                              ->select('posts.*')
                              ->get();
        return $result;
    }
}