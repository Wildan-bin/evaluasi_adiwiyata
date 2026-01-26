<?php

namespace App\Http\Controllers;

use App\Models\SkTim;
use App\Models\Ketua;
use App\Models\Anggota;
use App\Models\AdministrasiDasar;
use App\Models\AdministrasiSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FileAdministrasiController extends Controller
{
    /**
     * Display the file administration page
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Get sekolah data
        $sekolah = AdministrasiSekolah::where('user_id', $userId)->first();

        // Get SK Tim
        $skTim = SkTim::where('user_id', $userId)->first();

        // Get Ketua & Anggota
        $ketua = null;
        $anggota = [];

        if ($sekolah) {
            $ketua = Ketua::where('sekolah_id', $sekolah->id)->first();
            
            if ($ketua) {
                $anggota = Anggota::where('ketua_id', $ketua->id)->get();
            }
        }

        // Get Administrasi Dasar documents
        $administrasiDasar = AdministrasiDasar::where('user_id', $userId)->get();

        return Inertia::render('Features/FileUserAdministration', [
            'sekolah' => $sekolah,
            'skTim' => $skTim,
            'ketua' => $ketua,
            'anggota' => $anggota,
            'administrasiDasar' => $administrasiDasar,
            'user' => $user
        ]);
    }

    /**
     * Preview file
     */
    public function preview(Request $request, string $type, int $id)
    {
        $user = Auth::user();
        $filePath = null;

        switch ($type) {
            case 'skTim':
                $record = SkTim::where('id', $id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
                $filePath = $record->path_file;
                break;

            case 'administrasiDasar':
                $record = AdministrasiDasar::where('id', $id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
                $filePath = $record->path_file;
                break;

            default:
                abort(404, 'Type not found');
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk('local')->path($filePath);
        // $mimeType = Storage::disk('public')->mimeType($filePath);

        return response()->file($fullPath, [
            // 'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline'
        ]);
    }

    /**
     * Download file
     */
    public function download(Request $request, string $type, int $id)
    {
        $user = Auth::user();
        $filePath = null;
        $fileName = null;

        switch ($type) {
            case 'skTim':
                $record = SkTim::where('id', $id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
                $filePath = $record->path_file;
                $fileName = 'SK_Tim_' . $user->name . '.pdf';
                break;

            case 'administrasiDasar':
                $record = AdministrasiDasar::where('id', $id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
                $filePath = $record->path_file;
                $fileName = $record->indikator . '_' . $user->name . '.pdf';
                break;

            default:
                abort(404, 'Type not found');
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $fullPath = Storage::disk('public')->path($filePath);
        return response()->download($fullPath, $fileName);
    }
}