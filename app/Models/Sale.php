<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'sales';

    protected $fillable = [
        'id',
        'id_student',
        'id_unit',
        'description',
        'color',
        'size',
        'payment_method',
        'paid_at',
        'status',
        'payment_status',
        'price',
        'paid_price',
        'created_at',
        'updated_at'
    ];

    public function student(){
        return $this->hasOne(Student::class, 'id', 'id_student');
    }

    public function unit(){
        return $this->hasOne(Unit::class, 'id', 'id_unit');
    }

}
