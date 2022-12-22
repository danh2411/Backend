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


Route::middleware(['cors'])->group(function ($router) {
    Route::get('/2fa/reset', [AuthController::class, 'resendEmail'])->name('2fa.resend');//api resend
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/code', [AuthController::class, 'codeEmail'])->name('codeEmail');//api check code 2fe gan vao nut submit
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:admin');
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/view-account', [AuthController::class, 'userProfile']);
    Route::get('/all-account', [AuthController::class, 'allAccount'])->middleware('role:admin');
    Route::get('/account/id={id}', [AuthController::class, 'oneAccount']);
    Route::post('/update-account/id={id}', [AuthController::class, 'editAccount'])->middleware('role:admin');
    Route::post('/update-profile', [AuthController::class, 'updateProflie']);
    Route::post('/changepass', [AuthController::class, 'changePassWord']);
    Route::post('/hiden/id={id}', [AuthController::class, 'hiden'])->middleware('role:admin');
});
Route::middleware(['cors', 'role:personnel'])->group(function ($router) {
    Route::post('/client/create', [ClientController::class, 'create']);
    Route::post('/client/edit/id={id}', [ClientController::class, 'edit']);
    Route::post('/client/hiden/id={id}', [ClientController::class, 'hiden']);
    Route::get('/client/client-profile', [ClientController::class, 'clientProfile']);
});

//services
Route::middleware(['cors', 'role:personnel'])->group(function ($router) {
    Route::post('/service/add', [ServiceController::class, 'create']);
    Route::post('/service/edit/id={id}', [ServiceController::class, 'edit']);
    Route::post('/service/hiden/id={id}', [ServiceController::class, 'hiden']);
    Route::get('/service/service-info', [ServiceController::class, 'serviceInfo']);

});

//rooms
Route::middleware(['cors'])->group(function ($router) {
    Route::post('/room/add', [RoomController::class, 'create'])->middleware('role:admin');
    Route::post('/room/edit/id={id}', [RoomController::class, 'edit'])->middleware('role:admin');
    Route::get('/room/hiden/id={id}', [RoomController::class, 'hiden'])->middleware('role:admin');
    Route::get('/room/getlist', [RoomController::class, 'roomAll']);
    Route::get('/room/filter', [RoomController::class, 'filterStatus']);
    Route::get('/room/emptyroom', [RoomController::class, 'emptyroom'])->middleware('role:personnel');
    Route::get('/room/clearroom/id={id}', [RoomController::class, 'clearroom'])->middleware('role:cleaners');

    Route::get('/room/getid/id={id}', [RoomController::class, 'getId']);
});

//bills
Route::middleware(['cors', 'role:personnel'])->group(function ($router) {
    Route::post('/bill/create', [BillController::class, 'create']);
    Route::post('/bill/edit/id={id}', [BillController::class, 'edit']);
    Route::get('/bill/hiden/id={id}', [BillController::class, 'hiden']);
    Route::get('/bill/pay/id={id}', [BillController::class, 'Pay']);
    Route::post('/bill/checkin', [BillController::class, 'checkin']);
    Route::get('/bill/bill-info/id={id}', [BillController::class, 'billInfo']);
    Route::post('/bill/getlistroom', [BillController::class, 'getListTotalRoomBy']);
    Route::post('/bill/getlistservice', [BillController::class, 'getListTotalServiceBy']);
    Route::post('/bill/getlist', [BillController::class, 'getListTotalBy']);


    //services theo bill
    Route::get('/bill/billservice/id={id}', [BillController::class, 'billservice']);
    //room theo bill
    Route::get('/bill/billroom/id={id}', [BillController::class, 'billroom']);
    //chang bill
    Route::post('/bill/changeroom', [BillController::class, 'changroom']);
//add service
    Route::post('/bill/addservice', [BillController::class, 'addservice']);
    Route::post('/bill/deletesevice', [BillController::class, 'deletesevice']);
    Route::get('/bill/clientroom/id={id}', [BillController::class, 'clientroom']);
    Route::get('/bill/priceroom/id={id}', [BillController::class, 'roomprice']);
// get theo 30d 7d 1d  pagram by=m or by=d by=h
    Route::post('/bill/getByMoth', [BillController::class, 'getByMoth']);


});

