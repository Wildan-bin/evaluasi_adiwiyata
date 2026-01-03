<?php

namespace App\Http\Middleware;

use App\Models\BuktiSelfAssessment;
use App\Models\Pendampingan;
use App\Models\Permintaan;
use App\Models\Pernyataan;
use App\Models\Rencana;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'csrf_token' => csrf_token(),
            'completedSteps' => $this->getCompletedSteps($request),
        ];
    }

    /**
     * Get completed steps based on database records for the logged-in user.
     */
    private function getCompletedSteps(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [
                'a5' => false,
                'a6' => false,
                'a7' => false,
                'a8' => false,
            ];
        }

        $userId = $user->id;

        return [
            // A5 - Rencana & Evaluasi PBLHS
            'a5' => Rencana::where('user_id', $userId)->exists(),
            
            // A6 - Self Assessment (Bukti Self Assessment)
            'a6' => BuktiSelfAssessment::where('user_id', $userId)->exists(),
            
            // A7 - Kebutuhan Pendampingan (cek Pendampingan DAN Permintaan)
            'a7' => Pendampingan::where('user_id', $userId)->exists() 
                    || Permintaan::where('user_id', $userId)->exists(),
            
            // A8 - Pernyataan & Persetujuan
            'a8' => Pernyataan::where('user_id', $userId)->exists(),
        ];
    }
}
