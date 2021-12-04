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
        'id_class'
    ];

    public $timestamps = false;
}
