<?php
namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersService extends AbstractService
{
    protected $model;

    public function __construct()
    {
        $this->model = new User;
    }
}