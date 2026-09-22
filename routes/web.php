<?php

use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\WebCertificateController;
use App\Http\Controllers\WebDashboardController;
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
    return view('welcome');
})->name('home');

// ─── Guest Only (Login & Register) ──────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('register.submit');

    // Reset Password
    Route::get('/forgot-password', [\App\Http\Controllers\WebPasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\WebPasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\WebPasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\WebPasswordResetController::class, 'update'])->name('password.update');
});

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─── Authenticated Dashboard ────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [WebDashboardController::class, 'index'])->name('dashboard');

    // Portfolio
    Route::get('/dashboard/portfolios', [WebPortfolioController::class, 'index'])->name('portfolios.index');
    Route::post('/dashboard/portfolios', [WebPortfolioController::class, 'store'])->name('portfolios.store');
    Route::put('/dashboard/portfolios/{id}', [WebPortfolioController::class, 'update'])->name('portfolios.update');
    Route::delete('/dashboard/portfolios/{id}', [WebPortfolioController::class, 'destroy'])->name('portfolios.destroy');
    Route::post('/dashboard/portfolios/{id}/thumbnail', [WebPortfolioController::class, 'uploadThumbnail'])->name('portfolios.thumbnail');
    Route::post('/dashboard/portfolios/{id}/images', [WebPortfolioController::class, 'storeImages'])->name('portfolios.images.store');
    Route::delete('/dashboard/portfolios/{id}/images/{imageId}', [WebPortfolioController::class, 'destroyImage'])->name('portfolios.images.destroy');

    // Skills
    Route::get('/dashboard/skills', [WebSkillController::class, 'index'])->name('skills.index');
    Route::post('/dashboard/skills', [WebSkillController::class, 'store'])->name('skills.store');
    Route::delete('/dashboard/skills/{id}', [WebSkillController::class, 'destroy'])->name('skills.destroy');

    // Certificates
    Route::get('/dashboard/certificates', [WebCertificateController::class, 'index'])->name('certificates.index');
    Route::post('/dashboard/certificates', [WebCertificateController::class, 'store'])->name('certificates.store');
    Route::delete('/dashboard/certificates/{id}', [WebCertificateController::class, 'destroy'])->name('certificates.destroy');

    // Settings
    Route::get('/dashboard/settings', [WebSettingsController::class, 'index'])->name('settings.index');
    Route::post('/dashboard/settings', [WebSettingsController::class, 'update'])->name('settings.update');
    Route::post('/dashboard/settings/photo', [WebSettingsController::class, 'uploadPhoto'])->name('settings.photo');
    Route::post('/dashboard/settings/social', [WebSettingsController::class, 'storeSocialLink'])->name('settings.social.store');
    Route::delete('/dashboard/settings/social/{id}', [WebSettingsController::class, 'destroySocialLink'])->name('settings.social.destroy');
});

// ─── Admin Panel ─────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
});

// ─── Public Portfolio Page ───────────────────────
// PENTING: harus di baris PALING BAWAH agar tidak menangkap /login, /register, dll.
Route::get('/{username}', [WebPublicController::class, 'show'])->name('public.show');
