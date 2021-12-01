<?php 
 namespace App\Services;

use App\Models\PasswordRecovery;

class PasswordRecoveryService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PasswordRecovery;       
    }

}