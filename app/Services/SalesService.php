<?php 
 namespace App\Services;

use App\Models\Sale;

class SalesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Sale;       
    }

}