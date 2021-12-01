<?php 
 namespace App\Services;

use App\Models\InvoicePayment;

class InvoicePaymentsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new InvoicePayment;       
    }

}