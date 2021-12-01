<?php 
 namespace App\Services;

use App\Models\Invoice;

class InvoicesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Invoice;       
    }

}