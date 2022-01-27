<?php 
namespace App\Services;

use App\Models\ClassModel;
use App\Models\Contract;
use App\Models\Student;
use App\Services\Api\ClicksignService;
use Illuminate\Support\Facades\Storage;
use PDF;
use WGenial\NumeroPorExtenso\NumeroPorExtenso;
class DocumentsService
{
    protected $model;
    public function __construct()
    {
        
    }

    public function generateContract( Student $student, ClassModel $class ){

        $pdf = $this->getPdfInstance( $student, $class );
        $base64 = 'data:application/pdf;base64,' . base64_encode($pdf);

        $clicksignService = new ClicksignService;
        $contract = $clicksignService->createDocument( $student, $base64, $class );

        return $contract;
    }

    public function getPdfInstance( Student $student, ClassModel $class ){
        
        $parametesService = new ParametersService;
        $extenso = new NumeroPorExtenso;

        $contentText = $parametesService->getContract()->value;

        $user = $student->user;

        // replace keys params
        // user
        $contentText = str_replace( ':nome_usuario', $user->name, $contentText);
        $contentText = str_replace( ':cpf_usuario', $user->cpf, $contentText);
        $contentText = str_replace( ':email_usuario', $user->email, $contentText);
        $contentText = str_replace( ':telefone_usuario', $user->phone, $contentText);
        $contentText = str_replace( ':desde_usuario', date('d/m/Y', strtotime($user->created_at)), $contentText);
        $contentText = str_replace( ':nascimento_usuario', date('d/m/Y', strtotime($user->birthdate)), $contentText);
        
        $contentText = str_replace( ':rg_usuario', $user->rg, $contentText);
        $contentText = str_replace( ':orgao_exp_usuario', $user->orgao_exp, $contentText);
        $contentText = str_replace( ':uf_orgao_ex_usuario', $user->uf_orgao_exp, $contentText);
        $contentText = str_replace( ':cep_usuario', $user->cep, $contentText);
        $contentText = str_replace( ':uf_usuario', $user->uf, $contentText);
        $contentText = str_replace( ':cidade_usuario', $user->city, $contentText);
        $contentText = str_replace( ':bairro_usuario', $user->district, $contentText);
        $contentText = str_replace( ':rua_usuario', $user->street, $contentText);
        $contentText = str_replace( ':numero_endereco_usuario', $user->address_number, $contentText);
        $contentText = str_replace( ':complemento_usuario', $user->address_complement, $contentText);
        $contentText = str_replace( ':profissao_usuario', $user->profession, $contentText);
        $contentText = str_replace( ':instagram_usuario', $user->instagram, $contentText);

        // student
        $contentText = str_replace( ':nome_aluno', $student->name, $contentText);
        $contentText = str_replace( ':nascimento_aluno', date('d/m/Y', strtotime($student->birthdate)), $contentText);
        $contentText = str_replace( ':desde_aluno', date('d/m/Y', strtotime($student->created_at)), $contentText);

        // general
        $contentText = str_replace( ':data_hora', date('d/m/Y H:i'), $contentText);
        $contentText = str_replace( ':data', date('d/m/Y'), $contentText);

        // class
        $contentText = str_replace( ':nome_aula', $class->name, $contentText);
        $contentText = str_replace( ':nome_unidade', $class->unit->name, $contentText);
        $contentText = str_replace( ':valor_aula_real', number_format($class->value, 2, ',', ''), $contentText);

        if( floatval($class->value) > 0 ){
            $contentText = str_replace( ':valor_aula_extenso', $extenso->converter($class->value), $contentText);
        } else {
            $contentText = str_replace( ':valor_aula_extenso', 'zero reais', $contentText);
        }


        $pdf = PDF::loadHTML($contentText);

        return $pdf->stream();
    }

    
}