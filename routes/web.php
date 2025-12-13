<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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

Route::post('/submission/draft', function () {
    return response()->json(['success' => true, 'message' => 'Draft saved']);
})->name('submission.draft');

Route::post('/submission/submit', function () {
    return response()->json(['success' => true, 'message' => 'Submission saved']);
})->name('submission.submit');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
