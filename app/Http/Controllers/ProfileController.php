<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Rencana;
use App\Models\Permintaan;
use App\Models\Pernyataan;
use App\Models\AssignMentor;
use App\Models\Pendampingan;
use Illuminate\Http\Request;
use App\Models\AdministrasiSekolah;
use App\Models\BuktiSelfAssessment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * ✅ Display the admin dashboard dengan debugging lengkap
     */
    public function dashboardAdmin(): Response
    {
        Log::info('========== [DashboardAdmin] LOADING DASHBOARD ==========');

        // Get all users by role
        $admins = User::where('role', 'admin')->get();
        $mentors = User::where('role', 'mentor')->get();
        
        Log::info('[DashboardAdmin] Admins count: ' . $admins->count());
        Log::info('[DashboardAdmin] Mentors count: ' . $mentors->count());

        // ✅ Get all users dengan status submission
        $users = User::where('role', 'user')
            ->get()
            ->map(function ($user) {
                $a5 = Rencana::where('user_id', $user->id)->exists();
                $a6 = BuktiSelfAssessment::where('user_id', $user->id)->exists();
                $a7_pendampingan = Pendampingan::where('user_id', $user->id)->exists();
                $a7_permintaan = Permintaan::where('user_id', $user->id)->exists();
                $a7 = $a7_pendampingan || $a7_permintaan;
                $a8 = Pernyataan::where('user_id', $user->id)->exists();

                Log::info("[DashboardAdmin] User: {$user->name} (ID: {$user->id})", [
                    'A5' => $a5 ? 'YES' : 'NO',
                    'A6' => $a6 ? 'YES' : 'NO',
                    'A7_pendampingan' => $a7_pendampingan ? 'YES' : 'NO',
                    'A7_permintaan' => $a7_permintaan ? 'YES' : 'NO',
                    'A7_combined' => $a7 ? 'YES' : 'NO',
                    'A8' => $a8 ? 'YES' : 'NO',
                ]);

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'a5_status' => $a5,
                    'a6_status' => $a6,
                    'a7_status' => $a7,
                    'a8_status' => $a8,
                ];
            });

        Log::info('[DashboardAdmin] Total users: ' . $users->count());

        // ✅ Filter users yang sudah complete semua form (A5, A6, A7, A8)
        $allUsers = User::where('role', 'user')->get();
        Log::info('[DashboardAdmin] Processing ' . $allUsers->count() . ' users for complete check');

        $completeSchools = collect();

        foreach ($allUsers as $user) {
            $a5_status = Rencana::where('user_id', $user->id)->exists();
            $a6_status = BuktiSelfAssessment::where('user_id', $user->id)->exists();
            
            // ✅ Cek A7 dengan detail
            $a7_pendampingan = Pendampingan::where('user_id', $user->id)->exists();
            $a7_permintaan = Permintaan::where('user_id', $user->id)->exists();
            $a7_status = $a7_pendampingan || $a7_permintaan;
            
            $a8_status = Pernyataan::where('user_id', $user->id)->exists();

            Log::info("[DashboardAdmin] Checking completeness for: {$user->name}", [
                'user_id' => $user->id,
                'A5' => $a5_status ? '✅' : '❌',
                'A6' => $a6_status ? '✅' : '❌',
                'A7_pendampingan' => $a7_pendampingan ? '✅' : '❌',
                'A7_permintaan' => $a7_permintaan ? '✅' : '❌',
                'A7_final' => $a7_status ? '✅' : '❌',
                'A8' => $a8_status ? '✅' : '❌',
                'ALL_COMPLETE' => ($a5_status && $a6_status && $a7_status && $a8_status) ? '✅ YES' : '❌ NO',
            ]);

            // ✅ Hanya return jika SEMUA form sudah diisi
            if ($a5_status && $a6_status && $a7_status && $a8_status) {
                $sekolah = AdministrasiSekolah::where('user_id', $user->id)->first();
                
                Log::info("[DashboardAdmin] ✅✅✅ User {$user->name} IS COMPLETE!", [
                    'user_id' => $user->id,
                    'sekolah_nama' => $sekolah?->nama_sekolah,
                    'sekolah_npsn' => $sekolah?->npsn,
                ]);

                $completeSchools->push([
                    'id' => $user->id,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'sekolah_id' => $sekolah?->id,
                    'nama_sekolah' => $sekolah?->nama_sekolah ?? 'N/A',
                    'npsn' => $sekolah?->npsn ?? 'N/A',
                    'mentor' => null,
                    'status' => 'complete',
                ]);
            } else {
                Log::info("[DashboardAdmin] ❌ User {$user->name} is INCOMPLETE - Missing:", [
                    'missing_A5' => !$a5_status,
                    'missing_A6' => !$a6_status,
                    'missing_A7' => !$a7_status,
                    'missing_A8' => !$a8_status,
                ]);
            }
        }

        Log::info('[DashboardAdmin] ========================================');
        Log::info('[DashboardAdmin] ✅ COMPLETE SCHOOLS COUNT: ' . $completeSchools->count());
        Log::info('[DashboardAdmin] Complete schools data:', $completeSchools->toArray());
        Log::info('[DashboardAdmin] ========================================');

        // Get administrasi sekolah data untuk preview
        $administrasiSekolah = AdministrasiSekolah::with('user')
            ->get()
            ->map(function ($adm) {
                return [
                    'id' => $adm->id,
                    'user_id' => $adm->user_id,
                    'name' => $adm->user->name ?? 'Unknown',
                    'nama_sekolah' => $adm->nama_sekolah,
                    'npsn' => $adm->npsn,
                    'rencana_evaluasi' => Rencana::where('user_id', $adm->user_id)->exists(),
                    'self_assessment' => BuktiSelfAssessment::where('user_id', $adm->user_id)->exists(),
                    'kebutuhan_pendampingan' => Pendampingan::where('user_id', $adm->user_id)->exists() || 
                                                Permintaan::where('user_id', $adm->user_id)->exists(),
                    'pernyataan' => Pernyataan::where('user_id', $adm->user_id)->exists(),
                ];
            });

        return Inertia::render('Profile/DashboardAdmin', [
            'admins' => $admins,
            'users' => $users,
            'mentors' => $mentors,
            'completeSchools' => $completeSchools->values(), // ✅ Reset array keys
            'administrasiSekolah' => $administrasiSekolah,
        ]);
    }

    /**
     * ✅ Assign mentor to school - Tanpa API (Return Redirect dengan Flash Message)
     */
    public function assignMentor(Request $request)
    {
        $request->validate([
            'user_id_sekolah' => 'required|exists:users,id',
            'mentor_name' => 'required|string',
        ]);

        try {
            Log::info('[AssignMentor] Request received', $request->all());

            // Find mentor by name
            $mentor = User::where('name', $request->mentor_name)
                ->where('role', 'mentor')
                ->first();

            if (!$mentor) {
                return Redirect::back()->withErrors([
                    'message' => 'Mentor tidak ditemukan'
                ]);
            }

            // Check if school already has active assignment
            $existingAssignment = AssignMentor::where('user_id_sekolah', $request->user_id_sekolah)
                ->active()
                ->first();

            if ($existingAssignment) {
                return Redirect::back()->withErrors([
                    'message' => 'Sekolah sudah memiliki mentor aktif: ' . $existingAssignment->mentor->name
                ]);
            }

            // Create new assignment
            $assignment = AssignMentor::create([
                'user_id_sekolah' => $request->user_id_sekolah,
                'user_id_mentor' => $mentor->id,
                'assign_time_begin' => now(),
                'assign_time_finished' => null,
            ]);

            Log::info('[AssignMentor] ✅ Mentor assigned successfully', [
                'assignment_id' => $assignment->id,
                'sekolah_id' => $request->user_id_sekolah,
                'mentor_id' => $mentor->id,
                'mentor_name' => $mentor->name,
            ]);

            // ✅ Redirect back dengan success message
            return Redirect::back()->with([
                'success' => "Mentor {$mentor->name} berhasil ditugaskan",
            ]);

        } catch (\Exception $e) {
            Log::error('[AssignMentor] Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return Redirect::back()->withErrors([
                'message' => 'Gagal menyimpan assignment mentor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * ✅ Finish/Remove mentor assignment
     */
    public function finishMentorAssignment(Request $request)
    {
        $request->validate([
            'user_id_sekolah' => 'required|exists:users,id',
        ]);

        try {
            $assignment = AssignMentor::where('user_id_sekolah', $request->user_id_sekolah)
                ->active()
                ->first();

            if (!$assignment) {
                return Redirect::back()->withErrors([
                    'message' => 'Tidak ada assignment aktif untuk sekolah ini'
                ]);
            }

            $assignment->finish();

            Log::info('[FinishAssignment] ✅ Assignment finished', [
                'assignment_id' => $assignment->id,
                'sekolah_id' => $request->user_id_sekolah,
                'mentor_id' => $assignment->user_id_mentor,
            ]);

            return Redirect::back()->with([
                'success' => 'Assignment mentor berhasil diselesaikan'
            ]);

        } catch (\Exception $e) {
            Log::error('[FinishAssignment] Error: ' . $e->getMessage());
            
            return Redirect::back()->withErrors([
                'message' => 'Gagal menyelesaikan assignment: ' . $e->getMessage()
            ]);
        }
    }
}
