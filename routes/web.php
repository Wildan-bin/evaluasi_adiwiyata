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

    // ✅ ADMINISTRASI SEKOLAH ROUTES - HAPUS DUPLIKAT
    Route::prefix('administrasi-sekolah')->group(function () {
        // User routes
        Route::get('/', [AdministrasiSekolahController::class, 'create'])->name('administrasi-sekolah');
        Route::post('/', [AdministrasiSekolahController::class, 'store'])->name('administrasi-store');
        Route::get('/preview/{id}', [AdministrasiSekolahController::class, 'preview'])->name('administrasi-preview');
        Route::post('/submit/{id}', [AdministrasiSekolahController::class, 'submit'])->name('administrasi-submit');

        // Admin routes
        Route::get('/logs', [AdministrasiSekolahController::class, 'logs'])->name('administrasi-logs');
        Route::get('/logs/{id}', [AdministrasiSekolahController::class, 'logDetail'])->name('administrasi-log-detail');
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
