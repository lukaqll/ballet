<?php

namespace App\Http\Controllers;

use App\Services\UsersService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $usersService;
    
    public function __construct()
    {
        $this->usersService = new UsersService;    
    }
}
