<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\AccountingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Accounting\SaleStaffController;

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
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/product_list', [AccountingController::class, 'productList']);
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/user/{id}/roles', [UserController::class, 'editRoles'])->name('user.edit.roles');
Route::post('/user/{id}/roles', [UserController::class, 'updateRoles'])->name('user.update.roles');

Route::resource('sale-staff', SaleStaffController::class);


