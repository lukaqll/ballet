<?php 
 namespace App\Services;

use App\Models\StudentClass;
use App\Models\User;
use App\Services\Api\ClicksignService;
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

    /**
     * create a new user
     * 
     * @param array $data
     * @return User $user
     */
    public function createUser( array $data ){

        // send password email
        if( !empty($data['send_password_mail']) ){

            // rand password
            $data['password'] = strtotime(date('YmdHis')).$data['name'];

        } else {
            
            if( empty($data['password']) )
                throw ValidationException::withMessages(['Informe uma senha']);

            if( strlen($data['password']) < 6 )
                throw ValidationException::withMessages(['Senha muito curta']);

            if( $data['password'] != $data['password_confirmation'] )
                throw ValidationException::withMessages(['As senhas não conferem']);
        }
         
        $data['password'] = bcrypt($data['password']);
            
        $user = $this->model->create($data);
        $this->uploadPicture($user, $data['picture']);

        return $user;
    }

    /**
     * public create a new user
     * 
     * @param array $data
     * @return User $user
     */
    public function publicCreateUser( array $data ){

        // send password email
        $sendMail = true;

        if( $sendMail ){

            // rand password
            $data['password'] = strtotime(date('YmdHis')).$data['name'];

        } else {
            
            if( empty($data['password']) )
                throw ValidationException::withMessages(['Informe uma senha']);

            if( strlen($data['password']) < 6 )
                throw ValidationException::withMessages(['Senha muito curta']);

            if( $data['password'] != $data['password_confirmation'] )
                throw ValidationException::withMessages(['As senhas não conferem']);
        }
         
        $data['password'] = bcrypt($data['password']);
            
        $user = $this->model->create($data);

        if( !empty($data['picture']) ){
            $this->uploadPicture($user, $data['picture']);
        }

        return $user;
    }

    /**
     * list clients users
     */
    public function listClients( $filters = [] ){
    
        return $this->list($filters)->where('is_admin', 0);
    }

    /**
     * upload a user picture
     */
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

    public function approveRegistration( int $id ){

        $user = $this->model->find($id);

        if( $user->status != 'MP' )
            throw ValidationException::withMessages(['este usuário não está pendente de matrícula']);

        $student = $user->pendentStudent;

        if( !empty($student) ){

            if(  $student->status != 'MP' )
                throw ValidationException::withMessages(['este aluno não está pendente de matrícula']);
    
            // approve class
            $studentClass = new StudentClass;
            $studentClass->where('id_student', $student->id)
                         ->update(['approved_at' => date('Y-m-d H:i:s')]);

            // contrato pendente
            $student->update(['status' => 'CP']);
        }

        // ativo
        $user->update(['status' => 'A']);

        

        $clicksignService = new ClicksignService;
        if( empty($user->signer_key) ){
            $clicksignService->createSignatory($user);
        }

        $documentService = new DocumentsService;
        if(!empty($student) && empty($student->openContract) ){
            $documentService->generateContract( $student );
        }

        return $user;
    }
}