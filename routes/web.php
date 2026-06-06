<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Hosting\Admin\DashboardController as HostingAdminDashboardController;
use App\Http\Controllers\Hosting\User\DashboardController;
use App\Http\Controllers\Joki\Admin\DashboardController as JokiAdminDashboardController;
use App\Http\Controllers\Joki\User\DashboardController as UserJokiDashboardController;
use App\Http\Controllers\Joki\User\ProgressController;
use App\Http\Controllers\Joki\User\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'loginindex'])->name('login');
Route::get('/register', [AuthController::class, 'registerindex'])->name('register');
Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');
Route::post('/register', [AuthController::class, 'registerProcess'])->name('register.process');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('superadmin/dashboard', [AdminDashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::get('superadmin/users', [UserController::class, 'index'])->name('superadmin.users.index');
    Route::get('superadmin/users/{id}', [UserController::class, 'show'])->name('superadmin.users.show');

    Route::get('admin/hosting/dashboard', [HostingAdminDashboardController::class, 'index'])->name('admin_hosting.dashboard');

    Route::get('user/hosting/dashboard', [DashboardController::class, 'index'])->name('user_hosting.dashboard');
    Route::get('user/hosting/create', [DashboardController::class, 'create'])->name('user_hosting.create');
    Route::post('user/hosting/store', [DashboardController::class, 'store'])->name('user_hosting.store');
    Route::get('user/hosting/projects/{id}', [DashboardController::class, 'show'])->name('user_hosting.show');
    Route::post('user/hosting/projects/{hashid}/env', [DashboardController::class, 'updateEnv'])->name('user_hosting.env.update');
    Route::get('user/hosting/projects', [DashboardController::class, 'projects'])->name('user_hosting.projects');
    Route::post('user/hosting/projects/{hashid}/redeploy', [DashboardController::class, 'redeploy'])->name('user_hosting.redeploy');

    // --- AKSI USER JOKI ---
    Route::get('user/joki/dashboard', [UserJokiDashboardController::class, 'index'])->name('user_joki.dashboard');
    Route::get('user/joki/create', [UserJokiDashboardController::class, 'create'])->name('user_joki.create');
    Route::post('user/joki/store', [UserJokiDashboardController::class, 'store'])->name('user_joki.store');
    Route::get('user/joki/progress', [ProgressController::class, 'index'])->name('user_joki.progress');
    Route::get('user/joki/riwayat', [RiwayatController::class, 'index'])->name('user_joki.riwayat');
    Route::get('user/joki/detail/{id}', [UserJokiDashboardController::class, 'detail'])->name('user_joki.detail');
    Route::post('user/joki/orders/payment/{payment_id}/proof', [UserJokiDashboardController::class, 'uploadPaymentProof'])->name('user_joki.payment.proof');
    Route::post('user/joki/orders/{order_id}/revision', [UserJokiDashboardController::class, 'requestRevision'])->name('user_joki.revision.store');

    // --- AKSI ADMIN JOKI ---
    Route::get('admin/joki/dashboard', [JokiAdminDashboardController::class, 'index'])->name('admin_joki.dashboard');
    Route::get('admin/joki/orders', [JokiAdminDashboardController::class, 'manageOrders'])->name('admin_joki.orders');
    Route::get('admin/joki/orders/{id}/edit', [JokiAdminDashboardController::class, 'editOrder'])->name('admin_joki.orders.edit');
    Route::put('admin/joki/orders/{id}', [JokiAdminDashboardController::class, 'updateOrder'])->name('admin_joki.orders.update');
    Route::post('admin/joki/orders/{id}/milestone', [JokiAdminDashboardController::class, 'storeMilestone'])->name('admin_joki.milestone.store');
    Route::post('admin/joki/orders/{id}/payment', [JokiAdminDashboardController::class, 'storePayment'])->name('admin_joki.payment.store');
    Route::put('admin/joki/payments/{payment_id}/verify', [JokiAdminDashboardController::class, 'verifyPayment'])->name('admin_joki.payment.verify');
    Route::put('admin/joki/revisions/{revision_id}/reply', [JokiAdminDashboardController::class, 'replyRevision'])->name('admin_joki.revision.reply');
});
