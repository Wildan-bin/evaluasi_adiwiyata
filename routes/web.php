<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;

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

// Route::post('/upload-file', [\App\Http\Controllers\Example\UploadController::class, 'store']);
Route::get('/upload', fn() => Inertia::render('Example/UploadFile'));

// ============================================================================
// FORM SUBMISSION ROUTES - Protected by auth middleware
// ============================================================================
Route::middleware('auth')->prefix('form')->name('form.')->group(function () {
    // A5 - Rencana (Perencanaan & Evaluasi)
    Route::post('/save-a5', [\App\Http\Controllers\FormSubmissionController::class, 'saveA5'])->name('save-a5');
    Route::get('/get-a5', [\App\Http\Controllers\FormSubmissionController::class, 'getA5'])->name('get-a5');

    // A6 - Bukti Self Assessment
    Route::post('/save-a6', [\App\Http\Controllers\FormSubmissionController::class, 'saveA6'])->name('save-a6');
    Route::get('/get-a6', [\App\Http\Controllers\FormSubmissionController::class, 'getA6'])->name('get-a6');

    // A7 - Pendampingan
    Route::post('/save-a7', [\App\Http\Controllers\FormSubmissionController::class, 'saveA7'])->name('save-a7');
    Route::get('/get-a7', [\App\Http\Controllers\FormSubmissionController::class, 'getA7'])->name('get-a7');

    // A8 - Pernyataan & Persetujuan
    Route::post('/save-a8', [\App\Http\Controllers\FormSubmissionController::class, 'saveA8'])->name('save-a8');
    Route::get('/get-a8', [\App\Http\Controllers\FormSubmissionController::class, 'getA8'])->name('get-a8');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
