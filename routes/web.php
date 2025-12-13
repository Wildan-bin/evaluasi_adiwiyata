<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\FileEvidenceController;
use App\Http\Controllers\FormSubmissionController;

// CSRF Token Refresh Route (untuk SPA)
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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/administration', function () {
    return Inertia::render('Dashboard');
})->name('administration');

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

    // Submission routes
    Route::get('/form', function () {
        return Inertia::render('Features/Form', [
            'user' => Auth::user(),
        ]);
    })->name('form');

    // Form Submission Routes - A5, A6, A7, A8
    Route::post('/form/save-a5', [FormSubmissionController::class, 'saveA5'])->name('form.save-a5');
    Route::get('/form/get-a5', [FormSubmissionController::class, 'getA5'])->name('form.get-a5');

    Route::post('/form/save-a6', [FormSubmissionController::class, 'saveA6'])->name('form.save-a6');
    Route::get('/form/get-a6', [FormSubmissionController::class, 'getA6'])->name('form.get-a6');

    Route::post('/form/save-a7', [FormSubmissionController::class, 'saveA7'])->name('form.save-a7');
    Route::get('/form/get-a7', [FormSubmissionController::class, 'getA7'])->name('form.get-a7');

    Route::post('/form/save-a8', [FormSubmissionController::class, 'saveA8'])->name('form.save-a8');
    Route::get('/form/get-a8', [FormSubmissionController::class, 'getA8'])->name('form.get-a8');

    // Submission routes
    Route::post('/submission/save-draft-a5', [SubmissionController::class, 'saveDraftA5'])
        ->name('submission.save-draft-a5');

    Route::get('/submission/draft-a5', [SubmissionController::class, 'getDraftA5'])
        ->name('submission.get-draft-a5');

    Route::post('/submission/store', [SubmissionController::class, 'store'])->name('submission.store'); // masih belum tau apakah akan digunakan
    Route::post('/submission/draft', [SubmissionController::class, 'saveDraft'])->name('submission.draft'); // masih belum tau apakah akan digunakan

    // File Evidence routes - TAMBAHAN UNTUK PREVIEW
    Route::get('/file-evidence/{id}/preview', [FileEvidenceController::class, 'preview'])->name('file-evidence.preview');
    Route::get('/file-evidence/{id}/download', [FileEvidenceController::class, 'download'])->name('file-evidence.download');

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
        $path = storage_path('app/submissions/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    })->name('test.preview');
});

require __DIR__ . '/auth.php';
