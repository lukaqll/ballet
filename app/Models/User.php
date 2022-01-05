<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $primaryKey = 'id';
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'cpf',
        'status',
        'is_admin',
        'picture',
        'signer_key',
        'uf',
        'city',
        'district',
        'street',
        'address_number',
        'address_complement',
        'instagram',
        'rg',
        'orgao_exp',
        'profession',
        'birthdate',
        'cep',
        'know_by',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function students(){
        return $this->hasMany( Student::class, 'id_user', 'id' );
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function pendentStudent(){
        return $this->hasOne( Student::class, 'id_user', 'id' )
                    ->where('status', 'MP');
    }

    public function RegisterFiles(){
        return $this->hasMany( RegisterFile::class, 'id_user', 'id' );
    }

    public function getStatusArray(){

        return [
            'A' => 'Ativo',
            'I' => 'Inativo',
            'MP' => 'Matrícula Pendente',
        ];
    }

    public function getStatusText(){
        return $this->getStatusArray()[$this->status];
    }
}