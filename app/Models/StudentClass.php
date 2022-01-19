<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'student_classes';

    protected $fillable = [
        'id',
        'id_student',
        'id_class',
        'approved_at'
    ];

    public $timestamps = false;

    public function class(){
        return $this->hasOne(ClassModel::class, 'id', 'id_class');
    }

    public function contract(){
        return $this->hasOne(Contract::class, 'id_class', 'id_class')
                    ->where('contracts.id_student', $this->id_student);
    }

    public function notCanceledContract(){
        return $this->hasOne(Contract::class, 'id_class', 'id_class')
                    ->where('contracts.id_student', $this->id_student)
                    ->where('contracts.status', '!=', 'canceled');
    }

    public function openContract(){
        return $this->hasOne(Contract::class, 'id_class', 'id_class')
                    ->where('contracts.id_student', $this->id_student)
                    ->where('contracts.status', 'running');
    }
}
