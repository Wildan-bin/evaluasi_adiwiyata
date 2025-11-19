<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdministrasiSekolahController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// Dashboard dengan role-based logic
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return Inertia::render('Profile/DashboardAdmin');
        }

        return Inertia::render('Profile/DashboardUser');
    })->name('dashboard');

    // ✅ ADMINISTRASI SEKOLAH ROUTES - USER
    Route::prefix('administrasi-sekolah')->group(function () {
        // User - Form & Preview
        Route::get('/', [AdministrasiSekolahController::class, 'create'])->name('administrasi-sekolah');
        Route::post('/', [AdministrasiSekolahController::class, 'store'])->name('administrasi-store');
        Route::get('/preview/{id}', [AdministrasiSekolahController::class, 'preview'])->name('administrasi-preview');

        // User - Edit Request
        Route::post('/request-edit', [AdministrasiSekolahController::class, 'requestEdit'])->name('administrasi-request-edit');
        Route::post('/cancel-request-edit', [AdministrasiSekolahController::class, 'cancelRequestEdit'])->name('administrasi-cancel-request-edit');

        // Admin - Submissions List & Detail
        Route::get('/submissions', [AdministrasiSekolahController::class, 'submissions'])->name('administrasi-submissions');
        Route::get('/submissions/{id}', [AdministrasiSekolahController::class, 'showSubmission'])->name('administrasi-submission-detail');

        // Admin - Actions
        Route::patch('/{id}/verify', [AdministrasiSekolahController::class, 'verify'])->name('administrasi-verify');
        Route::patch('/{id}/note', [AdministrasiSekolahController::class, 'updateNote'])->name('administrasi-update-note');
        Route::patch('/{id}/unlock', [AdministrasiSekolahController::class, 'unlockForEdit'])->name('administrasi-unlock');
    });
});

Route::get('/form', function () {
    return Inertia::render('Features/Form');
})->name('form');

Route::get('/evaluation', function () {
    return Inertia::render('Features/Evaluation');
})->name('evaluation');

Route::get('/cards', function () {
    return Inertia::render('Example/Components-cards');
})->name('component-card');

Route::get('/headers', function () {
    return Inertia::render('Example/Components-headers');
})->name('component-header');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
