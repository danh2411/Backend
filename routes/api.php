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
    Route::post('/login', [AuthController::class, 'login'])->name('login');//login
    Route::post('/code', [AuthController::class, 'codeEmail'])->name('codeEmail');//api check code 2fe gan vao nut submit
    Route::post('/register', [AuthController::class, 'register'])->middleware('role:admin');//dang ki
    Route::post('/logout', [AuthController::class, 'logout']);//logout
    Route::post('/refresh', [AuthController::class, 'refresh']);//reset token
    Route::get('/view-account', [AuthController::class, 'userProfile']);//xem 1 account
    Route::get('/all-account', [AuthController::class, 'allAccount'])->middleware('role:admin');//all account
    Route::get('/account/id={id}', [AuthController::class, 'oneAccount']);//one account
    Route::post('/update-account/id={id}', [AuthController::class, 'editAccount'])->middleware('role:admin');//update account
    Route::post('/update-profile', [AuthController::class, 'updateProflie']);//update profile
    Route::post('/changepass', [AuthController::class, 'changePassWord']);//change pass
    Route::post('/hiden/id={id}', [AuthController::class, 'hiden'])->middleware('role:admin');// hiden account
    Route::get('/search', [AuthController::class, 'searchAccount'])->middleware('role:admin');// tim kiem

});
Route::middleware(['cors', 'role:personnel'])->group(function ($router) {
    Route::post('/client/create', [ClientController::class, 'create']);//tao khach hang
    Route::post('/client/edit/id={id}', [ClientController::class, 'edit']);//update khach hang
    Route::post('/client/hiden/id={id}', [ClientController::class, 'hiden']);// an khach hang
    Route::get('/client/client-profile', [ClientController::class, 'clientProfile']);// xem khach hang
    Route::get('/client/getclient', [ClientController::class, 'getClient']);// xem khach hang
    Route::get('/client/search', [ClientController::class, 'searchClient']);// tracuu khach hang
});

//services
Route::middleware(['cors', 'role:personnel'])->group(function ($router) {
    Route::post('/service/add', [ServiceController::class, 'create']);// them dich vu
    Route::post('/service/edit/id={id}', [ServiceController::class, 'edit']);// sua dich vu
    Route::post('/service/hiden/id={id}', [ServiceController::class, 'hiden']);// an dich vu
    Route::get('/service/service-info', [ServiceController::class, 'serviceInfo']);// thong tin dich vu
    Route::get('/service/getservice', [ServiceController::class, 'getService']);// lay thong tin dich vu
    Route::get('/service/search', [ServiceController::class, 'searchService']);// tra cuu dich vu


});

//rooms
Route::middleware(['cors'])->group(function ($router) {
    Route::post('/room/add', [RoomController::class, 'create'])->middleware('role:admin');// them phong
    Route::post('/room/edit/id={id}', [RoomController::class, 'edit'])->middleware('role:admin');// sua phong
    Route::get('/room/hiden/id={id}', [RoomController::class, 'hiden']);// an phong
    Route::get('/room/getlist', [RoomController::class, 'roomAll']);// danh sach phong
    Route::get('/room/filter', [RoomController::class, 'filterStatus']);// loc phong
    Route::get('/room/emptyroom', [RoomController::class, 'emptyroom'])->middleware('role:personnel');
    Route::get('/room/clearroom/id={id}', [RoomController::class, 'clearroom'])->middleware('role:cleaners');// don phong
    Route::get('/room/search', [RoomController::class, 'searchRoom']);// seacrh
    Route::get('/room/getid/id={id}', [RoomController::class, 'getId']);// get phong theo id
});

//bills
Route::middleware(['cors', 'role:personnel'])->group(function ($router) {
    Route::post('/bill/create', [BillController::class, 'create']);// tao bill
    Route::post('/bill/edit/id={id}', [BillController::class, 'edit']);// sua bill
    Route::get('/bill/hiden/id={id}', [BillController::class, 'hiden']);// an bill
    Route::get('/bill/pay/id={id}', [BillController::class, 'Pay']);// thanh toan
    Route::post('/bill/checkin', [BillController::class, 'checkin']);// nhan phong
    Route::get('/bill/bill-info/id={id}', [BillController::class, 'billInfo']);// thong tin bill
    Route::post('/bill/getlistroom', [BillController::class, 'getListTotalRoomBy']);// danh sach phong theo bill
    Route::post('/bill/getlistservice', [BillController::class, 'getListTotalServiceBy']);// danh sach dich vu theo bill
    Route::post('/bill/getlist', [BillController::class, 'getListTotalBy']);// danh sach bill


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

    Route::post('/notifi/send', [\App\Http\Controllers\SendNotification::class, 'store']);
    // danh sach bill v2
    Route::get('/bill/billall', [BillController::class, 'listBill']);
    // view bill v2
    Route::get('/bill/viewbill/id={id}', [BillController::class, 'viewBill']);
    Route::get('/bill/viewsta', [BillController::class, 'staticRoom']);
});

