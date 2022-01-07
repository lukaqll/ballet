<?php 
 namespace App\Services;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class StudentsService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Student;       
    }

    public function createStudent( array $data ){
        
        $student =  $this->create($data);
        
        if( !empty($data['id_class']) ){
            $student->classes()->attach( $data['id_class'] );
        }

        if( !empty($data['picture']) ){
            $this->uploadPicture($student, $data['picture']);
        }

        return $student;
    }

    public function updateStudentClasses( Student $student, array $classes ){

        $studentClassService = new StudentClassesService;
        $studentClasses = $studentClassService->list(['id_student' => $student->id]);

        foreach( $classes as $idClass ){
            
            $isStudentClassExists = $studentClassService->get([
                'id_student' => $student->id, 
                'id_class' => $idClass
            ]);

            if( empty( $isStudentClassExists ) ){
                $studentClassService->create([
                    'id_student' => $student->id, 
                    'id_class' => $idClass,
                    'approved_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        foreach( $studentClasses as $studentClass ){
            if( !in_array($studentClass->id_class, $classes) ){
                $studentClass->delete();
            }
        }

        return true;
    }

    public function uploadPicture( Student $student, $file ){

        if( empty($file) )
            return false;

        $oldFile = $student->picture;
        
        // upload
        $fileName = Storage::disk('public')->put('/students/pictures', $file);
        
        if( $fileName ){
            
            $student->update(['picture' => $fileName]);

            // remove old filte
            if( !empty($oldFile) )
                Storage::disk('public')->delete($oldFile);
        }

        return $student;
    }

    public function getbirthdays(){
        
        return $this->model->whereRaw("date_format(birthdate, '%m') = date_format(curdate(), '%m')")
                            ->orderBy('birthdate')->get();
    }
}