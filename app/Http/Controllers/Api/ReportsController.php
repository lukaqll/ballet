<?php 

 namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * get by key and value
     * 
     * @return  json
     */
    public function knowBy( Request $request ){

        try {

            $dataFilter = $request->all();
            $result = $this->repostsService->getKnowBy( );
    
            $response = [ 'status' => 'success', 'data' => ($result) ];
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

    /**
     * get by key and value
     * 
     * @return  json
     */
    public function revenue( Request $request ){

        try {

            $dataFilter = $request->all();
            $result = [];
            $result['by_month'] = $this->repostsService->getRevenueByMonth();
    
            $response = [ 'status' => 'success', 'data' => ($result) ];
        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }

}