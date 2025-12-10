<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\FileEvidence;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class SubmissionService
{
    /**
     * Store submission with all files
     */
    public function storeSubmission(array $data, int $schoolId): Submission
    {
        // Create submission
        $submission = Submission::create([
            'school_id' => $schoolId,
            'a5_data' => $data['a5_data'],
            'a6_data' => $data['a6_data'],
            'a7_data' => $data['a7_data'],
            'a8_data' => $data['a8_data'],
            'is_draft' => $data['is_draft'],
            'status' => $data['is_draft'] ? 'draft' : 'submitted'
        ]);

        // Store A5 files
        $this->storeA5Files($submission, $data);

        // Store A6 files
        $this->storeA6Files($submission, $data);

        // Store A8 file
        $this->storeA8File($submission, $data);

        return $submission;
    }

    /**
     * Store A5 files
     */
    private function storeA5Files(Submission $submission, array $data): void
    {
        $a5Files = [
            'evaluasi_diri_sekolah',
            'hasil_ipmlh',
            'rencana_gpblhs_4_tahunan',
            'rencana_gpblhs_tahunan',
            'dokumentasi_penyusunan',
            'sk_tim_sekolah'
        ];

        foreach ($a5Files as $fieldName) {
            $key = "a5_{$fieldName}";
            if (isset($data[$key]) && $data[$key] instanceof UploadedFile) {
                $this->storeFileEvidence(
                    $submission,
                    $key,
                    $data[$key]
                );
            }
        }
    }

    /**
     * Store A6 indicator files
     */
    private function storeA6Files(Submission $submission, array $data): void
    {
        // Get all keys starting with 'a6_bukti_'
        foreach ($data as $key => $file) {
            if (str_starts_with($key, 'a6_bukti_') && $file instanceof UploadedFile) {
                $this->storeFileEvidence($submission, $key, $file);
            }
        }
    }

    /**
     * Store A8 file
     */
    private function storeA8File(Submission $submission, array $data): void
    {
        if (isset($data['a8_dokumen_persetujuan']) && $data['a8_dokumen_persetujuan'] instanceof UploadedFile) {
            $this->storeFileEvidence(
                $submission,
                'a8_dokumen_persetujuan',
                $data['a8_dokumen_persetujuan']
            );
        }
    }

    /**
     * Store single file and create evidence record
     * 
     * CRITICAL: Deterministic naming to prevent collisions
     */
    private function storeFileEvidence(Submission $submission, string $fieldName, UploadedFile $file): void
    {
        // Calculate file hash for deduplication
        $fileHash = hash('sha256', file_get_contents($file));

        // Check if file already stored
        $existing = FileEvidence::where('hash', $fileHash)->first();
        if ($existing) {
            // Link to existing file
            FileEvidence::create([
                'submission_id' => $submission->id,
                'field_name' => $fieldName,
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $existing->storage_path,
                'disk' => $existing->disk,
                'mime_type' => $existing->mime_type,
                'file_size' => $existing->file_size,
                'hash' => $fileHash
            ]);
            return;
        }

        // Generate deterministic path
        $storagePath = $this->generateStoragePath($submission, $fieldName, $file);

        // Store file
        Storage::disk('private')->put(
            $storagePath,
            file_get_contents($file)
        );

        // Create evidence record
        FileEvidence::create([
            'submission_id' => $submission->id,
            'field_name' => $fieldName,
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $storagePath,
            'disk' => 'private',
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'hash' => $fileHash
        ]);
    }

    /**
     * Generate deterministic storage path
     * Format: submissions/{submissionId}/{fieldName}_{timestamp}.pdf
     */
    private function generateStoragePath(Submission $submission, string $fieldName, UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $filename = "{$fieldName}_{$timestamp}.{$extension}";

        return "submissions/{$submission->id}/{$filename}";
    }
}