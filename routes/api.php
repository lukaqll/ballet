<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ClassTimeController;
use App\Http\Controllers\Api\ClicksignHooksController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DocumentsController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ParameterController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\UnitController;
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

Route::get('/classes/list', [ClassController::class, 'list']);


Route::group(['middleware' => ['admin']], function () {
    
    Route::get('/get-home', [HomeController::class, 'getHomeData']);

    Route::get('/user', [AuthController::class, 'getUser']);

    Route::get('/users/list', [UserController::class, 'list']);
    Route::get('/users', [UserController::class, 'get']);
    Route::get('/users/{id}', [UserController::class, 'getById']);
    Route::post('/users/with-students', [UserController::class, 'createWithStudent']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::put('/users/admin-password-update/{id}', [UserController::class, 'adminPasswordUpdate']);
    Route::post('/users/admin-upload-picture/{id}', [UserController::class, 'adminUploadPicture']);
    Route::put('/users/admin/self-update', [UserController::class, 'adminSelfUpdate']);
    Route::post('/users/password/update', [UserController::class, 'passwordUpdate']);

    //
    Route::get('/users/get-registration/{id}', [UserController::class, 'getNewRegistration']);
    Route::post('/users/approve-registration/{id}', [UserController::class, 'approveRegistration']);


    Route::get('/students/list', [StudentController::class, 'list']);
    Route::get('/students', [StudentController::class, 'get']);
    Route::get('/students/{id}', [StudentController::class, 'getById']);
    Route::post('/students', [StudentController::class, 'create']);
    Route::put('/students/admin-update/{id}', [StudentController::class, 'update']);
    Route::post('/students/admin-upload-picture/{id}', [StudentController::class, 'adminUploadPicture']);


    // units
    Route::get('/units/list', [UnitController::class, 'list']);
    Route::get('/units', [UnitController::class, 'get']);
    Route::get('/units/{id}', [UnitController::class, 'getById']);
    Route::post('/units', [UnitController::class, 'create']);
    Route::put('/units/{id}', [UnitController::class, 'update']);

    // classes
    Route::get('/classes', [ClassController::class, 'get']);
    Route::get('/classes/{id}', [ClassController::class, 'getById']);
    Route::post('/classes', [ClassController::class, 'create']);
    Route::put('/classes/{id}', [ClassController::class, 'update']);

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


    /**
     * docs | clicksign
     */
    Route::post('/users/add-signatory/{id}', [UserController::class, 'addSignatory']);
    Route::get('/contracts/list/{idStudent}', [UserController::class, 'listByStudent']);
    Route::post('/contracts/cancel/{id}', [DocumentsController::class, 'cancelContract']);
    Route::post('/contracts/generate/{idStudent}', [DocumentsController::class, 'generate']);
    Route::post('/contracts/notify/{id}', [DocumentsController::class, 'notify']);

    // contracts
    Route::get('/contracts/list-all', [DocumentsController::class, 'list']);

    // posts
    Route::get('/posts/list', [PostController::class, 'list']);
    Route::post('/posts/update/{id}', [PostController::class, 'update']);
    Route::post('/posts', [PostController::class, 'create']);
    Route::get('/posts/get/{id}', [PostController::class, 'getById']);
    Route::delete('/posts/remove-image/{id}', [PostController::class, 'removeImage']);

    
});

// auth
Route::post('/register', [UserController::class, 'publicCreateWithStudent']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// password reset
Route::post('/password-reset/send-mail', [AuthController::class, 'sendMailPasswordReset']);
Route::post('/password-reset', [AuthController::class, 'passowrdReset']);

// hooks
Route::post('/clicksign/hook', [ClicksignHooksController::class, 'hooksCallback']);
