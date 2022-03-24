<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostClass extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'post_classes';

    protected $fillable = [
        'id',
        'id_post',
        'id_class'
    ];

    public $timestamps = false;
    
}
