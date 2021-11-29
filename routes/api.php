<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     
// });

Route::group(['middleware' => ['admin']], function () {
    
    Route::get('/user', [AuthController::class, 'getUser'])->name('login.verify');

    Route::get('/users/list', [UserController::class, 'index']);
});

Route::post('/login', [AuthController::class, 'login']);