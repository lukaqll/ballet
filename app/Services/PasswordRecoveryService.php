<?php 
 namespace App\Services;

use App\Mail\PasswordRecovery as MailPasswordRecovery;
use App\Models\PasswordRecovery;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PasswordRecoveryService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PasswordRecovery;       
    }


    public function sendMail( User $user, bool $isFirst = false ){

        if (env('APP_ENV') != 'prod') return true;

        // cancel others token
        $this->model->where('id_user', $user->id)
                    ->where('status', 'A')
                    ->update(['status' => 'B']);

        do {

            $token = md5(strtotime(date('YmdHis')).$user->id.'passrec'.strtotime('ss'));

            $isTokenExists = $this->model->where(['token' => $token])->first();

        } while ( !empty($isTokenExists) || !empty($isTokenExists->id) );

        $data = [
            'id_user' => $user->id,
            'status' => 'A',
            'is_first' => $isFirst ? 1 : 0,
            'token' => $token
        ];

        $created = $this->model->create($data);

        return Mail::send( new MailPasswordRecovery( $user, $created ) );
    }

    public function resetPassword( array $data ){

        $token = $data['token'];
        if( empty($token) )
            throw ValidationException::withMessages(['Token não identificado']);

            
        $isTokenExists = $this->model->where(['token' => $token])->first();
        if( empty($isTokenExists) )
            throw ValidationException::withMessages(['Token inválido']);

        if( $isTokenExists->status != 'A' )
            throw ValidationException::withMessages(['Token já utilizado']);

        $timeDiff = strtotime( date('Y-m-d H:i:s') ) - strtotime( $isTokenExists->created_at );

        if( $isTokenExists->is_first == 0 && $timeDiff > 600 ) {
            throw ValidationException::withMessages(['Token não expirado']);
        }
        
        if( empty($data['password']) )
            throw ValidationException::withMessages(['informe uma senha']);

        if( $data['password'] != $data['password_confirmation'] )
            throw ValidationException::withMessages(['as senhas não conferem']);

        $usersService = new UsersService;
        $updated = $usersService->updateById( $isTokenExists->id_user, ['password' => bcrypt($data['password'])] );

        $isTokenExists->update(['status' => 'B']);

        return $updated;
    }
}