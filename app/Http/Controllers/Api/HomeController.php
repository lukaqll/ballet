<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getHomeData()
    {
        try {

            $openContracts = $this->contractsService->list(['status' => 'running']);
            $usersRegPendent = $this->usersService->list(['status' => 'MP']);
            $studentsRegPendent = $this->studentsService->list(['status' => 'MP']);
            $studentsContPendent = $this->studentsService->list(['status' => 'CP']);
            $birthdays = $this->studentsService->getbirthdays();

            $result = [
                'usersRegPendent' => count($usersRegPendent),
                'openContracts' => count($openContracts),
                'studentsRegPendent' => count($studentsRegPendent),
                'studentsContPendent' => count($studentsContPendent),
                'birthdays' => $birthdays,
            ];

            $response = [ 'status' => 'success', 'data' => $result ];


        } catch ( ValidationException $e ){

            $response = [ 'status' => 'error', 'message' => $e->errors() ];
        }
        return response()->json( $response ); 
    }
}