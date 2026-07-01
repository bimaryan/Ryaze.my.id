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

Route::middleware(['throttle:10,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginindex'])->name('login');
    Route::get('/register', [AuthController::class, 'registerindex'])->name('register');
    Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');
    Route::post('/register', [AuthController::class, 'registerProcess'])->name('register.process');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Password Reset Routes
    Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    // ── PROFIL USER ───────────────────────────────────────────────
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // ── LOGOUT ───────────────────────────────────────────────────
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markRead');

    // ═══════════════════════════════════════════════════════════════
    // SUPERADMIN ONLY
    // ═══════════════════════════════════════════════════════════════
    Route::middleware('role:superadmin')->group(function () {
        Route::get('superadmin/dashboard', [AdminDashboardController::class, 'index'])->name('superadmin.dashboard');
        Route::get('superadmin/users', [UserController::class, 'index'])->name('superadmin.users.index');
        Route::get('superadmin/users/{hashid}', [UserController::class, 'show'])->name('superadmin.users.show');
    });

    // ═══════════════════════════════════════════════════════════════
    // ADMIN HOSTING (+ superadmin)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware('role:admin_hosting,superadmin')->group(function () {
        Route::get('admin/hosting/dashboard', [HostingAdminDashboardController::class, 'index'])->name('admin_hosting.dashboard');
        Route::get('admin/hosting/pending', [HostingAdminDashboardController::class, 'pending'])->name('admin_hosting.pending');
        Route::get('admin/hosting/deployments', [HostingAdminDashboardController::class, 'deployments'])->name('admin_hosting.deployments');
        Route::get('admin/hosting/projects', [HostingAdminDashboardController::class, 'projects'])->name('admin_hosting.projects');
        Route::get('admin/hosting/databases', [HostingAdminDashboardController::class, 'databases'])->name('admin_hosting.databases');
        Route::post('admin/hosting/databases', [HostingAdminDashboardController::class, 'storeDatabase'])->name('admin_hosting.databases.store');
        Route::delete('admin/hosting/databases/{hashid}', [HostingAdminDashboardController::class, 'destroyDatabase'])->name('admin_hosting.databases.destroy');
        Route::get('admin/hosting/storage', [HostingAdminDashboardController::class, 'storage'])->name('admin_hosting.storage');
        Route::put('admin/hosting/storage/{hashid}', [HostingAdminDashboardController::class, 'updateStorage'])->name('admin_hosting.storage.update');
        Route::patch('admin/hosting/{hashid}/activate', [HostingAdminDashboardController::class, 'activateProject'])->name('admin_hosting.activate');
        Route::patch('admin/hosting/{hashid}/suspend', [HostingAdminDashboardController::class, 'suspendProject'])->name('admin_hosting.suspend');
        Route::delete('admin/hosting/{hashid}', [HostingAdminDashboardController::class, 'destroyProject'])->name('admin_hosting.destroy');
        
        // Kelola Tagihan Hosting
        Route::get('admin/hosting/billing', [\App\Http\Controllers\Hosting\Admin\BillingController::class, 'index'])->name('admin_hosting.billing');
        Route::put('admin/hosting/billing/{hashid}/verify', [\App\Http\Controllers\Hosting\Admin\BillingController::class, 'verifyPayment'])->name('admin_hosting.billing.verify');
    });

    // ═══════════════════════════════════════════════════════════════
    // USER HOSTING (+ admin_hosting, superadmin)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware('role:user_hosting,admin_hosting,superadmin')->group(function () {
        Route::get('user/hosting/dashboard', [DashboardController::class, 'index'])->name('user_hosting.dashboard');
        Route::get('user/hosting/create', [DashboardController::class, 'create'])->name('user_hosting.create');
        Route::post('user/hosting/store', [DashboardController::class, 'store'])->name('user_hosting.store');
        Route::get('user/hosting/projects/{hashid}', [DashboardController::class, 'show'])->name('user_hosting.show');
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
        Route::post('user/hosting/storage/{hashid}/upgrade', [StorageController::class, 'upgrade'])->name('user_hosting.storage.upgrade');
        Route::get('user/hosting/databases', [DatabaseController::class, 'index'])->name('user_hosting.databases');
        Route::post('user/hosting/databases', [DatabaseController::class, 'store'])->name('user_hosting.databases.store');
        Route::delete('user/hosting/databases/{hashid}', [DatabaseController::class, 'destroy'])->name('user_hosting.databases.destroy');
        Route::get('user/hosting/storage/{hashid}', [StorageController::class, 'show'])->name('user_hosting.storage.detail');
        Route::get('user/hosting/billing', [DashboardController::class, 'billingHistory'])->name('user_hosting.billing');
        Route::delete('user/hosting/projects/{hashid}/delete', [DashboardController::class, 'deleteProject'])->name('user_hosting.destroy');
        Route::patch('user/hosting/projects/{hashid}/settings', [DashboardController::class, 'updateSettings'])->name('user_hosting.settings.update');
        Route::post('user/hosting/projects/{hashid}/dev/start', [DashboardController::class, 'startDevServer'])->name('user_hosting.dev.start');
        Route::post('user/hosting/projects/{hashid}/dev/stop', [DashboardController::class, 'stopDevServer'])->name('user_hosting.dev.stop');
        Route::get('user/hosting/docs', [DashboardController::class, 'docs'])->name('user_hosting.docs');
    });

    // ═══════════════════════════════════════════════════════════════
    // USER JOKI (+ admin_joki, superadmin)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware('role:user_joki,admin_joki,superadmin')->group(function () {
        Route::get('user/joki/dashboard', [UserJokiDashboardController::class, 'index'])->name('user_joki.dashboard');
        Route::get('user/joki/create', [UserJokiDashboardController::class, 'create'])->name('user_joki.create');
        Route::post('user/joki/store', [UserJokiDashboardController::class, 'store'])->name('user_joki.store');
        Route::get('user/joki/progress', [ProgressController::class, 'index'])->name('user_joki.progress');
        Route::get('user/joki/riwayat', [RiwayatController::class, 'index'])->name('user_joki.riwayat');
        Route::get('user/joki/detail/{hashid}', [UserJokiDashboardController::class, 'detail'])->name('user_joki.detail');
        Route::post('user/joki/orders/payment/{hashid}/proof', [UserJokiDashboardController::class, 'uploadPaymentProof'])->name('user_joki.payment.proof');
        Route::post('user/joki/orders/{hashid}/revision', [UserJokiDashboardController::class, 'requestRevision'])->name('user_joki.revision.store');
        Route::get('user/joki/billing', [UserJokiDashboardController::class, 'billingHistory'])->name('user_joki.billing');
    });

    // ═══════════════════════════════════════════════════════════════
    // ADMIN JOKI (+ superadmin)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware('role:admin_joki,superadmin')->group(function () {
        Route::get('admin/joki/dashboard', [JokiAdminDashboardController::class, 'index'])->name('admin_joki.dashboard');
        Route::get('admin/joki/orders', [JokiAdminDashboardController::class, 'manageOrders'])->name('admin_joki.orders');
        Route::get('admin/joki/orders/{hashid}/edit', [JokiAdminDashboardController::class, 'editOrder'])->name('admin_joki.orders.edit');
        Route::put('admin/joki/orders/{hashid}', [JokiAdminDashboardController::class, 'updateOrder'])->name('admin_joki.orders.update');
        Route::post('admin/joki/orders/{hashid}/milestone', [JokiAdminDashboardController::class, 'storeMilestone'])->name('admin_joki.milestone.store');
        Route::post('admin/joki/orders/{hashid}/payment', [JokiAdminDashboardController::class, 'storePayment'])->name('admin_joki.payment.store');
        Route::put('admin/joki/payments/{hashid}/verify', [JokiAdminDashboardController::class, 'verifyPayment'])->name('admin_joki.payment.verify');
        Route::put('admin/joki/revisions/{hashid}/reply', [JokiAdminDashboardController::class, 'replyRevision'])->name('admin_joki.revision.reply');
        
        // Manajemen Layanan Joki
        Route::resource('admin/joki/services', \App\Http\Controllers\Joki\Admin\ServiceController::class)->names('admin_joki.services');
        
        // Rekap Keuangan Joki
        Route::get('admin/joki/finance', [JokiAdminDashboardController::class, 'financeReport'])->name('admin_joki.finance');
    });
});

