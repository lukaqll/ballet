<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'units';

    protected $fillable = [
        'id',
        'name',
        'due_day'
    ];

    public $timestamps = false;

    public function classes(){
        return $this->hasMany(ClassModel::class, 'id_unit', 'id');
    }
}
