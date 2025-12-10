<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Submission;

Route::middleware('auth:sanctum')->get('/submissions-with-files', function (Request $request) {
    $submissions = Submission::with(['user.school', 'fileEvidences'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'submissions' => $submissions
    ]);
});