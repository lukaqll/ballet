<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostSrc extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'post_src';

    protected $fillable = [
        'id',
        'id_post',
        'type',
        'src'
    ];

    public $timestamps = false;

    public function post(){
        return $this->hasOne( Post::class, 'id', 'id_post' );
    }
}
