<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'table';

    protected $fillable = [
        'id',
        'id_example'
    ];

    // public $timestamps = false;

    // public function relationship(){
    //     return $this->hasOne( Example::class, 'id', 'id_example' );
    // }

}
