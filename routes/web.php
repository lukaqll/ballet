<?php

use App\Services\PasswordRecoveryService;
use App\Services\UsersService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test-mail', function(){

    $usersService = new UsersService;
    $user = $usersService->find(9);

    $passwordRevoveryService = new PasswordRecoveryService;
    return $passwordRevoveryService->sendMail($user, true);
});

Route::get('/{any}', function () {
    return view('base');
})->where('any', '.*');
