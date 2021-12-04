<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'students';

    protected $fillable = [
        'id',
        'id_user',
        'name',
        'nick_name',
        'birthdate',
        'status',
        'picture',
        'created_at',
        'updated_at'
    ];

    public function user(){
        return $this->hasOne( User::class, 'id', 'id_user' );
    }

    public function classes(){
        return $this->belongsToMany(ClassModel::class, StudentClass::class, 'id_student', 'id_class', 'id', 'id');
    }
}
