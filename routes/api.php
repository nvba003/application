<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Accounting\AccountingController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route 'login' cần được gọi mà không có middleware 'auth:api'
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function() {
    // Đặt các route cần bảo vệ ở đây
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);  
    
});

    Route::post('/accounting_orders', [AccountingController::class, 'store']);
    Route::post('/accounting_recovery', [AccountingController::class, 'recovery']);//thu hồi từ NVBH
    Route::post('/order_recovery', [AccountingController::class, 'accountingRecovery']);//đơn hàng thu hồi
    Route::post('/save_info_recovery_staff', [AccountingController::class, 'saveInfoRecoveryStaff']);//đơn hàng thu hồi
    Route::post('/accounting_product_price', [AccountingController::class, 'updateProductPrice']);
    Route::post('/export_temporary', [AccountingController::class, 'exportTemporary']);//đơn hàng tạm ứng
    Route::post('/import_temporary', [AccountingController::class, 'importTemporary']);//đơn hàng hoàn ứng

    Route::get('/accounting_orders', [AccountingController::class, 'getOrderCodes']);
    Route::get('/accounting_recovery', [AccountingController::class, 'getRecoveryCodes']);//thu hồi từ NVBH
    Route::get('/order_recovery', [AccountingController::class, 'getOrderRecovery']);//đơn hàng thu hồi
    Route::get('/export_temporary', [AccountingController::class, 'getExportTemporary']);//đơn hàng tạm ứng
    Route::get('/import_temporary', [AccountingController::class, 'getImportTemporary']);//đơn hàng hoàn ứng

    

