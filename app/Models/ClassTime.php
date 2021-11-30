<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassTime extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'class_times';

    protected $fillable = [
        'id',
        'id_class',
        'weekday',
        'start_at',
        'end_at'
    ];

    public $timestamps = false;

    public function class(){
        return $this->hasOne( ClassModel::class, 'id', 'id_class' );
    }
}
