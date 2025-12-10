<?php

namespace App\Services;

use App\Models\FileEvidence;
use App\Models\Submission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileEvidenceService
{
    /**
     * Store file dan buat FileEvidence record
     */
    public function storeFile(
        Submission $submission,
        UploadedFile $file,
        string $fieldName
    ): FileEvidence {
        // ================================================================
        // 1. GENERATE DETERMINISTIC FILENAME
        // ================================================================
        $fileHash = hash_file('sha256', $file->getPathname());
        $timestamp = now()->getTimestamp();
        
        $filename = "{$submission->id}_{$fieldName}_{substr($fileHash, 0, 8)}_{$timestamp}.pdf";

        // ================================================================
        // 2. STORE FILE TO PRIVATE DISK
        // ================================================================
        $storagePath = "submissions/{$submission->id}";
        
        Storage::disk('private')->putFileAs(
            $storagePath,
            $file,
            $filename
        );

        $fullPath = "{$storagePath}/{$filename}";

        // ================================================================
        // 3. CREATE FILE EVIDENCE RECORD
        // ================================================================
        $fileEvidence = FileEvidence::create([
            'submission_id' => $submission->id,
            'field_name' => $fieldName,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $fullPath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'hash' => $fileHash,
            'uploaded_at' => now(),
        ]);

        return $fileEvidence;
    }

    /**
     * Delete file dan FileEvidence record
     */
    public function deleteFile(FileEvidence $fileEvidence): void
    {
        // Delete physical file
        if (Storage::disk('private')->exists($fileEvidence->file_path)) {
            Storage::disk('private')->delete($fileEvidence->file_path);
        }

        // Delete database record
        $fileEvidence->delete();
    }

    /**
     * Get file preview/download response
     */
    public function getFileResponse(FileEvidence $fileEvidence, string $type = 'inline')
    {
        if (!Storage::disk('private')->exists($fileEvidence->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        $fileContent = Storage::disk('private')->get($fileEvidence->file_path);
        $disposition = $type === 'download' ? 'attachment' : 'inline';

        return response()->make($fileContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "{$disposition}; filename=\"{$fileEvidence->original_name}\"",
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }
}