<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'invoice_payment';

    protected $fillable = [
        'id',
        'id_invoice',
        'type',
        'value',
        'reference',
        'status',
        'status_detail',
        'created_at',
        'updated_at'
    ];

    public function invoice(){
        return $this->hasOne( Invoice::class, 'id', 'id_invoice' );
    }
}