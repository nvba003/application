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
Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::get('/user/{id}/roles', [UserController::class, 'editRoles'])->name('user.edit.roles');
Route::post('/user/{id}/roles', [UserController::class, 'updateRoles'])->name('user.update.roles');

Route::resource('sale-staff', SaleStaffController::class);
Route::get('/sale-staff', [SaleStaffController::class, 'index'])->name('sale-staff');

Route::get('/orders', [AccountingOrderController::class, 'index'])->name('orders.index');
Route::get('/recovery', [AccountingOrderController::class, 'recovery'])->name('orders.recovery');

Route::get('/immediate-not-summarized', [AccountingOrderController::class, 'immediateNotSummarized'])->name('orders.immediate_not_summarized');
Route::get('/scheduled-not-summarized', [AccountingOrderController::class, 'scheduledNotSummarized'])->name('orders.scheduled_not_summarized');
Route::post('/add-summary-order-for-scheduled', [AccountingOrderController::class, 'addSummaryOrderForScheduled'])->name('add_summary_order_for_scheduled');
Route::post('/add-summary-order-for-immediate', [AccountingOrderController::class, 'addSummaryOrderForImmediate'])->name('add_summary_order_for_immediate');
Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
Route::get('/summary-orders', [ReportController::class, 'summaryOrders'])->name('summary_orders');
Route::post('/save-transaction', [ReportController::class, 'saveTransaction']);
Route::put('/update-summary-orders', [ReportController::class, 'updateSummary']);
Route::put('/update-is-entered/{id}', [ReportController::class, 'updateIsEntered']);




