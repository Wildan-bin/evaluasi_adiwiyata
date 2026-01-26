<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SkTim;
use App\Models\Ketua;
use App\Models\Anggota;
use App\Models\AdministrasiDasar;
use App\Models\AdministrasiSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdministrasiAdminController extends Controller
{
    /**
     * Display list of users with their administration status
     */
    public function index()
    {
        // Get all users with role 'user' (excluding admin)
        $users = User::where('role', 'user')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $sekolah = AdministrasiSekolah::where('user_id', $user->id)->first();
                $skTim = SkTim::where('user_id', $user->id)->first();
                $ketua = null;
                
                if ($sekolah) {
                    $ketua = Ketua::where('sekolah_id', $sekolah->id)->first();
                }
                
                $administrasiDasar = AdministrasiDasar::where('user_id', $user->id)->count();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nama_sekolah' => $sekolah?->nama_sekolah ?? '-',
                    'npsn' => $sekolah?->npsn ?? '-',
                    'status' => [
                        'sekolah' => $sekolah !== null,
                        'skTim' => $skTim !== null && $ketua !== null,
                        'administrasiDasar' => $administrasiDasar > 0,
                    ],
                    'dokumen_count' => $administrasiDasar,
                    'created_at' => $user->created_at->format('d M Y'),
                ];
            });

        // ✅ Render ke Admin/AdministrationAdmin (bukan AdministrasiAdmin)
        return Inertia::render('Features/Admin/AdministrationAdmin', [
            'users' => $users,
        ]);
    }

    /**
     * Show detail administrasi for specific user
     */
    public function show(User $user)
    {
        // Get sekolah data
        $sekolah = AdministrasiSekolah::where('user_id', $user->id)->first();

        // Get SK Tim
        $skTim = SkTim::where('user_id', $user->id)->first();

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
        $administrasiDasar = AdministrasiDasar::where('user_id', $user->id)->get();

        return Inertia::render('Features/Admin/AdministrationAdminDetail', [
            'targetUser' => $user,
            'sekolah' => $sekolah,
            'skTim' => $skTim,
            'ketua' => $ketua,
            'anggota' => $anggota,
            'administrasiDasar' => $administrasiDasar,
        ]);
    }

    /**
     * Preview file for admin
     */
    public function preview(Request $request, User $user, string $type, int $id)
    {
        $filePath = null;

        try {
            switch ($type) {
                case 'skTim':
                    $record = SkTim::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();
                    
                    if (!$record) {
                        abort(404, 'SK Tim record not found');
                    }
                    
                    $filePath = $record->path_file;
                    break;

                case 'administrasiDasar':
                    $record = AdministrasiDasar::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();
                    
                    if (!$record) {
                        abort(404, 'Administrasi Dasar record not found');
                    }
                    
                    $filePath = $record->path_file;
                    break;

                default:
                    abort(404, 'Type not found');
            }

            if (!$filePath) {
                abort(404, 'File path not found in database');
            }

            // Normalize path
            $normalizedPath = $this->normalizePath($filePath);

            // Try local disk first
            if (Storage::disk('local')->exists($normalizedPath)) {
                $fullPath = Storage::disk('local')->path($normalizedPath);
                return response()->file($fullPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline',
                ]);
            }

            // Try with original path
            if (Storage::disk('local')->exists($filePath)) {
                $fullPath = Storage::disk('local')->path($filePath);
                return response()->file($fullPath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline',
                ]);
            }

            // Fallback: storage_path
            $storagePath = storage_path('app/' . $normalizedPath);
            if (file_exists($storagePath)) {
                return response()->file($storagePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline',
                ]);
            }

            Log::error("Admin preview - File not found: {$filePath}");
            abort(404, 'File not found on disk');

        } catch (\Exception $e) {
            Log::error("Admin preview error: " . $e->getMessage());
            abort(404, 'Error loading file');
        }
    }

    /**
     * Download file for admin
     */
    public function download(Request $request, User $user, string $type, int $id)
    {
        $filePath = null;
        $fileName = null;

        try {
            switch ($type) {
                case 'skTim':
                    $record = SkTim::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();
                    
                    if (!$record) {
                        abort(404, 'SK Tim record not found');
                    }
                    
                    $filePath = $record->path_file;
                    $fileName = 'SK_Tim_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name) . '.pdf';
                    break;

                case 'administrasiDasar':
                    $record = AdministrasiDasar::where('id', $id)
                        ->where('user_id', $user->id)
                        ->first();
                    
                    if (!$record) {
                        abort(404, 'Administrasi Dasar record not found');
                    }
                    
                    $filePath = $record->path_file;
                    $indikatorClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $record->indikator ?? 'Document');
                    $fileName = $indikatorClean . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $user->name) . '.pdf';
                    break;

                default:
                    abort(404, 'Type not found');
            }

            if (!$filePath) {
                abort(404, 'File path not found');
            }

            $normalizedPath = $this->normalizePath($filePath);

            if (Storage::disk('local')->exists($normalizedPath)) {
                return response()->download(Storage::disk('local')->path($normalizedPath), $fileName);
            }

            if (Storage::disk('local')->exists($filePath)) {
                return response()->download(Storage::disk('local')->path($filePath), $fileName);
            }

            $storagePath = storage_path('app/' . $normalizedPath);
            if (file_exists($storagePath)) {
                return response()->download($storagePath, $fileName);
            }

            abort(404, 'File not found');

        } catch (\Exception $e) {
            Log::error("Admin download error: " . $e->getMessage());
            abort(404, 'Error downloading file');
        }
    }

    /**
     * Normalize file path
     */
    private function normalizePath(string $path): string
    {
        $path = ltrim($path, '/');
        
        $prefixes = ['public/', 'storage/', 'app/'];
        
        foreach ($prefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }
        
        return $path;
    }
}