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
    
        $findable = User::where('is_admin', 0);
        
        if( !empty($filters['status']) ){
            $findable = $findable->where('users.status', $filters['status']);
        }

        if( !empty($filters['id_unit']) ){

            $findable = $findable->join('students', 'students.id_user', 'users.id')
                                 ->join('student_classes', 'student_classes.id_student', 'students.id')
                                 ->join('classes', 'classes.id', 'student_classes.id_class')
                                 ->join('units', function($join) use($filters){
                                    $join->on('units.id', 'classes.id_unit')
                                         ->where('units.id', $filters['id_unit']);
                                 });
        }
        
        return $findable->where('users.deleted', '!=', 1)->select('users.*')->group('users.id')->get();
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
        if( !empty($openInvoices) && count($openInvoices) > 0 ){

            foreach( $openInvoices as $openInvoice ){
                if( $openInvoice->getIsExpired() ){
                    $user->update(['status' => 'P']);
                    continue;
                }
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
            
        $storage = Storage::disk('public');

        // delete invoices
        $invoices = Invoice::where('id_user', $user->id);
        foreach($invoices->get() as $invoice){
            InvoicePayment::where('id_invoice', $invoice->id)->delete();
        }
        $invoices->delete();
            
        // delete student
        $students = Student::where('id_user', $user->id);
        foreach($students->get() as $student){
            StudentClass::where('id_student', $student->id)->delete();
            Contract::where('id_student', $student->id)->delete();

            if ($storage->exists($student->picture))
                $storage->delete($student->picture);
        }
        $students->delete();

        PasswordRecovery::where('id_user', $user->id)->delete();
        $files = RegisterFile::where('id_user', $user->id);
        foreach ($files->get() as $file) {
            if ($storage->exists($file->name))
                $storage->delete($file->name);
        }
        $files->delete();

        return $user->delete();
    }

    public function softDelete($id){
        
        $user = $this->find($id);

        if(empty($user))
            throw ValidationException::withMessages(['Falha ao buscar usuário']);

        $invoicesService = new InvoicesService;
        $clicksignService = new ClicksignService;

        foreach($user->openInvoices as $invoice){
            try {
                $invoicesService->cancelInvoice($invoice->id);
            } catch (ValidationException $e) {}
        }

        foreach($user->students as $student){
            foreach ($student->openContracts as $contract) {
                try {
                    $clicksignService->cancelContract($contract);
                } catch (ValidationException $e) {}
            }
            $student->update(['deleted' => 1]);
        }

        PasswordRecovery::where('id_user', $user->id)->delete();

        return $user->update(['deleted' => 1]);
    }

    public function verifyUserStatusScript(User $user){

        $openInvoices = $user->openInvoices;

        echo "\n $user->name ( $user->status )";

        // pelo menos uma fatura aberta
        if( !empty($openInvoices) && count($openInvoices) > 0 ){

            echo "\n ".count($openInvoices)." faturas abertas";

            foreach( $openInvoices as $openInvoice ){
                
                if( $openInvoice->getIsExpired() ){
                    echo "\n atualizado para inadimplente";
                    $user->update(['status' => 'P']);
                    continue;
                }
            }
        } 

        // nenhuma fatura aberta
        else {
            echo "\n atualizado para ativo";
            $user->update(['status' => 'A']);
        }

        echo "\n ------------";

    }
}