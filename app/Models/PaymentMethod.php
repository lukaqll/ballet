<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model {
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'payment_methods';

    protected $fillable = [
        'name',
        'deleted'
    ];

    public $timestamps = false;
}
