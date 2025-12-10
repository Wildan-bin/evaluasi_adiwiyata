<?php

namespace App\Services\Submissions;

use App\Models\SubmissionFile;
use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public function storeFile(
        int $submissionId,
        string $section,
        string $field,
        UploadedFile $file,
        ?int $indicatorNumber = null
    ): SubmissionFile {

        $storedName = uniqid().'.'.$file->getClientOriginalExtension();

        $path = $file->storeAs(
            "submissions/{$submissionId}/{$section}",
            $storedName
        );

        return SubmissionFile::create([
            'submission_id'  => $submissionId,
            'section'        => $section,
            'field'          => $field,
            'indicator_number' => $indicatorNumber,
            'original_name'  => $file->getClientOriginalName(),
            'stored_name'    => $storedName,
            'path'           => $path,
            'mime'           => $file->getMimeType(),
            'size'           => $file->getSize(),
        ]);
    }
}
