<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SkTim;
use App\Models\Ketua;
use App\Models\Anggota;
use App\Models\FileUpload;
use App\Models\AdministrasiDasar;
use App\Models\AdministrasiSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdministrationController extends Controller
{
    /**
     * Get status of all steps for current user
     */
    public function getStatus()
    {
        $user = Auth::user();

        $completedSteps = [
            'dataSekolah' => $this->checkStep1Completed($user->id),
            'skTim' => $this->checkStep2Completed($user->id),
            'administrasiDasar' => $this->checkStep3Completed($user->id),
        ];

        return response()->json([
            'completedSteps' => $completedSteps
        ]);
    }

    /**
     * Check if Step 1 (Sekolah) is completed
     */
    private function checkStep1Completed($userId)
    {
        // ✅ Cek apakah ada data di tabel administrasi_sekolah untuk user ini
        return AdministrasiSekolah::where('user_id', $userId)->exists();
    }

    /**
     * Check if Step 2 (Tim) is completed
     */
    private function checkStep2Completed($userId)
    {
        // ✅ Cek SK Tim
        $hasSkTim = SkTim::where('user_id', $userId)->exists();
        
        // ✅ Cek Ketua (harus ada sekolah dulu)
        $sekolah = AdministrasiSekolah::where('user_id', $userId)->first();
        $hasKetua = false;
        
        if ($sekolah) {
            $hasKetua = Ketua::where('sekolah_id', $sekolah->id)->exists();
        }

        // ✅ Step 2 completed jika SK Tim DAN Ketua ada
        // Anggota optional (boleh ada boleh tidak)
        return $hasSkTim && $hasKetua;
    }

    /**
     * Check if Step 3 (Administrasi Dasar) is completed
     */
    private function checkStep3Completed($userId)
    {
        // ✅ Cek apakah ada minimal 1 dokumen administrasi dasar
        return AdministrasiDasar::where('user_id', $userId)->exists();
    }

    /**
     * Get Sekolah data for current user
     */
    public function getSekolah()
    {
        $user = Auth::user();
        $sekolah = AdministrasiSekolah::where('user_id', $user->id)->first();

        return response()->json([
            'sekolah' => $sekolah
        ]);
    }

    /**
     * Store Sekolah data
     */
    public function storeSekolah(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:20',
            'alamat' => 'required|string',
            'kelurahan' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_maps_url' => 'nullable|url|max:500',
        ]);

        $sekolah = AdministrasiSekolah::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Data sekolah berhasil disimpan',
            'sekolah' => $sekolah
        ]);
    }

    /**
     * Get Tim data (SK Tim + Ketua + Anggota)
     */
    public function getTim()
    {
        $user = Auth::user();
        $sekolah = AdministrasiSekolah::where('user_id', $user->id)->first();

        $skTim = SkTim::where('user_id', $user->id)->first();
        $ketua = null;
        $anggota = [];

        if ($sekolah) {
            $ketua = Ketua::where('sekolah_id', $sekolah->id)->first();
            
            if ($ketua) {
                $anggota = Anggota::where('ketua_id', $ketua->id)->get();
            }
        }

        return response()->json([
            'skTim' => $skTim,
            'ketua' => $ketua,
            'anggota' => $anggota
        ]);
    }

    /**
     * Store Tim data (SK Tim + Ketua + Anggota)
     */
    public function storeTim(Request $request)
    {
        $user = Auth::user();
        $sekolah = AdministrasiSekolah::where('user_id', $user->id)->first();

        if (!$sekolah) {
            return response()->json([
                'success' => false,
                'message' => 'Data sekolah belum diisi. Lengkapi step sebelumnya.'
            ], 400);
        }

        // Validate SK Tim file
        $request->validate([
            'sk_tim_file' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        // Validate Ketua
        $ketuaData = json_decode($request->input('ketua'), true);
        if (!$ketuaData || empty($ketuaData['nama'])) {
            return response()->json([
                'success' => false,
                'message' => 'Data ketua harus diisi'
            ], 400);
        }

        // Handle SK Tim file upload dengan rename
        $file = $request->file('sk_tim_file');
        
        // Rename file: NamaUser_SK-Tim_Administration.pdf
        $fileName = $this->renameFile($user->name, 'SK-Tim', 'Administration', $file);
        
        // Store file ke storage/app/public/input_sk_tim
        $storagePath = Storage::disk('public')->putFileAs(
            'input_sk_tim',
            $file,
            $fileName
        );

        // Store SK Tim (without FileUpload record)
        $skTim = SkTim::updateOrCreate(
            ['user_id' => $user->id],
            [
                'path_file' => $storagePath,
                'file_upload_id' => null,
            ]
        );

        // Store Ketua
        $ketua = Ketua::updateOrCreate(
            ['sekolah_id' => $sekolah->id],
            [
                'nama' => $ketuaData['nama'],
                'nip' => $ketuaData['nip'] ?? null,
                'email' => $ketuaData['email'] ?? null,
                'nomor_telepon' => $ketuaData['nomor_telepon'] ?? null,
            ]
        );

        // Store Anggota
        $anggotaData = json_decode($request->input('anggota'), true);
        if ($anggotaData && is_array($anggotaData)) {
            // Delete old anggota and create new
            Anggota::where('ketua_id', $ketua->id)->delete();

            foreach ($anggotaData as $anggota) {
                if (!empty($anggota['nama'])) {
                    Anggota::create([
                        'ketua_id' => $ketua->id,
                        'sekolah_id' => $sekolah->id,
                        'nama' => $anggota['nama'],
                        'nip' => $anggota['nip'] ?? null,
                        'email' => $anggota['email'] ?? null,
                        'nomor_telepon' => $anggota['nomor_telepon'] ?? null,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data tim berhasil disimpan',
            'file_path' => $storagePath,
        ]);
    }

    /**
     * Get Administrasi Dasar data
     */
    public function getDasar()
    {
        $user = Auth::user();
        $documents = AdministrasiDasar::where('user_id', $user->id)->get();

        return response()->json([
            'documents' => $documents
        ]);
    }

    /**
     * Store Administrasi Dasar files
     */
    public function storeAdministrasiDasar(Request $request)
    {
        $user = Auth::user();

        // Validate that at least one file is uploaded
        $filesData = $request->input('files');
        if (!$filesData || !is_array($filesData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ], 400);
        }

        $savedCount = 0;

        foreach ($filesData as $key => $data) {
            if ($request->hasFile("files.{$key}.file")) {
                $file = $request->file("files.{$key}.file");
                $indikator = $data['indikator'] ?? $key;

                // Validate individual file
                $validator = Validator::make(
                    ["files.{$key}.file" => $file],
                    ["files.{$key}.file" => 'file|mimes:pdf|max:10240']
                );

                if ($validator->fails()) {
                    continue;
                }

                // Rename file: NamaUser_indikator_AdministrasiDasar.pdf
                $fileName = $this->renameFile($user->name, $indikator, 'AdministrasiDasar', $file);

                // Save file ke storage/app/public/input_administrasi_dasar
                $storagePath = Storage::disk('public')->putFileAs(
                    'input_administrasi_dasar',
                    $file,
                    $fileName
                );

                // Create or update AdministrasiDasar record (tanpa FileUpload)
                AdministrasiDasar::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'indikator' => $indikator
                    ],
                    [
                        'path_file' => $storagePath,
                        'file_upload_id' => null,
                    ]
                );

                $savedCount++;
            }
        }

        if ($savedCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file valid yang berhasil disimpan'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "{$savedCount} dokumen administrasi dasar berhasil disimpan",
        ]);
    }

    /**
     * Rename file dengan format: NamaUser_indikator_form.pdf
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
}
