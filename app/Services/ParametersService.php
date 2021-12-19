<?php 
 namespace App\Services;

use App\Models\Parameter;

class ParametersService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Parameter;       
    }


    public function getContract(){

        $contract = $this->model->where('operation', 'config')
                                ->where('attribute', 'contract')
                                ->first();

        if( empty($contract) || empty($contract->id) ){

            $contract = $this->create([
                'operation' => 'config',
                'attribute' => 'contract',
                'value' => '#contrato#'
            ]);
        }

        return $contract;
    }

    
}