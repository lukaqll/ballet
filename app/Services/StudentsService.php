<?php 
 namespace App\Services;

use App\Models\Student;

class StudentsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Student;       
    }

}