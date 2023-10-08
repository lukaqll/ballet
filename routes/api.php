<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ClassTimeController;
use App\Http\Controllers\Api\ClicksignHooksController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DocumentsController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\MercadoPagoHookController;
use App\Http\Controllers\Api\ParameterController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InvoiceAddController;
use App\Http\Controllers\Api\PaymentMethodController;
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

/**
 * public routes
 */
Route::get('/classes/list', [ClassController::class, 'list']);
Route::get('/classes/public-list', [ClassController::class, 'publicList']);
Route::get('/gallery', [HomeController::class, 'getGallery']);
Route::get('/units/list', [UnitController::class, 'list']);


// auth
Route::post('/register', [UserController::class, 'publicCreateWithStudent']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// password reset
Route::post('/password-reset/send-mail', [AuthController::class, 'sendMailPasswordReset']);
Route::post('/password-reset', [AuthController::class, 'passowrdReset']);

// hooks
Route::post('/clicksign/hook', [ClicksignHooksController::class, 'hooksCallback']);
Route::post('/mercadopago/hook', [MercadoPagoHookController::class, 'hooksCallback']);

Route::get('/user/commom', [AuthController::class, 'getUser']);

/**
 * client routes
 */
Route::group(['middleware' => ['client']], function () {

    Route::get('/get-client-home', [HomeController::class, 'getClientHomeData']);

    // user
    Route::get('/user/client', [AuthController::class, 'getUser']);
    Route::post('/users/upload-picture', [UserController::class, 'uploadPicture']);

    // students
    Route::get('/students/list-self', [StudentController::class, 'listBySelf']);
    Route::get('/students/get-self/{id}', [StudentController::class, 'getBySelf']);
    Route::put('/students/self-update/{id}', [StudentController::class, 'selfUpdate']);
    Route::post('/students/upload-picture/{id}', [StudentController::class, 'uploadPicture']);

    // contracts
    Route::get('/contracts/list-self', [DocumentsController::class, 'listSelf']);

    // invoices
    Route::get('/invoices/list-self', [InvoiceController::class, 'listSelf']);


});

/**
 * client and admin route
 */
Route::group(['midleware' => ['auth']], function () {

    // user
    Route::put('/users/self-update', [UserController::class, 'selfUpdate']);
    Route::post('/users/password/update', [UserController::class, 'passwordUpdate']);


    // posts
    Route::get('/posts/list-active', [PostController::class, 'listActive']);
    Route::get('/posts/list-by-user', [PostController::class, 'listByUser']);
    Route::get('/files/get-registration/{idUser}', [UserController::class, 'getRegistrationFiles']);

});

/**
 * admin routes
 */
Route::group(['middleware' => ['admin']], function () {
    
    Route::get('/get-home', [HomeController::class, 'getHomeData']);

    Route::get('/user/admin', [AuthController::class, 'getUser']);

    Route::get('/users/list', [UserController::class, 'list']);
    Route::get('/users/test', [UserController::class, 'test']);
    Route::get('/users', [UserController::class, 'get']);
    Route::get('/users/{id}', [UserController::class, 'getById']);
    Route::post('/users/with-students', [UserController::class, 'createWithStudent']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::put('/users/admin-password-update/{id}', [UserController::class, 'adminPasswordUpdate']);
    Route::post('/users/admin-upload-picture/{id}', [UserController::class, 'adminUploadPicture']);
    Route::put('/users/admin/self-update', [UserController::class, 'adminSelfUpdate']);
    Route::post('/users/inactivate/{id}', [UserController::class, 'inactivate']);
    Route::post('/users/activate/{id}', [UserController::class, 'activate']);
    Route::post('/users/delete-multiple', [UserController::class, 'deleteMultiple']);
    Route::delete('/users/{id}', [UserController::class, 'delete']);

    //
    Route::get('/users/get-registration/{id}', [UserController::class, 'getNewRegistration']);
    Route::post('/users/approve-registration/{id}', [UserController::class, 'approveRegistration']);


    Route::get('/students/list', [StudentController::class, 'list']);
    Route::get('/students/filter', [StudentController::class, 'filter']);
    Route::get('/students', [StudentController::class, 'get']);
    Route::get('/students/{id}', [StudentController::class, 'getById']);
    Route::post('/students', [StudentController::class, 'create']);
    Route::put('/students/admin-update/{id}', [StudentController::class, 'update']);
    Route::post('/students/admin-upload-picture/{id}', [StudentController::class, 'adminUploadPicture']);


    // units
    Route::get('/units', [UnitController::class, 'get']);
    Route::get('/units/{id}', [UnitController::class, 'getById']);
    Route::post('/units', [UnitController::class, 'create']);
    Route::put('/units/{id}', [UnitController::class, 'update']);

    // classes
    Route::get('/classes', [ClassController::class, 'get']);
    Route::get('/classes/{id}', [ClassController::class, 'getById']);
    Route::post('/classes', [ClassController::class, 'create']);
    Route::post('/classes/toggle-full/{id}', [ClassController::class, 'toggleFull']);
    Route::put('/classes/{id}', [ClassController::class, 'update']);
    Route::delete('/classes/{id}', [ClassController::class, 'delete']);

    // class times
    Route::get('/class-time/list', [ClassTimeController::class, 'list']);
    Route::get('/class-time', [ClassTimeController::class, 'get']);
    Route::get('/class-time/{id}', [ClassTimeController::class, 'getById']);
    Route::post('/class-time', [ClassTimeController::class, 'create']);
    Route::put('/class-time/{id}', [ClassTimeController::class, 'update']);
    Route::delete('/class-time/{id}', [ClassTimeController::class, 'delete']);

    //config
    Route::get('/contract/get-to-config', [ContractController::class, 'getContract']);
    Route::post('/contract/update', [ContractController::class, 'updateContract']);

    // student classes
    Route::post('/student-class/{idStudent}/{idClass}', [StudentController::class, 'addClass']);
    Route::delete('/student-class/{idStudent}/{idClass}', [StudentController::class, 'removeClass']);


    /**
     * docs | clicksign
     */
    Route::post('/users/add-signatory/{id}', [UserController::class, 'addSignatory']);
    Route::get('/contracts/list/{idStudent}', [UserController::class, 'listByStudent']);
    Route::post('/contracts/cancel/{id}', [DocumentsController::class, 'cancelContract']);
    Route::post('/contracts/generate/{idStudent}/{idClass}', [DocumentsController::class, 'generate']);
    Route::post('/contracts/notify/{id}', [DocumentsController::class, 'notify']);
    Route::post('/contracts/notifyAll', [DocumentsController::class, 'notifyAll']);

    // contracts
    Route::get('/contracts/list-all', [DocumentsController::class, 'list']);

    // posts
    Route::get('/posts/list', [PostController::class, 'list']);
    Route::post('/posts/update/{id}', [PostController::class, 'update']);
    Route::post('/posts', [PostController::class, 'create']);
    Route::get('/posts/get/{id}', [PostController::class, 'getById']);
    Route::delete('/posts/remove-image/{id}', [PostController::class, 'removeImage']);

    // invoices
    Route::get('/invoices/list-by-user/{id}', [InvoiceController::class, 'listByUser']);
    Route::get('/invoices/get/{id}', [InvoiceController::class, 'getById']);
    Route::put('/invoices/cancel/{id}', [InvoiceController::class, 'cancelInvoice']);
    Route::put('/invoices/{id}', [InvoiceController::class, 'update']);
    Route::post('/invoices', [InvoiceController::class, 'create']);
    Route::post('/invoices/pay-manually/{id}', [InvoiceController::class, 'payManually']);
    Route::post('/invoices/send-mail/{id}', [InvoiceController::class, 'sendInvoiceMail']);
    Route::post('/invoices/mail/all', [InvoiceController::class, 'sendAllUsersInvoicesMail']);
    Route::post('/invoices/attach-receipt/{id}', [InvoiceController::class, 'attachReceipt']);
    Route::post('/invoices/unsigned/generate', [InvoiceController::class, 'createForUnsignedUsers']);
    Route::get('/invoices/unsigned', [InvoiceController::class, 'getUnsignedUsers']);

    // reports
    Route::get('/reports/know-by', [ReportsController::class, 'knowBy']);
    Route::get('/reports/revenue', [ReportsController::class, 'revenue']);


    // signer config
    Route::post('/signer-config/save', [ParameterController::class, 'saveSigner']);
    Route::get('/signer-config/get', [ParameterController::class, 'getSigner']);

    // sales
    Route::post('/sales', [SaleController::class, 'create']);
    Route::get('/sales/{id}', [SaleController::class, 'getById']);
    Route::put('/sales/{id}', [SaleController::class, 'update']);
    Route::delete('/sales/{id}', [SaleController::class, 'delete']);
    Route::get('/sales', [SaleController::class, 'list']);

    // sales
    Route::post('/invoice-add', [InvoiceAddController::class, 'create']);
    Route::get('/invoice-add/{id}', [InvoiceAddController::class, 'getById']);
    Route::put('/invoice-add/{id}', [InvoiceAddController::class, 'update']);
    Route::delete('/invoice-add/{id}', [InvoiceAddController::class, 'delete']);
    Route::get('/user/{id}/invoice-add', [InvoiceAddController::class, 'list']);

    // general config
    Route::get('/config/get', [ParameterController::class, 'getConfig']);
    Route::post('/config/save', [ParameterController::class, 'saveConfig']);

    // payment methods
    Route::get('/payment-methods', [PaymentMethodController::class, 'list']);
    Route::post('/payment-methods', [PaymentMethodController::class, 'create']);
    Route::put('/payment-methods/{id}', [PaymentMethodController::class, 'update']);
    Route::delete('/payment-methods/{id}', [PaymentMethodController::class, 'delete']);
});
Route::post('/invoices/update-fee/{id}', [InvoiceController::class, 'updateFee']);
