<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

require __DIR__.'/auth.php';
