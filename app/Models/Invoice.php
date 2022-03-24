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
        'updated_at',
        'fee',
        'paid_at',
        'manual'
    ];

    public function user(){
        return $this->hasOne( User::class, 'id', 'id_user' );
    }

    public function getStatusText(){
        $status = [
            'A' => 'Aberto',
            'P' => 'Pago',
            'C' => 'Cancelado',
        ];
        return $status[$this->status];
    }

    public function getIsExpired(){
        return (date($this->expires_at) < date('Y-m-d H:i:s')) && $this->status == 'A';
    }

    public function openPayment(){
        return $this->hasOne(InvoicePayment::class, 'id_invoice', 'id')
                    ->where('invoice_payment.status', 'pending');
    }
}