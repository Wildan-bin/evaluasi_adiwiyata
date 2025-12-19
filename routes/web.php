<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdministrasiSekolahController;
use App\Http\Controllers\FileUploadController;

// CSRF Token Refresh Route (untuk SPA)
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

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

    return Inertia::render('Profile/DashboardUser', [
        'user' => $user,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/form', function () {
    return Inertia::render('Features/Form');
})->name('form');

Route::get('/evaluation', function () {
    return Inertia::render('Features/Evaluation');
})->name('evaluation');

Route::get('/administration', function () {
    return Inertia::render('Features/Administration');
})->middleware(['auth'])->name('administration');

Route::get('/admin-test', function () {
    return Inertia::render('Features/Admin/AdminTest');
})->middleware(['auth'])->name('admin.test');

// ROUTE UNTUK CONTOH KOMPONEN
Route::get('/cards', function () {
    return Inertia::render('Example/Components-cards');
})->name('component-card');

Route::get('/headers', function () {
    return Inertia::render('Example/Components-headers');
})->name('component-header');

// Admin test page
Route::get('/admin/test', function () {
    return Inertia::render('Features/Admin/AdminTest');
})->name('admin.test');

// Test file preview route
Route::get('/test/file-preview', function () {
    return Inertia::render('Features/Admin/FilePreviewTest');
})->name('test.file-preview');

// File viewer route
Route::get('/test/preview/{filename}', function ($filename) {
    // Prevent directory traversal
    $filename = basename($filename);
    $path = 'submissions/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404, 'File tidak ditemukan');
    }

    $fullPath = Storage::disk('public')->path($path);

    return response()->file($fullPath);
})->name('test.preview');

// ============================================================================
// ADMIN TEST SUPPORT API (JSON list of submissions + files)
// ============================================================================
Route::middleware('auth')->get('/api/submissions-with-files', function () {
    $user = Auth::user();
    abort_if(!$user || $user->role !== 'admin', 403);

    // Collect submissions based on existing data per user
    $users = \App\Models\User::select('id', 'name')->get();

    $makeId = function ($userId, $path) {
        $norm = ltrim($path, '/');
        if (str_starts_with($norm, 'public/')) $norm = substr($norm, 7);
        if (str_starts_with($norm, 'storage/')) $norm = substr($norm, 8);
        // url-safe base64 of "userId|path"
        $raw = base64_encode($userId . '|' . $norm);
        return rtrim(strtr($raw, '+/', '-_'), '=');
    };

    $decodeId = function ($id) {
        $raw = base64_decode(strtr($id, '-_', '+/') . str_repeat('=', (4 - strlen($id) % 4) % 4));
        [$uid, $path] = explode('|', $raw, 2);
        return [(int) $uid, $path];
    };

    $submissions = [];
    foreach ($users as $u) {
        // Gather files from each feature
        $files = [];

        // Rencana
        $rencana = \App\Models\Rencana::where('user_id', $u->id)->get();
        foreach ($rencana as $r) {
            $path = ltrim($r->path_file ?? '', '/');
            if ($path) {
                if (str_starts_with($path, 'public/')) $path = substr($path, 7);
                if (str_starts_with($path, 'storage/')) $path = substr($path, 8);
                $size = Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0;
                $files[] = [
                    'id' => $makeId($u->id, $path),
                    'original_name' => basename($path),
                    'file_size' => $size,
                    'field_name' => $r->indikator ?? 'Rencana',
                    'uploaded_at' => $r->created_at,
                ];
            }
        }

        // Bukti Self Assessment
        $bukti = \App\Models\BuktiSelfAssessment::where('user_id', $u->id)->get();
        foreach ($bukti as $b) {
            $path = ltrim($b->path_file ?? '', '/');
            if ($path) {
                if (str_starts_with($path, 'public/')) $path = substr($path, 7);
                if (str_starts_with($path, 'storage/')) $path = substr($path, 8);
                $size = Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0;
                $files[] = [
                    'id' => $makeId($u->id, $path),
                    'original_name' => basename($path),
                    'file_size' => $size,
                    'field_name' => $b->indikator ?? 'Self Assessment',
                    'uploaded_at' => $b->created_at,
                ];
            }
        }

        // Pernyataan (bukti_persetujuan)
        $pernyataan = \App\Models\Pernyataan::where('user_id', $u->id)->first();
        if ($pernyataan && $pernyataan->bukti_persetujuan) {
            $path = ltrim($pernyataan->bukti_persetujuan, '/');
            if (str_starts_with($path, 'public/')) $path = substr($path, 7);
            if (str_starts_with($path, 'storage/')) $path = substr($path, 8);
            $size = Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0;
            $files[] = [
                'id' => $makeId($u->id, $path),
                'original_name' => basename($path),
                'file_size' => $size,
                'field_name' => 'Bukti Persetujuan',
                'uploaded_at' => $pernyataan->created_at,
            ];
        }

        if (!empty($files)) {
            $submissions[] = [
                'id' => $u->id,
                'user' => [
                    'name' => $u->name,
                    'school' => ['name' => ''],
                ],
                'status' => 'completed',
                'submitted_at' => now(),
                'ketua_tim' => '',
                'jumlah_kader_adiwiyata' => 0,
                'file_evidences' => $files,
            ];
        }
    }

    return response()->json([
        'submissions' => $submissions,
    ]);
});

// Preview/Download file evidence by encoded id
Route::middleware('auth')->get('/file-evidence/{eid}/preview', function ($eid) {
    $user = Auth::user();
    abort_if(!$user || $user->role !== 'admin', 403);

    // Decode file path from encrypted ID
    $decoded = strtr($eid, '-_', '+/');
    $decoded .= str_repeat('=', (4 - strlen($decoded) % 4) % 4);
    @[$uid, $path] = explode('|', base64_decode($decoded, true), 2);

    if (!$path) abort(404);

    // Normalize path
    $path = ltrim($path, '/');
    if (str_starts_with($path, 'public/')) $path = substr($path, 7);
    if (str_starts_with($path, 'storage/')) $path = substr($path, 8);

    if (!Storage::disk('public')->exists($path)) abort(404);

    return response()->file(Storage::disk('public')->path($path));
})->name('file-evidence.preview');

Route::middleware('auth')->get('/file-evidence/{eid}/download', function ($eid) {
    $user = Auth::user();
    abort_if(!$user || $user->role !== 'admin', 403);

    // Decode file path from encrypted ID
    $decoded = strtr($eid, '-_', '+/');
    $decoded .= str_repeat('=', (4 - strlen($decoded) % 4) % 4);
    @[$uid, $path] = explode('|', base64_decode($decoded, true), 2);

    if (!$path) abort(404);

    // Normalize path
    $path = ltrim($path, '/');
    if (str_starts_with($path, 'public/')) $path = substr($path, 7);
    if (str_starts_with($path, 'storage/')) $path = substr($path, 8);

    if (!Storage::disk('public')->exists($path)) abort(404);

    return response()->download(Storage::disk('public')->path($path), basename($path));
})->name('file-evidence.download');


Route::get('/upload', fn() => Inertia::render('Example/UploadFile'));

// ============================================================================
// FILE UPLOAD DEMO PAGES
// ============================================================================
Route::middleware('auth')->group(function () {
    // User file upload page with step-by-step interface
    Route::get('/file-upload', function () {
        return Inertia::render('Features/UserFileUpload');
    })->name('file-upload');

    // Admin file management page
    Route::middleware('role:admin,mentor')->get('/admin/file-management', function () {
        return Inertia::render('Features/Admin/AdminFileManagement');
    })->name('admin.file-management');
});

// ============================================================================
// SUBMISSION STEP ROUTES (untuk navigation)
// ============================================================================
Route::middleware('auth')->prefix('submission')->name('submission.')->group(function () {
    Route::get('/a5', function () {
        return Inertia::render('Features/Form');
    })->name('a5');

    Route::get('/a6', function () {
        return Inertia::render('Features/Form');
    })->name('a6');

    Route::get('/a7', function () {
        return Inertia::render('Features/Form');
    })->name('a7');

    Route::get('/a8', function () {
        return Inertia::render('Features/Form');
    })->name('a8');
});

// ============================================================================
// FORM SUBMISSION API ROUTES - Protected by auth middleware
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

// ============================================================================
// ADMINISTRASI SEKOLAH ROUTES
// ============================================================================
// User create/view page and submit
Route::middleware('auth')->group(function () {
    // Halaman form administrasi (role enforced in controller)
    Route::get('/administrasi-sekolah', [AdministrasiSekolahController::class, 'create'])->name('administrasi-sekolah');
    // Submit form administrasi (role enforced in controller)
    Route::post('/administrasi-sekolah', [AdministrasiSekolahController::class, 'store'])->name('administrasi-sekolah.store');
});

// Grouped routes with prefix for preview and admin actions
Route::middleware('auth')->prefix('administrasi-sekolah')->name('administrasi.')->group(function () {
    // Preview dokumen administrasi sekolah
    Route::get('/{id}/preview', [AdministrasiSekolahController::class, 'preview'])->name('preview');

    // Admin pages/actions (role enforced in controller methods)
    Route::get('/submissions', [AdministrasiSekolahController::class, 'submissions'])->name('submissions');
    Route::patch('/{id}/verify', [AdministrasiSekolahController::class, 'verify'])->name('verify');
    Route::patch('/{id}/note', [AdministrasiSekolahController::class, 'updateNote'])->name('note');
    Route::patch('/{id}/unlock', [AdministrasiSekolahController::class, 'unlockForEdit'])->name('unlock');

    // Secure inline file preview for admin/owner
    Route::get('/{id}/file', [AdministrasiSekolahController::class, 'streamFile'])->name('file');
});

// ============================================================================
// FILE UPLOAD ROUTES - New comprehensive file upload system
// ============================================================================
Route::middleware('auth')->prefix('api/file-upload')->name('file-upload.')->group(function () {
    // User Routes - Upload & Manage Own Files
    Route::post('/', [FileUploadController::class, 'upload'])->name('upload');
    Route::get('/user', [FileUploadController::class, 'getUserFiles'])->name('user');
    Route::get('/{id}/preview', [FileUploadController::class, 'preview'])->name('preview');
    Route::get('/{id}/download', [FileUploadController::class, 'download'])->name('download');
    Route::delete('/{id}', [FileUploadController::class, 'delete'])->name('delete');

    // Admin/Mentor Routes - Review & Manage All Files
    Route::middleware('role:admin,mentor')->group(function () {
        Route::get('/pending', [FileUploadController::class, 'getPendingFiles'])->name('pending');
        Route::post('/{id}/review', [FileUploadController::class, 'review'])->name('review');
        Route::get('/user/{userId}', [FileUploadController::class, 'getFilesByUser'])->name('by-user');
        Route::get('/latest', [FileUploadController::class, 'getLatestUploads'])->name('latest');
    });
});

// Admin User Files Page
Route::middleware(['auth', 'role:admin,mentor'])->group(function () {
    Route::get('/admin/users/{userId}/files', function ($userId) {
        return Inertia::render('Features/Admin/UserFiles', [
            'userId' => $userId,
        ]);
    })->name('admin.user-files');

    // Get all users list for dropdown
    Route::get('/api/users', function () {
        $users = \App\Models\User::with('administrasiSekolah')
            ->where('role', '!=', 'admin')
            ->select('id', 'name', 'email', 'role')
            ->get();

        return response()->json(['data' => $users]);
    })->name('api.users');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
