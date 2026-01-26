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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdministrasiMentorController extends Controller
{
    /**
     * Display administrasi for assigned school
     */
    public function index()
    {
        $user = Auth::user();

        // Get assigned school
        $assignedSchool = DB::table('assign_mentor')
            ->where('user_id_mentor', $user->id)
            ->whereNull('assign_time_finished')
            ->first();

        if (!$assignedSchool) {
            return Inertia::render('Features/Mentor/AdministrasiMentor', [
                'user' => $user,
                'assignedSchool' => null,
                'targetUser' => null,
                'sekolah' => null,
                'skTim' => null,
                'ketua' => null,
                'anggota' => [],
                'administrasiDasar' => [],
            ]);
        }

        $targetUserId = $assignedSchool->user_id_sekolah;
        $targetUser = User::find($targetUserId);

        // Get sekolah data
        $sekolah = AdministrasiSekolah::where('user_id', $targetUserId)->first();

        // Get SK Tim
        $skTim = SkTim::where('user_id', $targetUserId)->first();

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
        $administrasiDasar = AdministrasiDasar::where('user_id', $targetUserId)->get();

        return Inertia::render('Features/Mentor/AdministrasiMentor', [
            'user' => $user,
            'assignedSchool' => $assignedSchool,
            'targetUser' => $targetUser,
            'sekolah' => $sekolah,
            'skTim' => $skTim,
            'ketua' => $ketua,
            'anggota' => $anggota,
            'administrasiDasar' => $administrasiDasar,
        ]);
    }

    /**
     * Preview file for mentor
     */
    public function preview(Request $request, string $type, int $id)
    {
        $user = Auth::user();

        // Get assigned school
        $assignedSchool = DB::table('assign_mentor')
            ->where('user_id_mentor', $user->id)
            ->whereNull('assign_time_finished')
            ->first();

        if (!$assignedSchool) {
            abort(403, 'No assigned school');
        }

        $targetUserId = $assignedSchool->user_id_sekolah;
        $filePath = null;

        try {
            switch ($type) {
                case 'skTim':
                    $record = SkTim::where('id', $id)
                        ->where('user_id', $targetUserId)
                        ->first();
                    
                    if (!$record) {
                        abort(404, 'SK Tim record not found');
                    }
                    
                    $filePath = $record->path_file;
                    break;

                case 'administrasiDasar':
                    $record = AdministrasiDasar::where('id', $id)
                        ->where('user_id', $targetUserId)
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

            // Try local disk
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

            Log::error("Mentor preview - File not found: {$filePath}");
            abort(404, 'File not found on disk');

        } catch (\Exception $e) {
            Log::error("Mentor preview error: " . $e->getMessage());
            abort(404, 'Error loading file');
        }
    }

    /**
     * Download file for mentor
     */
    public function download(Request $request, string $type, int $id)
    {
        $user = Auth::user();

        // Get assigned school
        $assignedSchool = DB::table('assign_mentor')
            ->where('user_id_mentor', $user->id)
            ->whereNull('assign_time_finished')
            ->first();

        if (!$assignedSchool) {
            abort(403, 'No assigned school');
        }

        $targetUserId = $assignedSchool->user_id_sekolah;
        $targetUser = User::find($targetUserId);
        $filePath = null;
        $fileName = null;

        try {
            switch ($type) {
                case 'skTim':
                    $record = SkTim::where('id', $id)
                        ->where('user_id', $targetUserId)
                        ->first();
                    
                    if (!$record) {
                        abort(404, 'SK Tim record not found');
                    }
                    
                    $filePath = $record->path_file;
                    $fileName = 'SK_Tim_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $targetUser->name ?? 'Sekolah') . '.pdf';
                    break;

                case 'administrasiDasar':
                    $record = AdministrasiDasar::where('id', $id)
                        ->where('user_id', $targetUserId)
                        ->first();
                    
                    if (!$record) {
                        abort(404, 'Administrasi Dasar record not found');
                    }
                    
                    $filePath = $record->path_file;
                    $indikatorClean = preg_replace('/[^a-zA-Z0-9_-]/', '_', $record->indikator ?? 'Document');
                    $fileName = $indikatorClean . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $targetUser->name ?? 'Sekolah') . '.pdf';
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
            Log::error("Mentor download error: " . $e->getMessage());
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