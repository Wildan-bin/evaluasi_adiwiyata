<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MentorAssignmentController;

// API routes dapat ditambahkan di sini jika diperlukan

// ============================================================================
// MENTOR ASSIGNMENT API ROUTES
// ============================================================================
Route::middleware(['auth:sanctum'])->prefix('mentor-assignment')->group(function () {
    // Get all schools ready for mentor assignment (submitted all forms)
    Route::get('/schools', [MentorAssignmentController::class, 'getSchoolsReadyForAssignment']);
    
    // Get all available mentors
    Route::get('/mentors', [MentorAssignmentController::class, 'getMentors']);
    
    // Assign mentor to school
    Route::post('/assign', [MentorAssignmentController::class, 'assignMentor']);
    
    // Re-assign mentor (admin only)
    Route::put('/reassign', [MentorAssignmentController::class, 'reassignMentor']);
    
    // Remove assignment (admin only)
    Route::delete('/remove/{schoolId}', [MentorAssignmentController::class, 'removeAssignment']);
    
    // Get evaluated schools
    Route::get('/evaluated', [MentorAssignmentController::class, 'getEvaluatedSchools']);
});
