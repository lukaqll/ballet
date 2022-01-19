<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'contracts';

    protected $fillable = [
        'id',
        'id_student',
        'id_class',
        'key',
        'path',
        'status',
        'created_at',
        'updated_at'
    ];

    public function student(){
        return $this->hasOne(Student::class, 'id', 'id_student');
    }

    public function class(){
        return $this->hasOne(ClassModel::class, 'id', 'id_class');
    }

    public function studentClass(){
        return $this->hasOne(StudentClass::class, 'id_class', 'id_class')
                    ->where('student_classes.id_student', $this->id_student);
    }

    public function getStatusText(){
        $status = [
            'running' => 'Aberto',
            'closed' => 'Finalizado',
            'canceled' => 'Cancelado',
        ];
        return $status[$this->status];
    }
}
