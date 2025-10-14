<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Staff\ChatControllers;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Customer\TicketController;
use App\Http\Controllers\Customer\ChatcustomerController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;

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
        Route::prefix('tickets')->name('admin.tickets.')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminTicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [AdminTicketController::class, 'reply'])->name('reply');
        Route::patch('/{id}/close', [AdminTicketController::class, 'close'])->name('close');
    });

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
        Route::get('/list-updates', [ChatController::class, 'getListUpdates']);
    });

    // Quản lý nhân viên
    Route::prefix('staffs')->name('admin.staffs.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/store', [StaffController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [StaffController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StaffController::class, 'update'])->name('update');
        Route::post('/delete-selected', [StaffController::class, 'deleteSelected'])->name('deleteSelected');
        Route::post('/import', [StaffController::class, 'import'])->name('import');
    });
});
// route admin, nhân viên
Route::prefix('customers')->name('customers.')->middleware(['auth', 'checkRole:1,2'])->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/store', [CustomerController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [CustomerController::class, 'update'])->name('update');
    Route::post('/import', [CustomerController::class, 'import'])->name('import');
    Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
    Route::post('/bulk-action', [CustomerController::class, 'bulkAction'])->name('bulkAction');
    Route::post('/bulk-action', [CustomerController::class, 'bulkAction'])->name('bulkAction');
});
// route nhân viên
Route::prefix('staff')->name('staff.')->middleware(['auth', 'checkRole:2'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chats', [ChatControllers::class, 'index'])->name('chats.index');
    Route::get('/chats/{id}', [ChatControllers::class, 'show'])->name('chats.show');
    Route::post('/chats/{id}/send', [ChatControllers::class, 'send'])->name('chats.send');
    Route::post('/chats/{id}/mark-read', [ChatControllers::class, 'markAsRead'])->name('chats.mark-read');
    Route::get('/chats/{id}/messages', [ChatControllers::class, 'getMessages'])->name('chats.messages');
});
// route khách hàng
Route::prefix('customer')->name('customer.')->middleware(['auth', 'checkRole:3'])->group(function () {
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::get('/chat', [ChatCustomerController::class, 'index'])->name('chatcustomer.index');
    Route::get('/chat/messages', [ChatCustomerController::class, 'getMessages'])->name('chatcustomer.messages');
    Route::post('/chat/send', [ChatCustomerController::class, 'send'])->name('chatcustomer.send');

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{id}', [TicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [TicketController::class, 'reply'])->name('reply');
    });
});
Route::prefix('customer')->name('customer.')->middleware(['auth', 'checkRole:3', 'must.update.profile'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
