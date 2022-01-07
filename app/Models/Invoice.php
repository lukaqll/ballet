<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'invoices';

    protected $fillable = [
        'id',
        'id_user',
        'value',
        'status',
        'reference',
        'expires_at',
        'created_at',
        'updated_at'
    ];

    public function user(){
        return $this->hasOne( User::class, 'id', 'id_user' );
    }

    public function getStatusText(){
        $status = [
            'A' => 'Aberto',
            'P' => 'Pago',
            'C' => 'Cancelado'
        ];
        return $status[$this->status];
    }

    public function getIsExpired(){
        return (date($this->expires_at) < date('Y-m-d H:i:s')) && $this->status == 'A';
    }
}