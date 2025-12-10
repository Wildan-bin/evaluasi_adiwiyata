<?php

namespace App\Http\Controllers;

use App\Models\FileEvidence;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class FileEvidenceController extends Controller
{
    /**
     * Preview file (display inline di browser)
     */
    public function preview($id)
    {
        $fileEvidence = FileEvidence::findOrFail($id);
        
        // Authorize: only user yang submit atau admin
        $this->authorize('view', $fileEvidence);
        
        // Cek file exists
        if (!Storage::disk('private')->exists($fileEvidence->file_path)) {
            abort(404, 'File tidak ditemukan');
        }
        
        $fileContent = Storage::disk('private')->get($fileEvidence->file_path);
        
        return Response::make($fileContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileEvidence->original_name . '"',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    /**
     * Download file
     */
    public function download($id)
    {
        $fileEvidence = FileEvidence::findOrFail($id);
        
        // Authorize
        $this->authorize('view', $fileEvidence);
        
        if (!Storage::disk('private')->exists($fileEvidence->file_path)) {
            abort(404, 'File tidak ditemukan');
        }
        
        return Storage::disk('private')->download(
            $fileEvidence->file_path,
            $fileEvidence->original_name
        );
    }
}