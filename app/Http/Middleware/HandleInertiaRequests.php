<?php

namespace App\Http\Middleware;

use App\Models\BuktiSelfAssessment;
use App\Models\Pendampingan;
use App\Models\Permintaan;
use App\Models\Pernyataan;
use App\Models\Rencana;
use App\Models\AdministrasiSekolah;
use App\Models\SkTim;
use App\Models\Ketua;
use App\Models\AdministrasiDasar;
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
            // CSRF token di-share ke Inertia props supaya bisa diakses di JS
            // Interceptor di bootstrap.js akan ambil token dari sini untuk setiap request
            'csrf_token' => csrf_token(),
            'completedSteps' => $this->getCompletedSteps($request),
            'completedAdministrasi' => $this->getCompletedAdministrasi($request), // ✅ Pisahkan
        ];
    }

    /**
     * Get completed steps for FORM (A5-A8)
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

    /**
     * Get completed steps for ADMINISTRASI (Data Sekolah, SK Tim, Administrasi Dasar)
     */
    private function getCompletedAdministrasi(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [
                'dataSekolah' => false,
                'skTim' => false,
                'administrasiDasar' => false,
            ];
        }

        $userId = $user->id;

        return [
            'dataSekolah' => AdministrasiSekolah::where('user_id', $userId)->exists(),
            'skTim' => $this->checkSkTimCompleted($userId),
            'administrasiDasar' => AdministrasiDasar::where('user_id', $userId)->exists(),
        ];
    }

    private function checkSkTimCompleted($userId)
    {
        $hasSkTim = SkTim::where('user_id', $userId)->exists();
        $sekolah = AdministrasiSekolah::where('user_id', $userId)->first();
        $hasKetua = false;

        if ($sekolah) {
            $hasKetua = Ketua::where('sekolah_id', $sekolah->id)->exists();
        }

        return $hasSkTim && $hasKetua;
    }
}
