<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\FileEvidence;
use App\Services\FileEvidenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SubmissionController extends Controller
{
    protected $fileEvidenceService;

    public function __construct(FileEvidenceService $fileEvidenceService)
    {
        $this->fileEvidenceService = $fileEvidenceService;
    }

    /**
     * Save A5 Draft
     */
    public function saveDraftA5(Request $request)
    {
        try {
            // ================================================================
            // 1. VALIDATION
            // ================================================================
            $validated = $request->validate([
                'ketua_tim' => 'nullable|string|max:255',
                'jumlah_kader_adiwiyata' => 'nullable|integer|min:1',
                'anggota_tim' => 'nullable|string', // JSON string dari frontend
                
                // Files - all optional untuk draft
                'evaluasi_diri_sekolah' => 'nullable|file|mimes:pdf|max:5120',
                'hasil_ipmlh' => 'nullable|file|mimes:pdf|max:5120',
                'rencana_gpblhs_4_tahunan' => 'nullable|file|mimes:pdf|max:5120',
                'rencana_gpblhs_tahunan' => 'nullable|file|mimes:pdf|max:5120',
                'dokumentasi_penyusunan' => 'nullable|file|mimes:pdf|max:5120',
                'sk_tim_sekolah' => 'nullable|file|mimes:pdf|max:5120',
            ]);

            // Decode JSON anggota_tim
            $anggota_tim = null;
            if ($request->has('anggota_tim') && !empty($request->input('anggota_tim'))) {
                try {
                    $anggota_tim = json_decode($request->input('anggota_tim'), true);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format anggota tim tidak valid'
                    ], 422);
                }
            }

            Log::info("A5 Draft Request", [
                'ketua_tim' => $validated['ketua_tim'],
                'jumlah_kader' => $validated['jumlah_kader_adiwiyata'],
                'anggota_tim' => $anggota_tim,
                'files_received' => $request->files->keys()
            ]);

            // ================================================================
            // 2. GET OR CREATE SUBMISSION
            // ================================================================
            $submission = Submission::where('status', 'draft')
                ->latest()
                ->first();

            if (!$submission) {
                $submission = Submission::create([
                    'status' => 'draft',
                    'ketua_tim' => $validated['ketua_tim'] ?? null,
                    'jumlah_kader_adiwiyata' => $validated['jumlah_kader_adiwiyata'] ?? null,
                    'anggota_tim' => $anggota_tim ?? null,
                ]);

                Log::info("New draft submission created", ['submission_id' => $submission->id]);
            } else {
                $submission->update([
                    'ketua_tim' => $validated['ketua_tim'] ?? $submission->ketua_tim,
                    'jumlah_kader_adiwiyata' => $validated['jumlah_kader_adiwiyata'] ?? $submission->jumlah_kader_adiwiyata,
                    'anggota_tim' => $anggota_tim ?? $submission->anggota_tim,
                ]);

                Log::info("Draft submission updated", ['submission_id' => $submission->id]);
            }

            // ================================================================
            // 3. HANDLE FILE UPLOADS
            // ================================================================
            $fileFields = [
                'evaluasi_diri_sekolah',
                'hasil_ipmlh',
                'rencana_gpblhs_4_tahunan',
                'rencana_gpblhs_tahunan',
                'dokumentasi_penyusunan',
                'sk_tim_sekolah',
            ];

            $uploadedFiles = [];

            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    try {
                        $file = $request->file($fieldName);

                        // Validate file
                        if (!$file->isValid()) {
                            throw new \Exception("File {$fieldName} tidak valid");
                        }

                        // Delete old file if exists
                        $oldFileEvidence = FileEvidence::where('submission_id', $submission->id)
                            ->where('field_name', $fieldName)
                            ->first();

                        if ($oldFileEvidence) {
                            $this->fileEvidenceService->deleteFile($oldFileEvidence);
                            Log::info("Old file deleted", ['field' => $fieldName]);
                        }

                        // Store new file
                        $fileEvidence = $this->fileEvidenceService->storeFile(
                            submission: $submission,
                            file: $file,
                            fieldName: $fieldName
                        );

                        $uploadedFiles[$fieldName] = $fileEvidence;

                        Log::info("File uploaded for A5", [
                            'submission_id' => $submission->id,
                            'field_name' => $fieldName,
                            'file_id' => $fileEvidence->id,
                            'file_size' => $fileEvidence->file_size,
                        ]);

                    } catch (\Exception $e) {
                        Log::error("File upload error for {$fieldName}", [
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }
                }
            }

            // ================================================================
            // 4. RESPONSE
            // ================================================================
            return response()->json([
                'success' => true,
                'message' => 'Draft A5 berhasil disimpan',
                'data' => [
                    'submission_id' => $submission->id,
                    'status' => $submission->status,
                    'ketua_tim' => $submission->ketua_tim,
                    'jumlah_kader_adiwiyata' => $submission->jumlah_kader_adiwiyata,
                    'files_uploaded' => count($uploadedFiles),
                    'uploaded_files' => array_map(fn($fe) => [
                        'id' => $fe->id,
                        'field_name' => $fe->field_name,
                        'original_name' => $fe->original_name,
                        'file_size' => $fe->file_size,
                    ], $uploadedFiles),
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning("A5 validation error", ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("A5 save draft error", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest draft submission for A5
     */
    public function getDraftA5(Request $request)
    {
        try {
            $submission = Submission::where('status', 'draft')
                ->with('fileEvidences')
                ->latest()
                ->first();

            if (!$submission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada draft ditemukan',
                    'data' => null
                ], 404);
            }

            // Get files for A5 fields
            $files = $submission->fileEvidences()
                ->whereIn('field_name', [
                    'evaluasi_diri_sekolah',
                    'hasil_ipmlh',
                    'rencana_gpblhs_4_tahunan',
                    'rencana_gpblhs_tahunan',
                    'dokumentasi_penyusunan',
                    'sk_tim_sekolah',
                ])
                ->get()
                ->mapWithKeys(fn($fe) => [
                    $fe->field_name => [
                        'id' => $fe->id,
                        'original_name' => $fe->original_name,
                        'file_size' => $fe->file_size,
                        'uploaded_at' => $fe->uploaded_at,
                    ]
                ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'submission_id' => $submission->id,
                    'status' => $submission->status,
                    'ketua_tim' => $submission->ketua_tim,
                    'jumlah_kader_adiwiyata' => $submission->jumlah_kader_adiwiyata,
                    'anggota_tim' => $submission->anggota_tim ?? [],
                    'files' => $files,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("Get draft A5 error", [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}