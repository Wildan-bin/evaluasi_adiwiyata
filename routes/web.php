<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    // Redirect ke dashboard jika sudah login
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Dashboard', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    $user = Auth::user();

    // Redirect berdasarkan role
    if ($user->role === 'admin') {
        $users = \App\Models\User::all()->groupBy('role');

        // Fetch administrasi sekolah data with user relationship
        $administrasiSekolah = \App\Models\AdministrasiSekolah::with('user')
            ->get()
            ->map(function ($adm) {
                return [
                    'id' => $adm->id,
                    'user_id' => $adm->user_id,
                    'name' => $adm->user->name ?? 'Unknown',
                    'npsn' => $adm->npsn ?? '-',
                    'nama_sekolah' => $adm->nama_sekolah ?? '-',
                    'rencana_evaluasi' => $adm->rencana_evaluasi ?? '-',
                    'self_assessment' => $adm->self_assessment ?? '-',
                    'kebutuhan_pendampingan' => $adm->kebutuhan_pendampingan ?? '-',
                    'pernyataan' => $adm->pernyataan ?? '-',
                ];
            });

        return Inertia::render('Profile/DashboardAdmin', [
            'admins' => $users->get('admin', collect())->values(),
            'users' => $users->get('user', collect())->values(),
            'mentors' => $users->get('mentor', collect())->values(),
            'administrasiSekolah' => $administrasiSekolah,
        ]);
    }

    return Inertia::render('Profile/DashboardUser');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/administrasi-sekolah', function () {
    return Inertia::render('Profile/AdministrasiSekolah', [
        'previewMode' => false,
        'administrasiData' => null
    ]);
})->middleware(['auth', 'verified'])->name('administrasi-sekolah');

// Preview route untuk admin
Route::get('/administrasi-sekolah/{userId}/preview', function ($userId) {
    // Get administrasi data by user_id
    $administrasi = \App\Models\AdministrasiSekolah::where('user_id', $userId)->first();

    return Inertia::render('Profile/AdministrasiSekolah', [
        'previewMode' => true,
        'administrasiData' => $administrasi
    ]);
})->middleware(['auth', 'verified'])->name('administrasi-preview');

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

    // Admin routes for user management
    Route::middleware('can:admin')->group(function () {
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
});

require __DIR__.'/auth.php';
