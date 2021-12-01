<?php 
 namespace App\Services;

use App\Models\Unit;

class UnitsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Unit;       
    }

}