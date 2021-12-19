<?php

namespace App\Http\Controllers;

use App\Services\ClassesService;
use App\Services\ClassTimesService;
use App\Services\InvoicePaymentsService;
use App\Services\InvoicesService;
use App\Services\ParametersService;
use App\Services\PasswordRecoveryService;
use App\Services\PostSrcsService;
use App\Services\PostsService;
use App\Services\StudentsService;
use App\Services\UnitsService;
use App\Services\UsersService;
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