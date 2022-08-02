<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAdd extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'invoice_adds';

    protected $fillable = [
        'id',
        'id_user',
        'id_invoice',
        'description',
        'value',
        'month',
        'created_at',
        'updated_at'
    ];
}
