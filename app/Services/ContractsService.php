<?php 
 namespace App\Services;

use App\Models\Contract;

class ContractsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Contract;       
    }

}