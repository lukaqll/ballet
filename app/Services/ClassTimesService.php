<?php 
 namespace App\Services;

use App\Models\ClassTime;

class ClassTimesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new ClassTime;       
    }

}