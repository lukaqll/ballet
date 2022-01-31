<?php 
 namespace App\Services;

use App\Models\ClassModel;
use App\Models\ClassTime;
use App\Models\Contract;
use App\Models\StudentClass;
use Illuminate\Validation\ValidationException;

class ClassesService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new ClassModel;       
    }

    public function delete(int $id){

        $class = $this->find($id);

        if(empty($class))
            throw ValidationException::withMessages(['Falha ao buscar aula']);

        $studentClasses = StudentClass::where('id_class', $class->id)->get();
        if(!empty($studentClasses) && count($studentClasses) > 0)
            throw ValidationException::withMessages(['Existem alunos vinculados à esta aula']);

        ClassTime::where('id_class', $class->id)->delete();
        Contract::where('id_class', $class->id)->delete();
        return ClassModel::find($id)->delete();
        
    }
}