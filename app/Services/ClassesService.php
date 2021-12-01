<?php 
 namespace App\Services;

use App\Models\ClassModel;

class ClassesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new ClassModel;       
    }

}