<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordRecovery extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'password_recovery';

    protected $fillable = [
        'id',
        'id_user',
        'token',
        'status',
        'picture',
        'is_first',
        'created_at',
        'updated_at'
    ];
}
