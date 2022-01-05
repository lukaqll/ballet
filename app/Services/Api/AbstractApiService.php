<?php 
 namespace App\Services\Api;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractApiService
{
    private $client;
    protected $baseUrl;
    protected $basePath;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);
    }

    /**
     * send a request
     */
    public function request( string $type = 'get', string $url, array $data = [] ){
        
        try {

            $response = $this->client->request( $type, $this->basePath.$url, $data);
            return $this->respose( $response->getStatusCode(), json_decode($response->getBody()) );

        } catch( ClientException $e ){
            return $this->respose( $e->getCode(), json_decode($e->getResponse()->getBody())->errors );
        }
    }

    /**
     * format response
     */
    public function respose( $code=200, $data ){
        
        switch( $code ){
            case 200: case 201: case 202:
                return ['status' => 'success', 'data' => $data];

            default:
                return ['status' => 'error', 'message' => $data];
        }
    }
}