<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CertificateApiController;
use App\Http\Controllers\Api\PortfolioApiController;
use App\Http\Controllers\Api\PublicApiController;
use App\Http\Controllers\Api\SkillApiController;
use App\Http\Controllers\Api\SocialLinkApiController;
use App\Http\Controllers\Api\StatsApiController;
use App\Http\Controllers\Api\UserApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ─── Public (tanpa token) ───────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/login',    [AuthApiController::class, 'login']);
    });

    Route::get('/u/{username}',        [PublicApiController::class, 'show']);
    Route::post('/u/{username}/view',  [PublicApiController::class, 'recordView']);

    // ─── Protected (butuh Bearer Token) ────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::get('/auth/me',      [AuthApiController::class, 'me']);

        // Profile
        Route::get('/user/profile',  [UserApiController::class, 'profile']);
        Route::put('/user/profile',  [UserApiController::class, 'update']);
        Route::post('/user/photo',   [UserApiController::class, 'uploadPhoto']);

        // Portfolio
        Route::apiResource('portfolios', PortfolioApiController::class);
        Route::post('/portfolios/{id}/thumbnail', [PortfolioApiController::class, 'uploadThumbnail']);

        // Portfolio Gallery (multi-foto)
        Route::post('/portfolios/{id}/images', [PortfolioApiController::class, 'uploadImages']);
        Route::delete('/portfolios/{id}/images/{imageId}', [PortfolioApiController::class, 'deleteImage']);

        // Skills
        Route::apiResource('skills', SkillApiController::class)->except(['show']);

        // Certificates
        Route::apiResource('certificates', CertificateApiController::class)->except(['show']);

        // Social Links
        Route::apiResource('social-links', SocialLinkApiController::class)->except(['show']);

        // Stats
        Route::get('/stats', [StatsApiController::class, 'index']);
    });
});
