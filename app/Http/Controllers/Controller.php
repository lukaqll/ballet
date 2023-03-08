<?php

namespace App\Http\Controllers;

use App\Services\Api\ClicksignService;
use App\Services\Api\MercadoPagoService;
use App\Services\ClassesService;
use App\Services\ClassTimesService;
use App\Services\ContractsService;
use App\Services\DocumentsService;
use App\Services\InvoicePaymentsService;
use App\Services\InvoicesService;
use App\Services\ParametersService;
use App\Services\PasswordRecoveryService;
use App\Services\PostSrcsService;
use App\Services\PostsService;
use App\Services\RegisterFilesSerivce;
use App\Services\ReportsService;
use App\Services\SalesService;
use App\Services\StudentClassesService;
use App\Services\StudentsService;
use App\Services\UnitsService;
use App\Services\UsersService;
use App\Services\InvoiceAddService;
use App\Services\PaymentMethodService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $classesService;
    protected $classTimesService;
    protected $invoicePaymentsService;
    protected $invoicesService;
    protected $passwordRecoveryService;
    protected $postSrcsService;
    protected $postsService;
    protected $studentsService;
    protected $unitsService;
    protected $usersService;
    protected $parametersService;
    protected $clicksignService;
    protected $documentsService;
    protected $contractsService;
    protected $registerFilesSerivce;
    protected $repostsService;
    protected $studentClassesService;
    protected $mercadoPagoService;
    protected $salesService;
    protected $invoiceAddService;
    protected $paymentMethodService;
    
    public function __construct()
    {
        $this->classesService = new ClassesService;
        $this->classTimesService = new ClassTimesService;
        $this->invoicePaymentsService = new InvoicePaymentsService;
        $this->invoicesService = new InvoicesService;
        $this->passwordRecoveryService = new PasswordRecoveryService;
        $this->postSrcsService = new PostSrcsService;
        $this->postsService = new PostsService;
        $this->studentsService = new StudentsService;
        $this->unitsService = new UnitsService;
        $this->usersService = new UsersService;
        $this->parametersService = new ParametersService;
        $this->clicksignService = new ClicksignService;
        $this->documentsService = new DocumentsService;
        $this->contractsService = new ContractsService;
        $this->registerFilesSerivce = new RegisterFilesSerivce;
        $this->repostsService = new ReportsService;
        $this->studentClassesService = new StudentClassesService;
        $this->mercadoPagoService = new MercadoPagoService;
        $this->salesService = new SalesService;
        $this->invoiceAddService = new InvoiceAddService;
        $this->paymentMethodService = new PaymentMethodService;
    }

    protected function unMaskMoney( string $str ){
        if( empty($str) ) return 0;

        $str = str_replace('.', '', $str);
        $str = str_replace(',', '.', $str);
        $str = str_replace('R$', '', $str);
        $str = str_replace(' ', '', $str);
        return floatval($str);
    }
}