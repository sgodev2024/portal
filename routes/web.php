<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CompanyController;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('admin')->group(function () {
    // Trang dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý thông tin công ty
    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');
});
