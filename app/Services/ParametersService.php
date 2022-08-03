<?php 
 namespace App\Services;

use App\Models\Parameter;
use App\Models\User;
use App\Services\Api\ClicksignService;
use Illuminate\Validation\ValidationException;
use stdClass;

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

    
    public function saveSigner( $data ){

        $wasUpdated = false;

        $clickSignService = new ClicksignService;

        foreach( $data as $att => $value ){

            $attibuteExists = $this->get(['operation' => 'signer', 'attribute' => $value]);

            if( !empty($attibuteExists) ){

                if( $attibuteExists->value != $value ){
                    $attibuteExists->update(['value' => $value]);
                    $wasUpdated = true;
                }

            } else {
                $this->create([
                    'operation' => 'signer',
                    'attribute' => $att,
                    'value' => $value
                ]);
            }
        }

        $sign = $this->getSigner(true);

        // foi atualizado
        if( $wasUpdated ){
            $clickSignService->deleteSignatory( $sign->sign_key );
            $this->get(['operation' => 'signer', 'attribute' => 'signer_key'])->delete();
        }

        $created = $clickSignService->createSignatory( $sign );

        if( empty($created->signer_key) ){
            throw ValidationException::withMessages(['Falha ao adicionar signatário']);
        }
        
        $this->create([
            'operation' => 'signer',
            'attribute' => 'signer_key',
            'value' => $created->signer_key
        ]);

    }


    public function getSigner( $asObject = false ){

        $allData = $this->list(['operation' => 'signer']);

        if( $asObject ){
            $signerData = new stdClass;
            $signerData->is_whatsapp = false;
        } else {
            $signerData = [];
        }


        foreach( $allData as $param ){

            if( $asObject ){
                $signerData->{$param->attribute} = $param->value;
            } else {
                $signerData[ $param->attribute ] = $param->value;
            }
        }

        return $signerData;
    }

    public function saveConfig($attr, $value){

        $config = $this->get(['operation' => 'general-config', 'attribute' => $attr]);

        if(!empty($config)){
            $config->update(['value' => $value]);
        } else {
            $config = $this->create([
                'operation' => 'general-config', 
                'attribute' => $attr,
                'value' => $value
            ]);
        }

        return $config;
    }
}