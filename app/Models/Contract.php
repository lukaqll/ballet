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
        'key',
        'path',
        'status',
        'created_at',
        'updated_at'
    ];

    public function student(){
        return $this->hasOne(Student::class, 'id', 'id_student');
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
