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
}
