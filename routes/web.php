<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Joki\Admin\DashboardController as JokiAdminDashboardController;
use App\Http\Controllers\Joki\User\DashboardController as UserJokiDashboardController;
use App\Http\Controllers\Hosting\Admin\DashboardController as HostingAdminDashboardController;
use App\Http\Controllers\Hosting\User\DashboardController as UserHostingDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'loginindex'])->name('login');
Route::get('/register', [AuthController::class, 'registerindex'])->name('register');
Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');
Route::post('/register', [AuthController::class, 'registerProcess'])->name('register.process');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('superadmin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('admin/joki/dashboard', [JokiAdminDashboardController::class, 'index'])->name('admin_joki.dashboard');
    Route::get('admin/hosting/dashboard', [HostingAdminDashboardController::class, 'index'])->name('admin_hosting.dashboard');
    Route::get('user/hosting/dashboard', [UserHostingDashboardController::class, 'index'])->name('user_hosting.dashboard');

    Route::get('user/joki/dashboard', [UserJokiDashboardController::class, 'index'])->name('user_joki.dashboard');
    Route::get('user/joki/create', [UserJokiDashboardController::class, 'create'])->name('user_joki.create');
    Route::post('user/joki/store', [UserJokiDashboardController::class, 'store'])->name('user_joki.store');
    Route::get('user/joki/detail/{id}', [UserJokiDashboardController::class, 'detail'])->name('user_joki.detail');
});
