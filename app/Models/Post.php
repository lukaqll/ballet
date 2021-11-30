<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'posts';

    protected $fillable = [
        'id',
        'title',
        'description',
        'status',
        'created_at',
        'updated_at'
    ];
}
