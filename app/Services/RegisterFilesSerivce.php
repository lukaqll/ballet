<?php 
 namespace App\Services;

use App\Models\RegisterFile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RegisterFilesSerivce extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new RegisterFile;       
    }

    /**
     * upload a file
     */
    public function upload( User $user, $file, $type='' ){

        if( empty($file) )
            return false;
        
        // upload
        $fileName = Storage::disk('public')->put('/register_files', $file);
        
        if( $fileName ){
            return $this->create([
                'id_user' => $user->id,
                'name' => $fileName,
                'type' => $type
            ]);
        }

        return false;
    }

    /**
     * register upload
     */
    public function registerUpload( User $user, $data ){
        foreach( $data as $type => $file ){

            $this->upload( $user, $file, $type);
        }
    }
}