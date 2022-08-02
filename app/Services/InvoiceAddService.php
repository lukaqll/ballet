<?php 
 namespace App\Services;

use App\Models\InvoiceAdd;

class InvoiceAddService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new InvoiceAdd;       
    }

}