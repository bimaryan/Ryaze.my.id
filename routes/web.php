<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Hosting\Admin\DashboardController as HostingAdminDashboardController;
use App\Http\Controllers\Hosting\User\DashboardController;
use App\Http\Controllers\Hosting\User\DatabaseController;
use App\Http\Controllers\Hosting\User\PhpVersionController;
use App\Http\Controllers\Hosting\User\StorageController;
use App\Http\Controllers\Hosting\User\DomainController;
use App\Http\Controllers\Hosting\User\CronController;
use App\Http\Controllers\Joki\Admin\DashboardController as JokiAdminDashboardController;
use App\Http\Controllers\Joki\User\DashboardController as UserJokiDashboardController;
use App\Http\Controllers\Joki\User\ProgressController;
use App\Http\Controllers\Joki\User\RiwayatController;
use App\Http\Controllers\Blog\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// ── BLOG PUBLIK ─────────────────────────────────────────────
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/kategori/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

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

    // ── EMAIL VERIFICATION ─────────────────────────────────────────
    Route::get('/email/verify', function () {
        return view('pages.auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        $user = $request->user();
        $redirect = match ($user->role) {
            'superadmin' => '/superadmin/dashboard',
            'admin_joki' => '/admin/joki/dashboard',
            'admin_hosting' => '/admin/hosting/dashboard',
            'user_joki' => '/user/joki/dashboard',
            'user_hosting' => '/user/hosting/dashboard',
            default => '/dashboard',
        };
        return redirect($redirect)->with('success', 'Email berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link verifikasi telah dikirim ulang ke email Anda!');
    })->middleware(['throttle:6,1'])->name('verification.send');
    // ── WALLET & AFFILIATE ───────────────────────────────────────
    Route::get('/user/wallet', [\App\Http\Controllers\WalletController::class, 'history'])->name('user.wallet.history');
    Route::post('/user/wallet/topup', [\App\Http\Controllers\WalletController::class, 'topUp'])->name('user.wallet.topup');
    Route::get('/user/wallet/withdraw', [\App\Http\Controllers\WalletController::class, 'withdrawForm'])->name('user.wallet.withdraw');
    Route::post('/user/wallet/withdraw', [\App\Http\Controllers\WalletController::class, 'withdrawProcess'])->name('user.wallet.withdraw.process');
    Route::get('/user/affiliate', [\App\Http\Controllers\AffiliateController::class, 'dashboard'])->name('user.affiliate.dashboard');

    // ── LOGOUT ───────────────────────────────────────────────────
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markRead');

    // ═══════════════════════════════════════════════════════════════
    // SUPERADMIN ONLY
    // ═══════════════════════════════════════════════════════════════
    Route::middleware(['role:superadmin', 'verified'])->group(function () {
        Route::get('superadmin/dashboard', [AdminDashboardController::class, 'index'])->name('superadmin.dashboard');
        Route::get('superadmin/server-status', [AdminDashboardController::class, 'getServerStatus'])->name('superadmin.server_status');
        
        // Backup Sistem
        Route::get('superadmin/backup', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('superadmin.backup.index');
        Route::post('superadmin/backup/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('superadmin.backup.create');
        Route::get('superadmin/backup/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('superadmin.backup.download');
        Route::delete('superadmin/backup/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('superadmin.backup.destroy');
        Route::post('superadmin/backup/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('superadmin.backup.restore');

        Route::get('superadmin/users', [UserController::class, 'index'])->name('superadmin.users.index');
        Route::get('superadmin/users/{hashid}', [UserController::class, 'show'])->name('superadmin.users.show');
        Route::put('superadmin/users/{hashid}/role', [UserController::class, 'updateRole'])->name('superadmin.users.role.update');
        Route::patch('superadmin/users/{hashid}/status', [UserController::class, 'toggleStatus'])->name('superadmin.users.status.toggle');
        Route::delete('superadmin/users/{hashid}', [UserController::class, 'destroy'])->name('superadmin.users.destroy');
        Route::get('superadmin/settings', [SettingController::class, 'index'])->name('superadmin.settings');
        Route::put('superadmin/settings', [SettingController::class, 'update'])->name('superadmin.settings.update');
        Route::get('superadmin/activity-logs', [ActivityLogController::class, 'index'])->name('superadmin.activity_logs');
        Route::get('superadmin/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('superadmin.withdrawals.index');
        Route::patch('superadmin/withdrawals/{id}/status', [\App\Http\Controllers\Admin\WithdrawalController::class, 'updateStatus'])->name('superadmin.withdrawals.update');
        Route::resource('superadmin/portfolios', \App\Http\Controllers\Admin\PortfolioController::class)->names('superadmin.portfolios');
        Route::patch('superadmin/portfolios/{hashid}/status', [\App\Http\Controllers\Admin\PortfolioController::class, 'toggleStatus'])->name('superadmin.portfolios.status.toggle');

        // Manajemen Artikel
        Route::post('superadmin/articles/upload-image', [\App\Http\Controllers\Admin\ArticleController::class, 'uploadImage'])->name('superadmin.articles.uploadImage');
        Route::post('superadmin/articles/import', [\App\Http\Controllers\Admin\ArticleController::class, 'import'])->name('superadmin.articles.import');
        Route::post('superadmin/articles/generate-ai', [\App\Http\Controllers\Admin\ArticleController::class, 'generateAi'])->name('superadmin.articles.generate_ai');
        Route::get('superadmin/articles/template', [\App\Http\Controllers\Admin\ArticleController::class, 'downloadTemplate'])->name('superadmin.articles.template');
        Route::resource('superadmin/articles', \App\Http\Controllers\Admin\ArticleController::class)->names('superadmin.articles');
        Route::patch('superadmin/articles/{hashid}/featured', [\App\Http\Controllers\Admin\ArticleController::class, 'toggleFeatured'])->name('superadmin.articles.featured');
        Route::patch('superadmin/articles/{hashid}/status', [\App\Http\Controllers\Admin\ArticleController::class, 'toggleStatus'])->name('superadmin.articles.status');
        Route::resource('superadmin/article-categories', \App\Http\Controllers\Admin\ArticleCategoryController::class)->names('superadmin.article_categories');
    });

    // ═══════════════════════════════════════════════════════════════
    // ADMIN HOSTING (+ superadmin)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware(['role:admin_hosting,superadmin', 'verified'])->group(function () {
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

        // Kelola Voucher Hosting
        Route::get('admin/hosting/vouchers', [\App\Http\Controllers\Hosting\Admin\VoucherController::class, 'index'])->name('admin_hosting.vouchers.index');
        Route::get('admin/hosting/vouchers/create', [\App\Http\Controllers\Hosting\Admin\VoucherController::class, 'create'])->name('admin_hosting.vouchers.create');
        Route::post('admin/hosting/vouchers', [\App\Http\Controllers\Hosting\Admin\VoucherController::class, 'store'])->name('admin_hosting.vouchers.store');
        Route::get('admin/hosting/vouchers/{hashid}/edit', [\App\Http\Controllers\Hosting\Admin\VoucherController::class, 'edit'])->name('admin_hosting.vouchers.edit');
        Route::put('admin/hosting/vouchers/{hashid}', [\App\Http\Controllers\Hosting\Admin\VoucherController::class, 'update'])->name('admin_hosting.vouchers.update');
        Route::delete('admin/hosting/vouchers/{hashid}', [\App\Http\Controllers\Hosting\Admin\VoucherController::class, 'destroy'])->name('admin_hosting.vouchers.destroy');
        
        // Tiket Bantuan
        Route::get('admin/hosting/tickets', [\App\Http\Controllers\Hosting\Admin\TicketController::class, 'index'])->name('admin_hosting.tickets.index');
        Route::get('admin/hosting/tickets/{hashid}', [\App\Http\Controllers\Hosting\Admin\TicketController::class, 'show'])->name('admin_hosting.tickets.show');
        Route::post('admin/hosting/tickets/{hashid}/reply', [\App\Http\Controllers\Hosting\Admin\TicketController::class, 'reply'])->name('admin_hosting.tickets.reply');
        Route::post('admin/hosting/tickets/{hashid}/close', [\App\Http\Controllers\Hosting\Admin\TicketController::class, 'close'])->name('admin_hosting.tickets.close');
    });

    // ═══════════════════════════════════════════════════════════════
    // USER HOSTING (+ admin_hosting, superadmin)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware(['role:user_hosting,admin_hosting,superadmin', 'verified'])->group(function () {
        Route::get('user/hosting/dashboard', [DashboardController::class, 'index'])->name('user_hosting.dashboard');
        Route::get('user/hosting/create', [DashboardController::class, 'create'])->name('user_hosting.create');
        Route::get('user/hosting/marketplace', [DashboardController::class, 'marketplace'])->name('user_hosting.marketplace');
        Route::get('user/hosting/templates', [DashboardController::class, 'templates'])->name('user_hosting.templates');
        Route::get('user/hosting/template/{key}/preview', [DashboardController::class, 'previewTemplate'])->name('user_hosting.template.preview');
        Route::post('user/hosting/store', [DashboardController::class, 'store'])->name('user_hosting.store');
        Route::get('user/hosting/projects/{hashid}', [DashboardController::class, 'show'])->name('user_hosting.show');
        Route::post('user/hosting/projects/{hashid}/env', [DashboardController::class, 'updateEnv'])->name('user_hosting.env.update');
        Route::get('user/hosting/projects', [DashboardController::class, 'projects'])->name('user_hosting.projects');
        Route::get('user/hosting/server-status', [DashboardController::class, 'getServerStatus'])->name('user_hosting.server_status');
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
        Route::post('user/hosting/projects/{hashid}/ide/chat', [DashboardController::class, 'ideChat'])->name('user_hosting.ide.chat');
        Route::post('user/hosting/projects/{hashid}/ide/search', [DashboardController::class, 'ideSearch'])->name('user_hosting.ide.search');
        Route::post('user/hosting/projects/{hashid}/ide/git/status', [DashboardController::class, 'ideGitStatus'])->name('user_hosting.ide.git.status');
        Route::post('user/hosting/projects/{hashid}/ide/git/commit', [DashboardController::class, 'ideGitCommit'])->name('user_hosting.ide.git.commit');
        Route::post('user/hosting/projects/{hashid}/ide/git/pull', [DashboardController::class, 'ideGitPull'])->name('user_hosting.ide.git.pull');
        Route::post('user/hosting/projects/{hashid}/ide/git/push', [DashboardController::class, 'ideGitPush'])->name('user_hosting.ide.git.push');
        Route::get('user/hosting/storage', [StorageController::class, 'index'])->name('user_hosting.storage');
        Route::post('user/hosting/storage/upgrade', [StorageController::class, 'upgrade'])->name('user_hosting.storage.upgrade');
        Route::get('user/hosting/storage/{hashid}', [StorageController::class, 'show'])->name('user_hosting.storage.show');
        Route::get('user/hosting/databases', [DatabaseController::class, 'index'])->name('user_hosting.databases');
        Route::post('user/hosting/databases', [DatabaseController::class, 'store'])->name('user_hosting.databases.store');
        Route::delete('user/hosting/databases/{hashid}', [DatabaseController::class, 'destroy'])->name('user_hosting.databases.destroy');

        // Email Management (disabled)
        // Route::get('user/hosting/emails', [\App\Http\Controllers\Hosting\User\EmailController::class, 'index'])->name('user_hosting.emails.index');
        // Route::post('user/hosting/emails', [\App\Http\Controllers\Hosting\User\EmailController::class, 'store'])->name('user_hosting.emails.store');
        // Route::delete('user/hosting/emails/{hashid}', [\App\Http\Controllers\Hosting\User\EmailController::class, 'destroy'])->name('user_hosting.emails.destroy');

        // Billing & Vouchers
        Route::get('user/hosting/databases/{hashid}/export', [DatabaseController::class, 'export'])->name('user_hosting.databases.export');
        Route::post('user/hosting/databases/{hashid}/import', [DatabaseController::class, 'import'])->name('user_hosting.databases.import');
        Route::get('user/hosting/pma', [DatabaseController::class, 'pmaIndex'])->name('user_hosting.databases.pma');
        Route::get('user/hosting/databases/{hashid}/pma', [DatabaseController::class, 'pmaLogin'])->name('user_hosting.databases.pma.login');
        Route::get('user/hosting/billing', [DashboardController::class, 'billingHistory'])->name('user_hosting.billing');
        Route::post('user/hosting/billing/subscribe', [DashboardController::class, 'subscribe'])->name('user_hosting.billing.subscribe');
        Route::delete('user/hosting/projects/{hashid}/delete', [DashboardController::class, 'deleteProject'])->name('user_hosting.destroy');
        Route::patch('user/hosting/projects/{hashid}/settings', [DashboardController::class, 'updateSettings'])->name('user_hosting.settings.update');
        Route::post('user/hosting/projects/{hashid}/team', [DashboardController::class, 'inviteTeamMember'])->name('user_hosting.team.invite');
        Route::delete('user/hosting/projects/{hashid}/team/{user_id}', [DashboardController::class, 'removeTeamMember'])->name('user_hosting.team.remove');
        Route::post('user/hosting/projects/{hashid}/dev/start', [DashboardController::class, 'startDevServer'])->name('user_hosting.dev.start');
        Route::post('user/hosting/projects/{hashid}/dev/stop', [DashboardController::class, 'stopDevServer'])->name('user_hosting.dev.stop');
        Route::post('user/hosting/projects/{hashid}/staging', [DashboardController::class, 'createStaging'])->name('user_hosting.staging.create');
        Route::post('user/hosting/projects/{hashid}/domains', [DomainController::class, 'store'])->name('user_hosting.domains.store');
        Route::post('user/hosting/domains/{hashid}/ssl', [DomainController::class, 'requestSsl'])->name('user_hosting.domains.ssl');
        Route::delete('user/hosting/domains/{hashid}', [DomainController::class, 'destroy'])->name('user_hosting.domains.destroy');
        Route::post('user/hosting/projects/{hashid}/crons', [CronController::class, 'store'])->name('user_hosting.crons.store');
        Route::delete('user/hosting/crons/{hashid}', [CronController::class, 'destroy'])->name('user_hosting.crons.destroy');
        Route::get('user/hosting/docs', [DashboardController::class, 'docs'])->name('user_hosting.docs');

        // Backup & Restore
        Route::get('user/hosting/projects/{hashid}/backup', [DashboardController::class, 'downloadBackup'])->name('user_hosting.backup.download');
        Route::post('user/hosting/projects/{hashid}/restore', [DashboardController::class, 'uploadBackup'])->name('user_hosting.backup.upload');

        // Env Manager
        Route::get('user/hosting/projects/{hashid}/env', [DashboardController::class, 'editEnv'])->name('user_hosting.env.edit');
        Route::put('user/hosting/projects/{hashid}/env', [DashboardController::class, 'updateEnv'])->name('user_hosting.env.update');

        // Tiket Bantuan (User)
        Route::get('user/hosting/tickets', [\App\Http\Controllers\User\TicketController::class, 'index'])->name('user_hosting.tickets.index');
        Route::get('user/hosting/tickets/create', [\App\Http\Controllers\User\TicketController::class, 'create'])->name('user_hosting.tickets.create');
        Route::post('user/hosting/tickets', [\App\Http\Controllers\User\TicketController::class, 'store'])->name('user_hosting.tickets.store');
        Route::get('user/hosting/tickets/{hashid}', [\App\Http\Controllers\User\TicketController::class, 'show'])->name('user_hosting.tickets.show');
        Route::post('user/hosting/tickets/{hashid}/reply', [\App\Http\Controllers\User\TicketController::class, 'reply'])->name('user_hosting.tickets.reply');

    });

    // ═══════════════════════════════════════════════════════════════
    // USER JOKI (+ admin_joki, superadmin)
    // ═══════════════════════════════════════════════════════════════
    Route::middleware(['role:user_joki,admin_joki,superadmin', 'verified'])->group(function () {
        Route::get('user/joki/dashboard', [UserJokiDashboardController::class, 'index'])->name('user_joki.dashboard');
        Route::get('user/joki/create', [UserJokiDashboardController::class, 'create'])->name('user_joki.create');
        Route::post('user/joki/store', [UserJokiDashboardController::class, 'store'])->name('user_joki.store');
        Route::get('user/joki/progress', [ProgressController::class, 'index'])->name('user_joki.progress');
        Route::get('user/joki/riwayat', [RiwayatController::class, 'index'])->name('user_joki.riwayat');
        Route::get('user/joki/detail/{hashid}', [UserJokiDashboardController::class, 'detail'])->name('user_joki.detail');
        Route::get('user/joki/orders/{hashid}/chat', [\App\Http\Controllers\Joki\ChatController::class, 'fetchMessages'])->name('user_joki.chat.fetch');
        Route::post('user/joki/orders/{hashid}/chat', [\App\Http\Controllers\Joki\ChatController::class, 'sendMessage'])->name('user_joki.chat.store');
        Route::post('user/joki/orders/payment/{hashid}/proof', [UserJokiDashboardController::class, 'uploadPaymentProof'])->name('user_joki.payment.proof');
        Route::post('user/joki/orders/{hashid}/revision', [UserJokiDashboardController::class, 'requestRevision'])->name('user_joki.revision.store');
        Route::post('user/joki/orders/{hashid}/review', [UserJokiDashboardController::class, 'submitReview'])->name('user_joki.review.store');
        Route::post('user/joki/orders/{hashid}/deploy-hosting', [UserJokiDashboardController::class, 'deployToHosting'])->name('user_joki.deploy_hosting');
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
        Route::post('admin/joki/orders/{hashid}/portfolio', [JokiAdminDashboardController::class, 'pushToPortfolio'])->name('admin_joki.orders.portfolio');
        Route::get('admin/joki/orders/{hashid}/chat', [\App\Http\Controllers\Joki\ChatController::class, 'fetchMessages'])->name('admin_joki.chat.fetch');
        Route::post('admin/joki/orders/{hashid}/chat', [\App\Http\Controllers\Joki\ChatController::class, 'sendMessage'])->name('admin_joki.chat.store');
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
