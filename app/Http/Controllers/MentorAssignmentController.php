<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rencana;
use App\Models\Permintaan;
use App\Models\Pernyataan;
use App\Models\Pendampingan;
use App\Models\BuktiSelfAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MentorAssignmentController extends Controller
{
    /**
     * Get all schools that have submitted all forms (A5, A6, A7, A8)
     * with their current mentor assignment status
     */
    public function getSchoolsReadyForAssignment()
    {
        $schools = User::where('role', 'user')
            ->get()
            ->filter(function ($user) {
                // Check if user has submitted all required forms
                $hasA5 = Rencana::where('user_id', $user->id)->exists();
                $hasA6 = BuktiSelfAssessment::where('user_id', $user->id)->exists();
                $hasA7 = Pendampingan::where('user_id', $user->id)->exists() || 
                         Permintaan::where('user_id', $user->id)->exists();
                $hasA8 = Pernyataan::where('user_id', $user->id)->exists();
                
                return $hasA5 && $hasA6 && $hasA7 && $hasA8;
            })
            ->map(function ($user) {
                // Get current active mentor assignment
                $currentAssignment = DB::table('assign_mentor')
                    ->where('user_id_sekolah', $user->id)
                    ->whereNull('assign_time_finished')
                    ->first();
                
                $mentorInfo = null;
                if ($currentAssignment) {
                    $mentor = User::find($currentAssignment->user_id_mentor);
                    $mentorInfo = [
                        'id' => $mentor->id ?? null,
                        'name' => $mentor->name ?? 'Unknown',
                        'email' => $mentor->email ?? null,
                        'assigned_at' => $currentAssignment->assign_time_begin,
                    ];
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'current_mentor' => $mentorInfo,
                    'has_mentor' => $currentAssignment !== null,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'schools' => $schools,
        ]);
    }

    /**
     * Get all available mentors
     */
    public function getMentors()
    {
        $mentors = User::where('role', 'mentor')
            ->select('id', 'name', 'email')
            ->get()
            ->map(function ($mentor) {
                // Count active assignments
                $activeAssignments = DB::table('assign_mentor')
                    ->where('user_id_mentor', $mentor->id)
                    ->whereNull('assign_time_finished')
                    ->count();

                return [
                    'id' => $mentor->id,
                    'name' => $mentor->name,
                    'email' => $mentor->email,
                    'active_assignments' => $activeAssignments,
                ];
            });

        return response()->json([
            'success' => true,
            'mentors' => $mentors,
        ]);
    }

    /**
     * Assign a mentor to a school
     */
    public function assignMentor(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:users,id',
            'mentor_id' => 'required|exists:users,id',
        ]);

        $schoolId = $request->school_id;
        $mentorId = $request->mentor_id;

        // Verify school is a user and mentor is a mentor
        $school = User::where('id', $schoolId)->where('role', 'user')->first();
        $mentor = User::where('id', $mentorId)->where('role', 'mentor')->first();

        if (!$school || !$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid school or mentor',
            ], 400);
        }

        // Check if school already has an active mentor
        $existingAssignment = DB::table('assign_mentor')
            ->where('user_id_sekolah', $schoolId)
            ->whereNull('assign_time_finished')
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Sekolah ini sudah memiliki mentor aktif. Gunakan fitur re-assign untuk mengganti mentor.',
                'current_mentor_id' => $existingAssignment->user_id_mentor,
            ], 409);
        }

        // Create new assignment
        DB::table('assign_mentor')->insert([
            'user_id_mentor' => $mentorId,
            'user_id_sekolah' => $schoolId,
            'assign_time_begin' => now(),
            'assign_time_finished' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentor berhasil di-assign ke sekolah',
            'data' => [
                'school_id' => $schoolId,
                'school_name' => $school->name,
                'mentor_id' => $mentorId,
                'mentor_name' => $mentor->name,
            ],
        ]);
    }

    /**
     * Re-assign mentor (change mentor for a school)
     * Only admin can do this directly
     */
    public function reassignMentor(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:users,id',
            'new_mentor_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        
        // Only admin can re-assign
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat melakukan re-assign mentor',
            ], 403);
        }

        $schoolId = $request->school_id;
        $newMentorId = $request->new_mentor_id;

        // Verify new mentor
        $newMentor = User::where('id', $newMentorId)->where('role', 'mentor')->first();
        if (!$newMentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor tidak valid',
            ], 400);
        }

        // Get current active assignment
        $currentAssignment = DB::table('assign_mentor')
            ->where('user_id_sekolah', $schoolId)
            ->whereNull('assign_time_finished')
            ->first();

        if (!$currentAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Sekolah ini belum memiliki mentor. Gunakan fitur assign untuk menambahkan mentor.',
            ], 400);
        }

        // End current assignment
        DB::table('assign_mentor')
            ->where('id', $currentAssignment->id)
            ->update([
                'assign_time_finished' => now(),
                'updated_at' => now(),
            ]);

        // Create new assignment
        DB::table('assign_mentor')->insert([
            'user_id_mentor' => $newMentorId,
            'user_id_sekolah' => $schoolId,
            'assign_time_begin' => now(),
            'assign_time_finished' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $school = User::find($schoolId);
        $oldMentor = User::find($currentAssignment->user_id_mentor);

        return response()->json([
            'success' => true,
            'message' => 'Mentor berhasil di-reassign',
            'data' => [
                'school_id' => $schoolId,
                'school_name' => $school->name ?? null,
                'old_mentor_id' => $currentAssignment->user_id_mentor,
                'old_mentor_name' => $oldMentor->name ?? null,
                'new_mentor_id' => $newMentorId,
                'new_mentor_name' => $newMentor->name,
            ],
        ]);
    }

    /**
     * Remove mentor assignment (end assignment without replacement)
     */
    public function removeAssignment(Request $request, $schoolId)
    {
        $user = Auth::user();
        
        // Only admin can remove assignment
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya admin yang dapat menghapus penugasan mentor',
            ], 403);
        }

        // Get current active assignment
        $currentAssignment = DB::table('assign_mentor')
            ->where('user_id_sekolah', $schoolId)
            ->whereNull('assign_time_finished')
            ->first();

        if (!$currentAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada penugasan aktif untuk sekolah ini',
            ], 404);
        }

        // End current assignment
        DB::table('assign_mentor')
            ->where('id', $currentAssignment->id)
            ->update([
                'assign_time_finished' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Penugasan mentor berhasil dihapus',
        ]);
    }

    /**
     * Get schools that have been evaluated (completed evaluation by mentor)
     */
    public function getEvaluatedSchools()
    {
        $evaluatedSchools = DB::table('assign_mentor')
            ->whereNotNull('assign_time_finished')
            ->get()
            ->map(function ($assignment) {
                $school = User::find($assignment->user_id_sekolah);
                $mentor = User::find($assignment->user_id_mentor);

                return [
                    'id' => $assignment->id,
                    'school_id' => $assignment->user_id_sekolah,
                    'school_name' => $school->name ?? 'Unknown',
                    'mentor_id' => $assignment->user_id_mentor,
                    'mentor_name' => $mentor->name ?? 'Unknown',
                    'assigned_at' => $assignment->assign_time_begin,
                    'completed_at' => $assignment->assign_time_finished,
                ];
            });

        return response()->json([
            'success' => true,
            'schools' => $evaluatedSchools,
        ]);
    }
}
