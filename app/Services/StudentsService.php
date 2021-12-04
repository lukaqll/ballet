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
        
        if( $data['id_class'] ){
            $student->classes()->attach( $data['id_class'] );
        }

        $this->uploadPicture($student, $data['picture']);

        return $student;
    }

    public function updateStudentClasses( Student $student, array $classes ){

        $studentClassService = new StudentClassesService;
        $studentClassService->deleteAllByStudent($student);
        foreach( $classes as $idClass ){
            $student->classes()->attach( $idClass );
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
}