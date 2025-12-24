<?php

namespace App\Http\Controllers;

use App\Models\Rencana;
use App\Models\BuktiSelfAssessment;
use App\Models\Pernyataan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class FileEvidenceController extends Controller
{
    /**
     * Get file record based on type and id
     */
    private function getFileRecord($type, $id)
    {
        switch ($type) {
            case 'a5':
                return Rencana::findOrFail($id);
            case 'a6':
                return BuktiSelfAssessment::findOrFail($id);
            case 'a8':
                return Pernyataan::findOrFail($id);
            default:
                abort(404, 'Tipe file tidak valid');
        }
    }

    /**
     * Get file path from record based on type
     */
    private function getFilePath($type, $record)
    {
        switch ($type) {
            case 'a5':
            case 'a6':
                return $record->path_file;
            case 'a8':
                return $record->bukti_persetujuan;
            default:
                return null;
        }
    }

    /**
     * Preview file (display inline di browser)
     */
    public function preview($type, $id)
    {
        $record = $this->getFileRecord($type, $id);
        $filePath = $this->getFilePath($type, $record);

        if (!$filePath) {
            abort(404, 'Path file tidak ditemukan');
        }

        // Cek file exists - path sudah termasuk 'storage/input_A5' dll
        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $fileContent = Storage::disk('local')->get($filePath);
        $fileName = basename($filePath);

        return Response::make($fileContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    /**
     * Download file
     */
    public function download($type, $id)
    {
        $record = $this->getFileRecord($type, $id);
        $filePath = $this->getFilePath($type, $record);

        if (!$filePath) {
            abort(404, 'Path file tidak ditemukan');
        }

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage');
        }

        $fileName = basename($filePath);
        $fullPath = Storage::disk('local')->path($filePath);

        return response()->download($fullPath, $fileName);
    }
}