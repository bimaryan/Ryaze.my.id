<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Hosting\Admin\DashboardController as HostingAdminDashboardController;
use App\Http\Controllers\Hosting\User\DashboardController;
use App\Http\Controllers\Hosting\User\DatabaseController;
use App\Http\Controllers\Hosting\User\PhpVersionController;
use App\Http\Controllers\Hosting\User\StorageController;
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

    // --- ADMIN HOSTING ---
    Route::get('admin/hosting/dashboard', [HostingAdminDashboardController::class, 'index'])->name('admin_hosting.dashboard');
    Route::get('admin/hosting/pending', [HostingAdminDashboardController::class, 'pending'])->name('admin_hosting.pending');
    Route::get('admin/hosting/deployments', [HostingAdminDashboardController::class, 'deployments'])->name('admin_hosting.deployments');
    Route::get('admin/hosting/projects', [HostingAdminDashboardController::class, 'projects'])->name('admin_hosting.projects');
    Route::patch('admin/hosting/{hashid}/activate', [HostingAdminDashboardController::class, 'activateProject'])->name('admin_hosting.activate');
    Route::patch('admin/hosting/{hashid}/suspend', [HostingAdminDashboardController::class, 'suspendProject'])->name('admin_hosting.suspend');
    Route::delete('admin/hosting/{hashid}', [HostingAdminDashboardController::class, 'destroyProject'])->name('admin_hosting.destroy');

    // --- USER HOSTING ---
    Route::get('user/hosting/dashboard', [DashboardController::class, 'index'])->name('user_hosting.dashboard');
    Route::get('user/hosting/create', [DashboardController::class, 'create'])->name('user_hosting.create');
    Route::post('user/hosting/store', [DashboardController::class, 'store'])->name('user_hosting.store');
    Route::get('user/hosting/projects/{id}', [DashboardController::class, 'show'])->name('user_hosting.show');
    Route::post('user/hosting/projects/{hashid}/env', [DashboardController::class, 'updateEnv'])->name('user_hosting.env.update');
    Route::get('user/hosting/projects', [DashboardController::class, 'projects'])->name('user_hosting.projects');
    Route::post('user/hosting/projects/{hashid}/redeploy', [DashboardController::class, 'redeploy'])->name('user_hosting.redeploy');
    Route::get('user/hosting/projects/{hashid}/logs', [DashboardController::class, 'buildLogs'])->name('user_hosting.build_logs');
    Route::post('user/hosting/projects/{hashid}/terminal', [DashboardController::class, 'terminal'])->name('user_hosting.terminal');
    Route::get('user/hosting/projects/{hashid}/files', [DashboardController::class, 'getFiles'])->name('user_hosting.files');
    Route::get('user/hosting/projects/{hashid}/files/read', [DashboardController::class, 'readFile'])->name('user_hosting.files.read');
    Route::post('user/hosting/projects/{hashid}/files/save', [DashboardController::class, 'saveFile'])->name('user_hosting.files.save');
    Route::post('user/hosting/projects/{hashid}/files/upload', [DashboardController::class, 'uploadFile'])->name('user_hosting.files.upload');
    Route::post('user/hosting/projects/{hashid}/files/create', [DashboardController::class, 'createItem'])->name('user_hosting.files.create');
    Route::post('user/hosting/projects/{hashid}/files/delete', [DashboardController::class, 'deleteItem'])->name('user_hosting.files.delete');
    Route::get('user/hosting/projects/{hashid}/files/download', [DashboardController::class, 'downloadItem'])->name('user_hosting.files.download');
    Route::get('user/hosting/storage', [StorageController::class, 'index'])->name('user_hosting.storage');
    Route::get('user/hosting/storage/{hashid}', [StorageController::class, 'show'])->name('user_hosting.storage.show');
    Route::get('user/hosting/databases', [DatabaseController::class, 'index'])->name('user_hosting.databases');
    Route::post('user/hosting/databases', [DatabaseController::class, 'store'])->name('user_hosting.databases.store');
    Route::delete('user/hosting/databases/{hashid}', [DatabaseController::class, 'destroy'])->name('user_hosting.databases.destroy');
    Route::get('user/hosting/storage/{hashid}', [StorageController::class, 'show'])->name('user_hosting.storage.detail');
    Route::get('user/hosting/billing', [DashboardController::class, 'billingHistory'])->name('user_hosting.billing');
    Route::delete('user/hosting/projects/{hashid}/delete', [DashboardController::class, 'deleteProject'])->name('user_hosting.destroy');
    Route::patch('user/hosting/projects/{hashid}/settings', [DashboardController::class, 'updateSettings'])->name('user_hosting.settings.update');

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
