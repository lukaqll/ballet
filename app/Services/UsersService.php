<?php 
 namespace App\Services;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\PasswordRecovery;
use App\Models\RegisterFile;
use App\Models\Student;
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
        if(!empty($data['picture'])){
            $this->uploadPicture($user, $data['picture']);
        }

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

        if( empty($student) )
            throw ValidationException::withMessages(['Nenhum aluno pendente de matricula encontrado']);
                
        // status update
        $user->update(['status' => 'A']);
        $student->update(['status' => 'CP']);

        // add as signer
        $clicksignService = new ClicksignService;
        if( empty($user->signer_key) ){
            $clicksignService->createSignatory($user);
        }

        // approve classes
        $studentClassModel = new StudentClass;
        $documentService = new DocumentsService;

        $studentClasses = $studentClassModel->where('id_student', $student->id)->get();

        foreach( $studentClasses as $studentClass ){
            if( empty($studentClass->notCanceledContract) ){
                $documentService->generateContract( $student, $studentClass->class );
            }
        }
        
        return $user;
    }

    public function verifyUserName(string $name){

        if( empty($name))
            throw ValidationException::withMessages(['Informe o nome']);
            
        $nameParts = explode(' ', $name);

        if( empty($nameParts) || count($nameParts) <= 1 )
            throw ValidationException::withMessages(['Informe o nome completo']);

    }

    public function verifyUserStatus(User $user){

        $openInvoices = $user->openInvoices;

        // mais de uma fatura aberta
        if( count($openInvoices) > 1 ){

            foreach( $openInvoices as $openInvoice ){
                if( $openInvoice->getIsExpired() ){
                    $user->update(['status' => 'P']);
                    continue;
                }
            }
        } 

        // uma fatura aberta
        else if ( count($openInvoices) == 1 ){
            $expiredInvoice = $openInvoices[0];
            $expirationDateLimit = date('Y-m-d H:i:s', strtotime('+5 day', strtotime(date('Y-m-d H:i:s')) ));

            if( $expiredInvoice->expres_at > $expirationDateLimit ){
                $user->update(['status' => 'P']);
            } else {
                $user->update(['status' => 'A']);
            }
        } 
         // nenhuma fatura aberta
        else {
            $user->update(['status' => 'A']);
        }

    }


    public function delete(int $id){
        
        $user = $this->find($id);

        if(empty($user))
            throw ValidationException::withMessages(['Falha ao buscar usuário']);

        $students = Student::where('id_user', $user->id)->get();

        // delete invoices
        $invoices = Invoice::where('id_user', $user->id)->get();
        foreach($invoices as $invoice){
            InvoicePayment::where('id_invoice', $invoice->id)->delete();
            Invoice::find($invoice->id)->delete();
        }

        // delete student
        foreach($students as $student){

            StudentClass::where('id_student', $student->id)->delete();
            Contract::where('id_student', $student->id)->delete();
            Student::find($student->id)->delete();
        }

        PasswordRecovery::where('id_user', $user->id)->delete();
        RegisterFile::where('id_user', $user->id)->delete();
        return User::find($user->id)->delete();

    }
}