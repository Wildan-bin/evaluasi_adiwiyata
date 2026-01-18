<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdministrasiSekolahController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\FileEvidenceController;
use App\Models\File;

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Profile/DashboardUser');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/new-dashboard', function () {
    return Inertia::render('Dashboard');
})->name('new-dashboard');

Route::get('/dashboard-admin', function () {
    return Inertia::render('Profile/DashboardAdmin');
})->middleware(['auth', 'verified'])->name('dashboard-admin');

Route::get('/informasi', function () {
    return Inertia::render('Profile/PageInformasi');
})->name('informasi');

Route::get('/administrasi', function () {
    return Inertia::render('Profile/AdministrasiSekolah');
})->name('administrasi');

Route::get('/form', function () {
    return Inertia::render('Features/Form');
})->name('form');

Route::get('/evaluation', function () {
    return Inertia::render('Features/Evaluation');
})->name('evaluation');

// ROUTE UNTUK CONTOH KOMPONEN

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

// Rute untuk menangani pengiriman formulir
Route::post('/form-submission', [FormSubmissionController::class, 'store'])->name('form.submission');

// Rute untuk mengunggah file bukti
Route::post('/file-evidence', [FileEvidenceController::class, 'store'])->name('file.evidence');

Route::get('/files', [FileUploadController::class, 'index'])->name('files.index');
Route::get('/files/{id}', [FileUploadController::class, 'show'])->name('files.show');
Route::delete('/files/{id}', [FileUploadController::class, 'destroy'])->name('files.destroy');

Route::post('/upload', [FileUploadController::class, 'store'])->name('upload');

require __DIR__.'/auth.php';