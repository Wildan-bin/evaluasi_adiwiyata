<?php

namespace App\Http\Controllers;

use App\Models\Rencana;
use App\Models\BuktiSelfAssessment;
use App\Models\Pendampingan;
use App\Models\Pernyataan;
use App\Models\Permintaan;
use App\Models\Kemajuan;
use App\Models\User;
use App\Models\MentorComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class FormSubmissionController extends Controller
{
    /**
     * ============================================================================
     * A5 - RENCANA (Perencanaan & Evaluasi PBLHS)
     * ============================================================================
     */

    /**
     * Save A5 Form (Rencana)
     * - Check if user already has data
     * - Create multiple records for each indikator
     * - Handle file uploads with renaming
     * - Create/update Kemajuan record
     */
    public function saveA5(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check if user already has Rencana data
        $existingRencana = Rencana::where('user_id', $userId)->first();
        if ($existingRencana) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengisi bagian A5 (Rencana)',
                'data_exists' => true
            ], 422);
        }

        // Define indikator for A5
        $indicators = [
            'evaluasi_diri_sekolah' => 'Evaluasi Diri Sekolah',
            'hasil_ipmlh' => 'Hasil IPMLH',
            'rencana_gpblhs_4_tahunan' => 'Rencana GPBLHS 4-tahunan',
            'rencana_gpblhs_tahunan' => 'Rencana GPBLHS tahunan',
            'dokumentasi_penyusunan' => 'Dokumentasi Penyusunan',
            'sk_tim_sekolah' => 'SK Tim Sekolah'
        ];

        try {
            $rencanaCreated = 0;

            foreach ($indicators as $fieldName => $indicatorName) {
                // Check if file exists in request
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);

                    // Validate file
                    if (!$file->isValid()) {
                        continue;
                    }

                    // Rename file: NamaUser_indikator_A5.pdf
                    $fileName = $this->renameFile($user->name, $indicatorName, 'A5', $file);

                    // Store file ke public folder
                    $storagePath = Storage::disk('public')->putFileAs(
                        'input_A5',
                        $file,
                        $fileName
                    );

                    // Create Rencana record
                    Rencana::create([
                        'user_id' => $userId,
                        'indikator' => $indicatorName,
                        'path_file' => $storagePath
                    ]);

                    $rencanaCreated++;
                }
            }

            // Create Kemajuan record if not exists
            $kemajuan = Kemajuan::firstOrCreate(
                ['user_id' => $userId],
                ['progress' => 1]
            );

            // If Kemajuan already exists, increment progress
            if (!$kemajuan->wasRecentlyCreated) {
                $kemajuan->increment('progress');
            }

            return response()->json([
                'success' => true,
                'message' => "A5 berhasil disimpan! $rencanaCreated file terupload",
                'rencana_count' => $rencanaCreated,
                'progress' => $kemajuan->progress
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan A5: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get A5 data for current user
     */
    public function getA5(Request $request)
    {
        $user = Auth::user();
        $rencanaData = Rencana::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data_exists' => $rencanaData->count() > 0,
            'data' => $rencanaData
        ]);
    }

    /**
     * ============================================================================
     * A6 - BUKTI SELF ASSESSMENT
     * ============================================================================
     */

    /**
     * Save A6 Form (Bukti Self Assessment)
     * - Check if user already has data
     * - Create records for each indikator with file + penjelasan
     * - Increment Kemajuan progress
     */
    public function saveA6(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check if user already has Bukti Self Assessment data
        $existingBukti = BuktiSelfAssessment::where('user_id', $userId)->first();
        if ($existingBukti) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengisi bagian A6 (Self Assessment)',
                'data_exists' => true
            ], 422);
        }

        // Define indikator for A6
        $indicators = [
            'kebersihan' => 'Kebersihan, Sanitasi, Drainase',
            'kelola_sampah' => 'Pengelolaan Sampah (3R + bank sampah)',
            'pemeliharaan_pohon' => 'Pemeliharaan Pohon/Tanaman',
            'konservasi_air' => 'Konservasi Air',
            'konservasi_energi' => 'Konservasi Energi',
            'inovasi_prlh' => 'Inovasi PRLH',
            'penerapan_prlh' => 'Penerapan PRLH di Masyarakat',
            'jejaring' => 'Jejaring dan Komunikasi',
            'kampanye_media' => 'Kampanye & Media Publikasi',
            'pembinaan_kader' => 'Pembentukan & Pembinaan Kader',
            'pemantauan_evaluasi' => 'Pemantauan & Evaluasi'
        ];

        try {
            $buktiCreated = 0;

            foreach ($indicators as $fieldKey => $indicatorName) {
                // Get penjelasan from request
                $penjelasan = $request->input("penjelasan_{$fieldKey}", '');

                // Check if file exists
                if ($request->hasFile("bukti_{$fieldKey}")) {
                    $file = $request->file("bukti_{$fieldKey}");

                    if (!$file->isValid()) {
                        continue;
                    }

                    // Rename file: NamaUser_indikator_A6.pdf
                    $fileName = $this->renameFile($user->name, $indicatorName, 'A6', $file);

                    // Store file
                    $storagePath = Storage::disk('public')->putFileAs(
                        'input_A6',
                        $file,
                        $fileName
                    );

                    // Create Bukti Self Assessment record
                    BuktiSelfAssessment::create([
                        'user_id' => $userId,
                        'indikator' => $indicatorName,
                        'path_file' => $storagePath,
                        'penjelasan' => $penjelasan
                    ]);

                    $buktiCreated++;
                }
            }

            // Increment Kemajuan progress
            $kemajuan = Kemajuan::where('user_id', $userId)->first();
            if ($kemajuan) {
                $kemajuan->increment('progress');
            }

            return response()->json([
                'success' => true,
                'message' => "A6 berhasil disimpan! $buktiCreated indikator terupload",
                'bukti_count' => $buktiCreated,
                'progress' => $kemajuan->progress ?? 0
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan A6: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get A6 data for current user
     */
    public function getA6(Request $request)
    {
        $user = Auth::user();
        $buktiData = BuktiSelfAssessment::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data_exists' => $buktiData->count() > 0,
            'data' => $buktiData
        ]);
    }

    /**
     * ============================================================================
     * A7 - PENDAMPINGAN & PERMINTAAN
     * ============================================================================
     */

    /**
     * Save A7 Form (Pendampingan & Permintaan)
     * - Save Permintaan record first
     * - Create Pendampingan records for each kebutuhan
     * - Increment Kemajuan progress
     */
    public function saveA7(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check if user already has Pendampingan data
        $existingPendampingan = Pendampingan::where('user_id', $userId)->first();
        if ($existingPendampingan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengisi bagian A7 (Pendampingan)',
                'data_exists' => true
            ], 422);
        }

        // Define kebutuhan options for A7
        $kebutuhanOptions = [
            'kurikulum_terintegrasi' => 'Kurikulum terintegrasi PRLH',
            'pengelolaan_sampah' => 'Pengelolaan sampah & bank sampah',
            'konservasi_air' => 'Konservasi air',
            'konservasi_energi' => 'Konservasi energi',
            'inovasi_lingkungan' => 'Perancangan inovasi lingkungan',
            'pelaporan_monev' => 'Pelaporan & monev (dashboard)'
        ];

        try {
            // Step 1: Create Permintaan record
            $permintaanData = $request->input('permintaan_tim', '');
            if (!$permintaanData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan tim harus diisi'
                ], 422);
            }

            $permintaan = Permintaan::create([
                'user_id' => $userId,
                'permintaan_tim' => $permintaanData
            ]);

            // Step 2: Get selected kebutuhan from request
            $selectedKebutuhan = $request->input('kebutuhan_pendampingan', []);
            if (empty($selectedKebutuhan)) {
                $permintaan->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Minimal pilih satu kebutuhan pendampingan'
                ], 422);
            }

            // Step 3: Create Pendampingan records for each selected kebutuhan
            $pendampinganCreated = 0;
            $waktuPendampingan = $request->input('waktu_pendampingan');

            foreach ($selectedKebutuhan as $kebutuhanKey) {
                if (isset($kebutuhanOptions[$kebutuhanKey])) {
                    Pendampingan::create([
                        'user_id' => $userId,
                        'id_permintaan' => $permintaan->id,
                        'kebutuhan' => $kebutuhanOptions[$kebutuhanKey],
                        'waktu_pendampingan' => $waktuPendampingan
                    ]);

                    $pendampinganCreated++;
                }
            }

            // Step 4: Increment Kemajuan progress
            $kemajuan = Kemajuan::where('user_id', $userId)->first();
            if ($kemajuan) {
                $kemajuan->increment('progress');
            }

            return response()->json([
                'success' => true,
                'message' => "A7 berhasil disimpan! $pendampinganCreated kebutuhan pendampingan tercatat",
                'permintaan_id' => $permintaan->id,
                'pendampingan_count' => $pendampinganCreated,
                'progress' => $kemajuan->progress ?? 0
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan A7: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get A7 data for current user
     */
    public function getA7(Request $request)
    {
        $user = Auth::user();
        $pendampinganData = Pendampingan::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data_exists' => $pendampinganData->count() > 0,
            'data' => $pendampinganData
        ]);
    }

    /**
     * ============================================================================
     * A8 - PERNYATAAN & PERSETUJUAN
     * ============================================================================
     */

    /**
     * Save A8 Form (Pernyataan & Persetujuan)
     * - Save Pernyataan record with agreements
     * - Handle file upload for bukti_persetujuan
     * - Increment Kemajuan progress
     */
    public function saveA8(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check if user already has Pernyataan data
        $existingPernyataan = Pernyataan::where('user_id', $userId)->first();
        if ($existingPernyataan) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengisi bagian A8 (Pernyataan)',
                'data_exists' => true
            ], 422);
        }

        try {
            // Validate request
            $validated = $request->validate([
                'pernyataan_data' => 'required|in:benar,tidak benar',
                'persetujuan_publikasi' => 'required|in:setuju,tidak setuju',
                'bukti_persetujuan' => 'required|file|mimes:pdf|max:5120' // max 5MB
            ]);

            // Handle file upload
            $buktiPath = null;
            if ($request->hasFile('bukti_persetujuan')) {
                $file = $request->file('bukti_persetujuan');

                // Rename file: NamaUser_bukti_persetujuan_A8.pdf
                $fileName = $this->renameFile($user->name, 'bukti_persetujuan', 'A8', $file);

                // Store file
                $buktiPath = Storage::disk('public')->putFileAs(
                    'input_A8',
                    $file,
                    $fileName
                );
            }

            // Create Pernyataan record
            $pernyataan = Pernyataan::create([
                'user_id' => $userId,
                'pernyataan_data' => $validated['pernyataan_data'],
                'persetujuan_publikasi' => $validated['persetujuan_publikasi'],
                'bukti_persetujuan' => $buktiPath
            ]);

            // Increment Kemajuan progress
            $kemajuan = Kemajuan::where('user_id', $userId)->first();
            if ($kemajuan) {
                $kemajuan->increment('progress');
            }

            return response()->json([
                'success' => true,
                'message' => 'A8 berhasil disimpan! Semua tahap telah selesai.',
                'pernyataan_id' => $pernyataan->id,
                'progress' => $kemajuan->progress ?? 0
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan A8: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get A8 data for current user
     */
    public function getA8(Request $request)
    {
        $user = Auth::user();
        $pernyataanData = Pernyataan::where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'data_exists' => !!$pernyataanData,
            'data' => $pernyataanData
        ]);
    }

    /**
     * ============================================================================
     * VIEW RENDERING METHODS - Render Inertia view dengan data checking
     * ============================================================================
     */

    /**
     * Show A5 page dengan pengecekan data
     */
    public function showA5()
    {
        Log::info('========== SHOW A5 METHOD CALLED ==========');

        $user = Auth::user();
        Log::info('User:', ['id' => $user?->id, 'name' => $user?->name]);

        if (!$user) {
            Log::error('NO USER FOUND');
            return Inertia::render('Features/Submission/A5', [
                'dataExists' => false,
                'existingData' => [],
                'debugInfo' => ['error' => 'User not authenticated']
            ]);
        }

        $userId = $user->id;

        // Direct query
        $rencanaData = Rencana::where('user_id', $userId)->get();

        Log::info('Rencana Data:', [
            'count' => $rencanaData->count(),
            'sql' => Rencana::where('user_id', $userId)->toSql(),
            'data' => $rencanaData->toArray()
        ]);

        $dataExists = $rencanaData->count() > 0;

        $debugInfo = [
            'methodCalled' => true,
            'userId' => $userId,
            'userName' => $user->name,
            'rencanaCount' => $rencanaData->count(),
            'dataExists' => $dataExists,
            'timestamp' => now()->toDateTimeString()
        ];

        Log::info('========== DEBUG INFO ==========', $debugInfo);

        return Inertia::render('Features/Submission/A5', [
            'dataExists' => $dataExists,
            'existingData' => $rencanaData,
            'debugInfo' => $debugInfo
        ]);
    }

    /**
     * Show A6 page dengan pengecekan data
     */
    public function showA6()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check if user already has Bukti Self Assessment data
        $buktiData = BuktiSelfAssessment::where('user_id', $userId)->get();
        $dataExists = $buktiData->count() > 0;

        return Inertia::render('Features/Submission/A6', [
            'dataExists' => $dataExists,
            'existingData' => $buktiData
        ]);
    }

    /**
     * Show A7 page dengan pengecekan data
     */
    public function showA7()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check if user already has Pendampingan data
        $pendampinganData = Pendampingan::where('user_id', $userId)->get();
        $dataExists = $pendampinganData->count() > 0;

        return Inertia::render('Features/Submission/A7', [
            'dataExists' => $dataExists,
            'existingData' => $pendampinganData
        ]);
    }

    /**
     * Show A8 page dengan pengecekan data
     */
    public function showA8()
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check if user already has Pernyataan data
        $pernyataanData = Pernyataan::where('user_id', $userId)->first();
        $dataExists = !!$pernyataanData;

        return Inertia::render('Features/Submission/A8', [
            'dataExists' => $dataExists,
            'existingData' => $pernyataanData
        ]);
    }

    /**
     * ============================================================================
     * STEP STATUS & SAVE METHODS
     * ============================================================================
     */

    /**
     * Get status for all steps (A5, A6, A7, A8)
     */
    public function getStatus(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        // Check each step completion status
        $completedSteps = [
            'a5' => Rencana::where('user_id', $userId)->exists(),
            'a6' => BuktiSelfAssessment::where('user_id', $userId)->exists(),
            'a7' => Pendampingan::where('user_id', $userId)->exists(),
            'a8' => Pernyataan::where('user_id', $userId)->exists(),
        ];

        // Get current progress from Kemajuan table
        $kemajuan = Kemajuan::where('user_id', $userId)->first();

        return response()->json([
            'success' => true,
            'completedSteps' => $completedSteps,
            'progress' => $kemajuan ? $kemajuan->progress : 0
        ]);
    }

    /**
     * Save step data - Generic method (optional, untuk future use)
     */
    public function saveStep(Request $request)
    {
        $step = $request->input('step');
        
        // Route to specific save method based on step
        switch ($step) {
            case 'a5':
                return $this->saveA5($request);
            case 'a6':
                return $this->saveA6($request);
            case 'a7':
                return $this->saveA7($request);
            case 'a8':
                return $this->saveA8($request);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid step'
                ], 400);
        }
    }

    /**
     * ============================================================================
     * HELPER METHODS
     * ============================================================================
     */

    /**
     * Rename file dengan format: NamaUser_indikator_form.pdf
     * Contoh: Wildan_Konservasi-air_A5.pdf
     */
    private function renameFile($userName, $indicator, $form, $file)
    {
        // Sanitize user name
        $userName = preg_replace('/[^a-zA-Z0-9-_]/', '', $userName);

        // Sanitize indicator
        $indicator = preg_replace('/[^a-zA-Z0-9-_\s]/', '', $indicator);
        $indicator = str_replace(' ', '-', $indicator);

        // Get file extension
        $extension = $file->getClientOriginalExtension();

        // Generate filename
        return "{$userName}_{$indicator}_{$form}.{$extension}";
    }

    /**
     * Check if user has completed a form
     */
    private function checkDataExists($model, $userId)
    {
        return $model::where('user_id', $userId)->first();
    }

    /**
     * Get users submission status - for admin dashboard
     */
    public function getUsersSubmissionStatus()
    {
        $users = User::select('id', 'name', 'email', 'role')
            ->where('role', '!=', 'admin')
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

    /**
     * Show user files page - untuk FileUser.vue
     */
    public function showUserFiles($userId = null)
    {
        $currentUser = Auth::user();
    
        // Jika $userId tidak diberikan, gunakan user yang sedang login
        if (!$userId) {
            $user = $currentUser;
        } else {
            // Jika admin/mentor yang akses user lain, pastikan authorized
            abort_if(!in_array($currentUser->role, ['admin', 'mentor']), 403);
            $user = User::findOrFail($userId);
        }
        
        // Get A5 files (Rencana) with mentor comments
        $a5Files = Rencana::where('user_id', $user->id)
            ->with(['comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'indikator' => $file->indikator,
                    'path_file' => $file->path_file,
                    'file_size' => $this->getFileSize($file->path_file),
                    'created_at' => $file->created_at,
                    'comments' => $this->formatComments($file->comments),
                ];
            });
        
        // Get A6 files (Bukti Self Assessment) with mentor comments
        $a6Files = BuktiSelfAssessment::where('user_id', $user->id)
            ->with(['comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'indikator' => $file->indikator,
                    'path_file' => $file->path_file,
                    'file_size' => $this->getFileSize($file->path_file),
                    'created_at' => $file->created_at,
                    'comments' => $this->formatComments($file->comments),
                ];
            });
        
        // Get A7 data (Pendampingan)
        $a7Data = Pendampingan::where('user_id', $user->id)->get();
        
        // Get A8 data (Pernyataan) with mentor comments
        $a8Data = Pernyataan::where('user_id', $user->id)
            ->with(['comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->first();
        
        if ($a8Data) {
            $a8Data = [
                'id' => $a8Data->id,
                'pernyataan_data' => $a8Data->pernyataan_data,
                'persetujuan_publikasi' => $a8Data->persetujuan_publikasi,
                'bukti_persetujuan' => $a8Data->bukti_persetujuan,
                'file_size' => $this->getFileSize($a8Data->bukti_persetujuan),
                'created_at' => $a8Data->created_at,
                'comments' => $this->formatComments($a8Data->comments),
            ];
        }

        $data = [
            'user' => $user,
            'a5_files' => $a5Files,
            'a6_files' => $a6Files,
            'a7_data' => $a7Data,
            'a8_data' => $a8Data
        ];
        
        if ($currentUser->role === 'admin') {
            // Admin melihat file submission user lain
            return Inertia::render('Features/Admin/Administrasi', $data);
        } else {
            // Mentor atau user biasa melihat file mereka sendiri
            return Inertia::render('Features/FileUser', $data);
        }
    }

    /**
     * Format mentor comments untuk ditampilkan di frontend
     */
    private function formatComments($comments)
    {
        if (!$comments) {
            return [];
        }
        
        return $comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'mentor_name' => $comment->mentor->name ?? 'Unknown Mentor',
                'mentor_id' => $comment->mentor_id,
                'created_at' => $comment->created_at,
                'file_type' => $comment->file_type,
            ];
        })->toArray();
    }

    /**
     * Get file size dari storage
     */
    private function getFileSize($filePath)
    {
        if (!$filePath) {
            return 0;
        }
        
        try {
            // Normalize path
            $path = ltrim($filePath, '/');
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, 7);
            }
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, 8);
            }
            
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->size($path);
            }
        } catch (\Exception $e) {
            Log::error('Error getting file size: ' . $e->getMessage());
        }
        
        return 0;
    }
}
