<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\AccountingController;
use App\Http\Controllers\Accounting\AccountingOrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Accounting\SaleStaffController;
use App\Http\Controllers\Accounting\ReportController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('dashboard', ['header' => 'Welcome']);
})->middleware(['auth'])->name('dashboard');


require __DIR__.'/auth.php';

Route::get('/product_list', [AccountingController::class, 'productList']);
Route::get('/product-discounts', [AccountingController::class, 'productDiscounts'])->name('productDiscounts');
Route::post('/product-discounts', [AccountingController::class, 'updateProductDiscount'])->name('updateProductDiscount');
Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::get('/user/{id}/roles', [UserController::class, 'editRoles'])->name('user.edit.roles');
Route::post('/user/{id}/roles', [UserController::class, 'updateRoles'])->name('user.update.roles');

Route::resource('sale-staff', SaleStaffController::class);
Route::get('/sale-staff', [SaleStaffController::class, 'index'])->name('sale-staff');

Route::get('/orders', [AccountingOrderController::class, 'index'])->name('orders.index');
Route::get('/recovery', [AccountingOrderController::class, 'recovery'])->name('orders.recovery');

Route::get('/immediate-not-summarized', [AccountingOrderController::class, 'immediateNotSummarized'])->name('orders.immediate_not_summarized');
Route::get('/scheduled-not-summarized', [AccountingOrderController::class, 'scheduledNotSummarized'])->name('orders.scheduled_not_summarized');
Route::get('/recovery-not-summarized', [AccountingOrderController::class, 'recoveryNotSummarized'])->name('orders.recovery_not_summarized');
Route::get('/order-recovery-not-summarized', [AccountingOrderController::class, 'orderRecoveryNotSummarized'])->name('order_recovery_not_summarized');
Route::post('/add-summary-order-for-scheduled', [AccountingOrderController::class, 'addSummaryOrderForScheduled'])->name('add_summary_order_for_scheduled');
Route::post('/add-summary-order-for-immediate', [AccountingOrderController::class, 'addSummaryOrderForImmediate'])->name('add_summary_order_for_immediate');
Route::post('/add-summary-order-for-recovery', [AccountingOrderController::class, 'addSummaryOrderForRecovery'])->name('add_summary_order_for_recovery');
Route::post('/add-summary-order-for-order-recovery', [AccountingOrderController::class, 'addSummaryOrderForOrderRecovery'])->name('add_summary_order_for_order_recovery');
Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
Route::get('/transaction-details', [ReportController::class, 'transactionDetails'])->name('transactionDetails');
Route::get('/summary-orders', [ReportController::class, 'summaryOrders'])->name('summary_orders');
Route::post('/save-transaction', [ReportController::class, 'saveTransaction']);
Route::put('/update-summary-orders', [ReportController::class, 'updateSummary']);
Route::put('/update-is-entered/{id}', [ReportController::class, 'updateIsEntered']);
Route::post('/add-transaction-detail', [ReportController::class, 'addTransactionDetail']);
Route::put('/update-transaction', [ReportController::class, 'updateTransaction']);
Route::put('/update-transaction-detail', [ReportController::class, 'updateTransactionDetail']);




