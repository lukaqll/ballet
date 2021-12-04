<?php 
 namespace App\Services;

use App\Models\Student;
use App\Models\StudentClass;

class StudentClassesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new StudentClass;       
    }

    public function deleteAllByStudent(Student $student){
        return $this->model->where('id_student', $student->id)->delete();
    }

}