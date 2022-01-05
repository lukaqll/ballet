<?php 
 namespace App\Services;

use App\Models\Post;
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

}