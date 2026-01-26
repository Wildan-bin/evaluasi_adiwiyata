<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Rencana;
use App\Models\Permintaan;
use App\Models\Pernyataan;
use App\Models\Pendampingan;
use Illuminate\Http\Request;
use App\Models\BuktiSelfAssessment;

class FormAdminController extends Controller
{
    /**
     * Display the form admin dashboard
     */
    public function index()
    {
        return Inertia::render('Features/Admin/FormAdmin');
    }

    /**
     * Get all users with their submission status
     */
    public function getUsersStatus(Request $request)
    {
        $users = User::select('id', 'name', 'email', 'role')
            ->where('role', 'user')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'a5_status' => Rencana::where('user_id', $user->id)->exists(),
                    'a6_status' => BuktiSelfAssessment::where('user_id', $user->id)->exists(),
                    'a7_status' => Pendampingan::where('user_id', $user->id)->exists() || 
                                   Permintaan::where('user_id', $user->id)->exists(),
                    'a8_status' => Pernyataan::where('user_id', $user->id)->exists(),
                ];
            });

        return response()->json(['users' => $users]);
    }
}
