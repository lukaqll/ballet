<?php

namespace App\Mail;

use App\Models\PasswordRecovery as ModelsPasswordRecovery;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordRecovery extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $passwordRecoveryToken;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, ModelsPasswordRecovery $passwordRecoveryToken)
    {
        $this->user = $user;
        $this->passwordRecoveryToken = $passwordRecoveryToken;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Recuperação de senha Arte e Dança Ballet');
        $this->to($this->user->email, $this->user->name);

        return $this->view('mail.passwordRecovery', [
            'user' => $this->user,
            'token' => $this->passwordRecoveryToken
        ]);
    }
}
