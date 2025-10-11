<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Staff\ChatControllers;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\ChatcustomerController;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// route admin
Route::prefix('admin')->middleware(['auth', 'checkRole:1'])->group(function () {
    // Trang dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Quản lý thông tin công ty
    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{id}/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/{id}/assign', [ChatController::class, 'assign'])->name('assign');
        Route::post('/{id}/send', [ChatController::class, 'sendMessage'])->name('send');
    });
});
// route admin, nhân viên
Route::prefix('customers')->name('customers.')->middleware(['auth', 'checkRole:1,2'])->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/store', [CustomerController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])->name('delete');
    Route::post('/import', [CustomerController::class, 'import'])->name('import');
});
// route nhân viên
Route::prefix('staff')->name('staff.')->middleware(['auth', 'checkRole:2'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chats', [ChatControllers::class, 'index'])->name('chats.index');
    Route::get('/chats/{id}', [ChatControllers::class, 'show'])->name('chats.show');
    Route::post('/chats/{id}/send', [ChatControllers::class, 'send'])->name('chats.send');
    Route::get('/chats/{id}/messages', [ChatControllers::class, 'getMessages'])->name('chats.messages');
});
// route khách hàng
Route::prefix('customer')->name('customer.')->middleware(['auth', 'checkRole:3'])->group(function () {
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::get('/chat', [ChatCustomerController::class, 'index'])->name('chatcustomer.index');
    Route::get('/chat/messages', [ChatCustomerController::class, 'getMessages'])->name('chatcustomer.messages');
    Route::post('/chat/send', [ChatCustomerController::class, 'send'])->name('chatcustomer.send');
});
Route::prefix('customer')->name('customer.')->middleware(['auth', 'checkRole:3', 'must.update.profile'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});
