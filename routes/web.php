<?php

use App\Http\Controllers\Api\DocumentsController;
use App\Http\Controllers\Api\InvoiceController;
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

Route::get('/php-test', function(){

});

Route::get('/contracts/view/{id}', [DocumentsController::class, 'viewDocument']);
Route::get('/contracts/sign/{id}', [DocumentsController::class, 'signDocument']);

Route::get('/invoice-payment/get/{id}', [InvoiceController::class, 'getInvoicePayment']);



Route::get('/{any}', function () {
    return view('base');
})->where('any', '.*');
