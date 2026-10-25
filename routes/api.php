<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentCallbackController;

// Rute ini otomatis akan memiliki awalan /api/ di depannya
Route::post('pakasir/webhook', [PaymentCallbackController::class, 'handleWebhook'])
    ->middleware('throttle:10,1'); // Maksimal 10 request per menit untuk mencegah bruteforce

// Supabase-like Database API
Route::any('/v1/db/{hashid}/{path?}', [\App\Http\Controllers\Api\DatabaseApiController::class, 'handle'])
    ->where('path', '.*');

// Tunnel Relay & Response API
Route::any('/tunnel/relay', [\App\Http\Controllers\Hosting\User\TunnelManagerController::class, 'relay']);
Route::post('/tunnel/response', [\App\Http\Controllers\Hosting\User\TunnelManagerController::class, 'response']);
Route::post('/tunnel/heartbeat', [\App\Http\Controllers\Hosting\User\TunnelManagerController::class, 'heartbeat']);
Route::post('/tunnel/fetch', [\App\Http\Controllers\Hosting\User\TunnelManagerController::class, 'fetchRequest']);

// AI Consultation API
Route::post('/consultation/chat', [\App\Http\Controllers\Api\ConsultationController::class, 'chat'])->middleware('throttle:20,1');
Route::get('/consultation/history', [\App\Http\Controllers\Api\ConsultationController::class, 'history']);

// ── PORTFOLIO REST API (v1) ─────────────────────────────────────
$portfolioApi = \App\Http\Controllers\Api\PortfolioApiController::class;

// Token superadmin untuk mengakses admin API
Route::post('/v1/auth/token', [$portfolioApi, 'issueToken'])->middleware('throttle:10,1');

// Publik (hanya data aktif, tanpa file sensitif)
Route::prefix('/v1/portfolio')->middleware('throttle:60,1')->group(function () use ($portfolioApi) {
    Route::get('/', [$portfolioApi, 'overview']);
    Route::get('/portfolios', [$portfolioApi, 'indexPortfolios']);
    Route::get('/educations', [$portfolioApi, 'indexEducations']);
    Route::get('/experiences', [$portfolioApi, 'indexExperiences']);
    Route::get('/skills', [$portfolioApi, 'indexSkills']);
    Route::get('/testimonials', [$portfolioApi, 'indexTestimonials']);
    Route::get('/certifications', [$portfolioApi, 'indexCertifications']);
    Route::get('/digital-products', [$portfolioApi, 'indexDigitalProducts']);
    Route::post('/contact', [$portfolioApi, 'sendContact'])->middleware('throttle:10,1');
});

// Admin full-CRUD (wajib token sanctum + role superadmin)
Route::prefix('/v1/admin')->middleware(['auth:sanctum', 'role:superadmin'])->group(function () use ($portfolioApi) {
    Route::get('/contact-messages', [$portfolioApi, 'indexContactMessages'])->name('api.admin.contact_messages.index');
    Route::get('/contact-messages/{hashid}', [$portfolioApi, 'showContactMessage'])->name('api.admin.contact_messages.show');
    Route::delete('/contact-messages/{hashid}', [$portfolioApi, 'destroyContactMessage'])->name('api.admin.contact_messages.destroy');

    // Skill & Tech
    Route::get('/skills', [$portfolioApi, 'indexSkillGroups'])->name('api.admin.skills.index');
    Route::post('/skills/groups', [$portfolioApi, 'storeSkillGroup'])->name('api.admin.skills.groups.store');
    Route::put('/skills/groups/{hashid}', [$portfolioApi, 'updateSkillGroup'])->name('api.admin.skills.groups.update');
    Route::delete('/skills/groups/{hashid}', [$portfolioApi, 'destroySkillGroup'])->name('api.admin.skills.groups.destroy');
    Route::post('/skills/skills', [$portfolioApi, 'storeSkill'])->name('api.admin.skills.skills.store');
    Route::put('/skills/skills/{hashid}', [$portfolioApi, 'updateSkill'])->name('api.admin.skills.skills.update');
    Route::delete('/skills/skills/{hashid}', [$portfolioApi, 'destroySkill'])->name('api.admin.skills.skills.destroy');
    Route::get('/tech-badges', [$portfolioApi, 'indexTechBadges'])->name('api.admin.tech_badges.index');
    Route::post('/tech-badges', [$portfolioApi, 'storeTechBadge'])->name('api.admin.tech_badges.store');
    Route::delete('/tech-badges/{hashid}', [$portfolioApi, 'destroyTechBadge'])->name('api.admin.tech_badges.destroy');

    // Resource generik: portfolios | educations | experiences | testimonials | certifications | digital-products
    Route::get('/{resource}', [$portfolioApi, 'adminIndex'])->where('resource', '[a-z-]+')->name('api.admin.resource.index');
    Route::post('/{resource}', [$portfolioApi, 'adminStore'])->where('resource', '[a-z-]+')->name('api.admin.resource.store');
    Route::put('/{resource}/{hashid}', [$portfolioApi, 'adminUpdate'])->where('resource', '[a-z-]+')->name('api.admin.resource.update');
    Route::delete('/{resource}/{hashid}', [$portfolioApi, 'adminDestroy'])->where('resource', '[a-z-]+')->name('api.admin.resource.destroy');
    Route::patch('/{resource}/{hashid}/status', [$portfolioApi, 'adminToggleStatus'])->where('resource', '[a-z-]+')->name('api.admin.resource.status');
});

