<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'parameters';

    protected $fillable = [
        'id',
        'operation',
        'attribute',
        'value',
    ];

    public $timestamps = false;
}
