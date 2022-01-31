<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'classes';

    protected $fillable = [
        'id',
        'id_unit',
        'name',
        'value',
        'team'
    ];

    public $timestamps = false;

    public function unit(){
        return $this->hasOne( Unit::class, 'id', 'id_unit' );
    }

    public function times(){
        return $this->hasMany( ClassTime::class, 'id_class', 'id')
                    ->orderBy('weekday');
    }

    public function students(){
        return $this->hasMany(StudentClass::class, 'id_class', 'id' )
                    ->where('approved_at', '!=', null);
    }

    public function contracts(){
        return $this->hasMany(Contract::class, 'id_class', 'id');
    }
}
