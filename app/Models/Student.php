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
        'health_problem',
        'food_restriction',
        'in_school',
        'school_time',
        'created_at',
        'updated_at',
        
    ];

    public function user(){
        return $this->hasOne( User::class, 'id', 'id_user' );
    }

    public function classes(){
        return $this->belongsToMany(ClassModel::class, StudentClass::class, 'id_student', 'id_class', 'id', 'id');
    }

    public function approvedClasses(){
        return $this->belongsToMany(ClassModel::class, StudentClass::class, 'id_student', 'id_class', 'id', 'id')
                    ->where('approved_at', '!=', null);
    }

    public function approvedStudentClasses(){
        return $this->hasMany(StudentClass::class, 'id_student', 'id')
                    ->where('approved_at', '!=', null);
    }

    public function studentClasses(){
        return $this->hasMany(StudentClass::class, 'id_student', 'id');
    }

    public function openContract(){
        return $this->hasOne(Contract::class, 'id_student', 'id')
                    ->where('status', 'running');
    }

    public function openContracts(){
        return $this->hasMany(Contract::class, 'id_student', 'id')
                    ->where('status', 'running');
    }

    public function closedContract(){
        return $this->hasMany(Contract::class, 'id_student', 'id')
                    ->where('status', 'closed');
    }

    public function contracts(){
        return $this->hasMany(Contract::class, 'id_student', 'id');
    }

    public function getStatusArray(){

        return [
            'A' => 'Ativo',
            'I' => 'Inativo',
            'MP' => 'Matrícula Pendente',
            'CP' => 'Contrato Pendente'
        ];
    }

    public function getStatusText(){
        return $this->getStatusArray()[$this->status];
    }
}
