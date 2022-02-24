<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterFile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'register_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'id_user',
        'type',
        'name',
        'created_at',
        'updated_at',
    ];

    public function getExtention(){

        $extArray = explode('.', $this->name);
        if( empty($extArray) ){
            return null;
        }

        return end($extArray);
    }
}
