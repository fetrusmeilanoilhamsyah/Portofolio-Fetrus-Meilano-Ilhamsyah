<?php

use App\Http\Controllers\CommandPaletteController;
use App\Http\Controllers\PublicController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
|
| Bahasa Indonesia: default, tanpa prefix (/about, /projects, dll.)
| Bahasa Inggris: dengan prefix /en (/en/about, /en/projects, dll.)
|
*/

Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');
    Route::get('/robots.txt', [PublicController::class, 'robots'])->name('robots');

    // ─── Indonesia (default, tanpa prefix) ───────────────────────────────────────
    Route::middleware([SetLocale::class])->group(function () {
        Route::get('/', [PublicController::class, 'home'])->name('home');
        Route::get('/about', [PublicController::class, 'about'])->name('about');
        Route::get('/experience', [PublicController::class, 'experience'])->name('experience');
        Route::get('/projects', [PublicController::class, 'projects'])->name('projects');
        Route::get('/projects/{slug}', [PublicController::class, 'projectShow'])->name('projects.show');
        Route::get('/social', [PublicController::class, 'social'])->name('social');
        Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
        Route::get('/cv', [PublicController::class, 'cv'])->name('cv');

        // API endpoint for Command Palette
        Route::get('/api/command-palette', [CommandPaletteController::class, 'index'])->name('command-palette');
    });

    // ─── Inggris (prefix /en) ─────────────────────────────────────────────────────
    Route::prefix('en')->middleware([SetLocale::class])->group(function () {
        Route::get('/', [PublicController::class, 'home'])->name('en.home');
        Route::get('/about', [PublicController::class, 'about'])->name('en.about');
        Route::get('/experience', [PublicController::class, 'experience'])->name('en.experience');
        Route::get('/projects', [PublicController::class, 'projects'])->name('en.projects');
        Route::get('/projects/{slug}', [PublicController::class, 'projectShow'])->name('en.projects.show');
        Route::get('/social', [PublicController::class, 'social'])->name('en.social');
        Route::get('/contact', [PublicController::class, 'contact'])->name('en.contact');
        Route::get('/cv', [PublicController::class, 'cv'])->name('en.cv');

        // API endpoint for Command Palette
        Route::get('/api/command-palette', [CommandPaletteController::class, 'index'])->name('en.command-palette');
    });
});
