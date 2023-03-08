<?php 
 namespace App\Services;

use App\Models\PaymentMethod;

class PaymentMethodService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new PaymentMethod;       
    }

}