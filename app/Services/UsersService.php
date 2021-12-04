<?php 
 namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UsersService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new User;       
    }

    public function createUser( array $data ){

                
        if( $data['password'] != $data['password_confirmation'] )
            throw ValidationException::withMessages(['As senhas não conferem']);

        $data['password'] = bcrypt($data['password']);

        $user = $this->create($data);
        $this->uploadPicture($user, $data['picture']);

        return $user;
    }

    public function listClients(){
        return $this->model->where('is_admin', 0)->get();
    }

    public function uploadPicture( User $user, $file ){

        if( empty($file) )
            return false;

        $oldFile = $user->picture;
        
        // upload
        $fileName = Storage::disk('public')->put('/users/pictures', $file);
        
        if( $fileName ){
            
            $user->update(['picture' => $fileName]);

            // remove old filte
            if( !empty($oldFile) )
                Storage::disk('public')->delete($oldFile);
        }

        return $user;
    }
}