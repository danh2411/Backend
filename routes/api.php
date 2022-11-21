<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RoomController;


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



Route::middleware(['cors'])->group(function($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/view-account', [AuthController::class, 'userProfile']);
    Route::get('/all-account', [AuthController::class, 'allAccount']);
    Route::get('/account/id={id}', [AuthController::class,'oneAccount']);
    Route::post('/update-account/id={id}', [AuthController::class, 'editAccount']);
    Route::post('/update-profile', [AuthController::class, 'updateProflie']);
    Route::post('/changepass', [AuthController::class, 'changePassWord']);
    Route::post('/hiden/id={id}', [AuthController::class,'hiden']);
});
Route::middleware(['cors'])->group(function($router) {
    Route::post('/client/create', [ClientController::class,'create']);
    Route::post('/client/edit/id={id}', [ClientController::class,'edit']);
    Route::post('/client/hiden/id={id}', [ClientController::class,'hiden']);
    Route::get('/client/client-profile', [ClientController::class, 'clientProfile']);
});

//services
Route::middleware(['cors'])->group(function($router) {
    Route::post('/service/add', [ServiceController::class,'create']);
    Route::post('/service/edit/id={id}', [ServiceController::class,'edit']);
    Route::post('/service/hiden/id={id}', [ServiceController::class,'hiden']);
    Route::get('/service/service-info', [ServiceController::class, 'serviceInfo']);

});

//rooms
Route::middleware(['cors'])->group(function($router) {
    Route::post('/room/add', [RoomController::class,'create']);
    Route::post('/room/edit/id={id}', [RoomController::class,'edit']);
    Route::post('/room/hiden/id={id}', [RoomController::class,'hiden']);
    Route::get('/room/getlist', [RoomController::class, 'roomAll']);
    Route::post('/room/filter', [RoomController::class, 'filterStatus']);


});

//bills
Route::middleware(['cors'])->group(function($router) {
    Route::post('/bill/create', [BillController::class,'create']);
    Route::post('/bill/edit/id={id}', [BillController::class,'edit']);
    Route::post('/bill/hiden/id={id}', [BillController::class,'hiden']);
    Route::get('/bill/bill-info/id={id}', [BillController::class, 'billInfo']);
    Route::post('/bill/getlistroom', [BillController::class, 'getListTotalRoomBy']);
    Route::post('/bill/getlistservice', [BillController::class, 'getListTotalServiceBy']);
    Route::post('/bill/getlist', [BillController::class, 'getListTotalBy']);



});

