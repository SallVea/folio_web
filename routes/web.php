<?php

use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\WebCertificateController;
use App\Http\Controllers\WebDashboardController;
use App\Http\Controllers\WebEmailVerificationController;
use App\Http\Controllers\WebExportController;
use App\Http\Controllers\WebPortfolioController;
use App\Http\Controllers\WebPublicController;
use App\Http\Controllers\WebSettingsController;
use App\Http\Controllers\WebSkillController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Folio
|--------------------------------------------------------------------------
*/

// ─── Landing Page ───────────────────────────────
Route::get('/', function () {
    $exampleUser = \App\Models\User::where('is_active', true)
        ->whereHas('portfolios')
        ->withCount([
            'portfolios as portfolio_total',
            'portfolios as featured_total' => function ($query) {
                $query->where('is_featured', true);
            },
        ])
        ->orderByDesc('featured_total')
        ->orderByDesc('portfolio_total')
        ->orderByDesc('id')
        ->first();

    return view('welcome', [
        'stats' => [
            'users' => \App\Models\User::where('is_active', true)->count(),
            'portfolios' => \App\Models\Portfolio::count(),
        ],
        'exampleProfile' => $exampleUser?->username,
    ]);
})->name('home');

// ─── API Docs ───────────────────────────────────
Route::get('/api/docs', function () {
    return view('api-docs');
})->name('api.docs');

// ─── Guest Only (Login & Register) ──────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->middleware('throttle:5,1')->name('register.submit');

    // Reset Password
    Route::get('/forgot-password', [\App\Http\Controllers\WebPasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\WebPasswordResetController::class, 'email'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\WebPasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\WebPasswordResetController::class, 'update'])->name('password.update');

    // OAuth
    Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\WebOAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [\App\Http\Controllers\WebOAuthController::class, 'callback'])->name('oauth.callback');
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Email Verification
Route::get('/email/verify', [WebEmailVerificationController::class, 'notice'])
    ->middleware('auth')
    ->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [WebEmailVerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [WebEmailVerificationController::class, 'send'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ─── Authenticated Dashboard ────────────────────
Route::middleware(['auth', 'verified', 'active'])->group(function () {

    Route::get('/dashboard', [WebDashboardController::class, 'index'])->name('dashboard');

    // Portfolio
    Route::get('/dashboard/portfolios', [WebPortfolioController::class, 'index'])->name('portfolios.index');
    Route::post('/dashboard/portfolios', [WebPortfolioController::class, 'store'])->name('portfolios.store');
    Route::post('/dashboard/portfolios/reorder', [WebPortfolioController::class, 'reorder'])->name('portfolios.reorder');
    Route::put('/dashboard/portfolios/{id}', [WebPortfolioController::class, 'update'])->name('portfolios.update');
    Route::delete('/dashboard/portfolios/{id}', [WebPortfolioController::class, 'destroy'])->name('portfolios.destroy');
    Route::post('/dashboard/portfolios/{id}/duplicate', [WebPortfolioController::class, 'duplicate'])->name('portfolios.duplicate');
    Route::post('/dashboard/portfolios/{id}/thumbnail', [WebPortfolioController::class, 'uploadThumbnail'])->name('portfolios.thumbnail');
    Route::post('/dashboard/portfolios/{id}/images', [WebPortfolioController::class, 'storeImages'])->name('portfolios.images.store');
    Route::delete('/dashboard/portfolios/{id}/images/{imageId}', [WebPortfolioController::class, 'destroyImage'])->name('portfolios.images.destroy');

    // Skills
    Route::get('/dashboard/skills', [WebSkillController::class, 'index'])->name('skills.index');
    Route::post('/dashboard/skills', [WebSkillController::class, 'store'])->name('skills.store');
    Route::put('/dashboard/skills/{id}', [WebSkillController::class, 'update'])->name('skills.update');
    Route::post('/dashboard/skills/{id}/move', [WebSkillController::class, 'move'])->name('skills.move');
    Route::post('/dashboard/skills/reorder', [WebSkillController::class, 'reorder'])->name('skills.reorder');
    Route::get('/dashboard/skills/export', [WebSkillController::class, 'export'])->name('skills.export');
    Route::post('/dashboard/skills/import', [WebSkillController::class, 'import'])->name('skills.import');
    Route::delete('/dashboard/skills/{id}', [WebSkillController::class, 'destroy'])->name('skills.destroy');

    // Certificates
    Route::get('/dashboard/certificates', [WebCertificateController::class, 'index'])->name('certificates.index');
    Route::post('/dashboard/certificates', [WebCertificateController::class, 'store'])->name('certificates.store');
    Route::put('/dashboard/certificates/{id}', [WebCertificateController::class, 'update'])->name('certificates.update');
    Route::post('/dashboard/certificates/{id}/proof', [WebCertificateController::class, 'uploadProof'])->name('certificates.proof');
    Route::post('/dashboard/certificates/{id}/verify', [WebCertificateController::class, 'verifyCredential'])->name('certificates.verify');
    Route::delete('/dashboard/certificates/{id}', [WebCertificateController::class, 'destroy'])->name('certificates.destroy');

    // Settings
    Route::get('/dashboard/settings', [WebSettingsController::class, 'index'])->name('settings.index');
    Route::post('/dashboard/settings', [WebSettingsController::class, 'update'])->name('settings.update');
    Route::post('/dashboard/settings/photo', [WebSettingsController::class, 'uploadPhoto'])->name('settings.photo');
    Route::patch('/dashboard/settings/username', [WebSettingsController::class, 'updateUsername'])->name('settings.username');
    Route::get('/dashboard/settings/username-check', [WebSettingsController::class, 'checkUsername'])->name('settings.username.check');
    Route::delete('/dashboard/settings/account', [WebSettingsController::class, 'destroyAccount'])->name('settings.account.destroy');
    Route::post('/dashboard/settings/social', [WebSettingsController::class, 'storeSocialLink'])->name('settings.social.store');
    Route::delete('/dashboard/settings/social/{id}', [WebSettingsController::class, 'destroySocialLink'])->name('settings.social.destroy');

    // Export data
    Route::get('/dashboard/export', [WebExportController::class, 'export'])->name('dashboard.export');

    // Notifications
    Route::get('/dashboard/notifications', [\App\Http\Controllers\WebNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/dashboard/notifications/read-all', [\App\Http\Controllers\WebNotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/dashboard/notifications/{id}/read', [\App\Http\Controllers\WebNotificationController::class, 'read'])->name('notifications.read');
});

// ─── Admin Panel ─────────────────────────────────
Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}', [\App\Http\Controllers\AdminController::class, 'showUser'])->name('admin.users.show');
    Route::patch('/users/{id}/toggle', [\App\Http\Controllers\AdminController::class, 'toggleActive'])->name('admin.users.toggle');
    Route::patch('/users/{id}/role', [\App\Http\Controllers\AdminController::class, 'updateRole'])->name('admin.users.role');
    Route::get('/audit-logs', [\App\Http\Controllers\AdminController::class, 'auditLogs'])->name('admin.audit');
});

// ─── Public vCard Download ──────────────────────
Route::get('/{username}/vcard', [WebPublicController::class, 'vcard'])->name('public.vcard');

// ─── Public Portfolio Page ───────────────────────
// PENTING: harus di baris PALING BAWAH agar tidak menangkap /login, /register, dll.
Route::get('/{username}', [WebPublicController::class, 'show'])->name('public.show');
