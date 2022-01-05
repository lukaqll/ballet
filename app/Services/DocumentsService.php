<?php 
namespace App\Services;

use App\Models\Student;
use App\Services\Api\ClicksignService;
use Illuminate\Support\Facades\Storage;
use PDF;

class DocumentsService
{
    protected $model;
    public function __construct()
    {
        
    }

    public function generateContract( Student $student ){

        $pdf = $this->getPdfInstance($student);
        $base64 = 'data:application/pdf;base64,' . base64_encode($pdf);

        $clicksignService = new ClicksignService;
        $contract = $clicksignService->createDocument( $student, $base64 );

        return $contract;
    }

    public function getPdfInstance( Student $student ){
        $parametesService = new ParametersService;
        $contentText = $parametesService->getContract()->value;

        $user = $student->user;

        // replace keys params
        // user
        $contentText = str_replace( ':nome_usuario', $user->name, $contentText);
        $contentText = str_replace( ':cpf_usuario', $user->cpf, $contentText);
        $contentText = str_replace( ':email_usuario', $user->email, $contentText);
        $contentText = str_replace( ':telefone_usuario', $user->phone, $contentText);
        $contentText = str_replace( ':desde_usuario', $user->created_at, $contentText);
        
        // student
        $contentText = str_replace( ':nome_aluno', $student->name, $contentText);
        $contentText = str_replace( ':apelido_aluno', $student->nick_name, $contentText);
        $contentText = str_replace( ':nascimento_aluno', $student->birthdate, $contentText);
        $contentText = str_replace( ':desde_aluno', $student->created_at, $contentText);

        // general
        $contentText = str_replace( ':data', date('d/m/Y'), $contentText);
        $contentText = str_replace( ':data_hora', date('d/m/Y H:i'), $contentText);

        $pdf = PDF::loadHTML($contentText);

        return $pdf->stream();
    }
}