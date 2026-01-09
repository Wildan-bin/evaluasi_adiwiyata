<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FileUploadController extends Controller
{
    /**
     * Maximum file size: 5 MB
     */
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB in bytes

    /**
     * Allowed MIME types
     */
    const ALLOWED_MIME_TYPES = [
        'application/pdf',
    ];

    /**
     * Upload file with automatic renaming based on indicator
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:pdf', 'max:5120'], // 5MB max
            'category' => ['required', 'in:administrasi,self_assessment,rencana,pendampingan,pernyataan'],
            'indikator' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $file = $request->file('file');
        $category = $request->input('category');
        $indikator = $request->input('indikator');
        $section = $request->input('section', '');

        try {
            // Check file size
            if ($file->getSize() > self::MAX_FILE_SIZE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ukuran file melebihi 5 MB'
                ], 422);
            }

            // Check MIME type
            if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya file PDF yang diperbolehkan'
                ], 422);
            }

            // Generate system filename
            $systemFilename = $this->generateSystemFilename(
                $user->name,
                $indikator,
                $section,
                $file->getClientOriginalExtension()
            );

            // Store file
            $storagePath = Storage::disk('public')->putFileAs(
                "uploads/{$category}",
                $file,
                $systemFilename
            );

            // Create file upload record
            $fileUpload = FileUpload::create([
                'user_id' => $user->id,
                'original_filename' => $file->getClientOriginalName(),
                'system_filename' => $systemFilename,
                'file_path' => $storagePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'category' => $category,
                'indikator' => $indikator,
                'section' => $section,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'data' => [
                    'id' => $fileUpload->id,
                    'original_filename' => $fileUpload->original_filename,
                    'file_size' => $fileUpload->human_file_size,
                    'uploaded_at' => $fileUpload->created_at->format('d M Y H:i'),
                    'status' => $fileUpload->status_label,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate system filename
     * Format: NamaUser_Indikator_Section_timestamp.ext
     *
     * @param string $userName
     * @param string $indikator
     * @param string $section
     * @param string $extension
     * @return string
     */
    private function generateSystemFilename($userName, $indikator, $section, $extension)
    {
        // Clean username (remove special characters)
        $cleanUserName = Str::slug($userName, '_');

        // Clean indikator
        $cleanIndikator = Str::slug($indikator, '_');

        // Clean section
        $cleanSection = $section ? Str::slug($section, '_') : '';

        // Timestamp
        $timestamp = now()->format('YmdHis');

        // Build filename
        $parts = array_filter([$cleanUserName, $cleanIndikator, $cleanSection, $timestamp]);

        return implode('_', $parts) . '.' . $extension;
    }

    /**
     * Get user's uploaded files
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserFiles(Request $request)
    {
        $user = Auth::user();
        $category = $request->input('category');

        $query = FileUpload::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        $files = $query->get()->map(function ($file) {
            return [
                'id' => $file->id,
                'original_filename' => $file->original_filename,
                'indikator' => $file->indikator,
                'section' => $file->section,
                'file_size' => $file->human_file_size,
                'status' => $file->status,
                'status_label' => $file->status_label,
                'status_color' => $file->status_color,
                'uploaded_at' => $file->created_at->format('d M Y H:i'),
                'mentor_comment' => $file->mentor_comment,
                'revision_number' => $file->revision_number,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }

    /**
     * Preview file (inline)
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function preview($id)
    {
        $user = Auth::user();
        $file = FileUpload::findOrFail($id);

        // Check authorization (user owns file or user is admin/mentor)
        if ($file->user_id !== $user->id && !in_array($user->role, ['admin', 'mentor'])) {
            abort(403, 'Tidak memiliki akses ke file ini');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file(Storage::disk('public')->path($file->file_path));
    }

    /**
     * Download file
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $user = Auth::user();
        $file = FileUpload::findOrFail($id);

        // Check authorization
        if ($file->user_id !== $user->id && !in_array($user->role, ['admin', 'mentor'])) {
            abort(403, 'Tidak memiliki akses ke file ini');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(Storage::disk('public')->path($file->file_path), $file->original_filename);
    }

    /**
     * Delete file (only owner can delete if status is pending or rejected)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $user = Auth::user();
        $file = FileUpload::findOrFail($id);

        // Only owner can delete
        if ($file->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses untuk menghapus file ini'
            ], 403);
        }

        // Cannot delete approved files
        if ($file->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'File yang sudah disetujui tidak dapat dihapus'
            ], 422);
        }

        try {
            // Delete physical file
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // Delete record
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin/Mentor: Get all pending files
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingFiles()
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'mentor'])) {
            abort(403, 'Akses ditolak');
        }

        $files = FileUpload::with(['user'])
            ->pending()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'user_name' => $file->user->name,
                    'user_id' => $file->user_id,
                    'original_filename' => $file->original_filename,
                    'indikator' => $file->indikator,
                    'category' => $file->category,
                    'section' => $file->section,
                    'file_size' => $file->human_file_size,
                    'uploaded_at' => $file->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }

    /**
     * Admin/Mentor: Review file (approve/reject)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function review(Request $request, $id)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'mentor'])) {
            abort(403, 'Akses ditolak');
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:approved,rejected,revision_requested'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = FileUpload::findOrFail($id);

        try {
            $file->update([
                'status' => $request->input('status'),
                'reviewed_by' => $user->id,
                'reviewed_at' => now(),
                'mentor_comment' => $request->input('comment'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review berhasil disimpan',
                'data' => [
                    'status' => $file->status_label,
                    'reviewed_at' => $file->reviewed_at->format('d M Y H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan review: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get files by user (for admin to view specific user's files)
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilesByUser($userId)
    {
        $currentUser = Auth::user();

        if (!in_array($currentUser->role, ['admin', 'mentor'])) {
            abort(403, 'Akses ditolak');
        }

        $targetUser = User::findOrFail($userId);

        $files = FileUpload::where('user_id', $userId)
            ->with(['reviewer'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'original_filename' => $file->original_filename,
                    'indikator' => $file->indikator,
                    'category' => $file->category,
                    'section' => $file->section,
                    'file_size' => $file->human_file_size,
                    'status' => $file->status,
                    'status_label' => $file->status_label,
                    'status_color' => $file->status_color,
                    'uploaded_at' => $file->created_at->format('d M Y H:i'),
                    'reviewed_by' => $file->reviewer ? $file->reviewer->name : null,
                    'reviewed_at' => $file->reviewed_at ? $file->reviewed_at->format('d M Y H:i') : null,
                    'mentor_comment' => $file->mentor_comment,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $targetUser->id,
                    'name' => $targetUser->name,
                    'email' => $targetUser->email,
                ],
                'files' => $files,
            ]
        ]);
    }

    /**
     * Get latest uploads (for admin dashboard)
     *
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestUploads($limit = 10)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['admin', 'mentor'])) {
            abort(403, 'Akses ditolak');
        }

        $files = FileUpload::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'user_name' => $file->user->name,
                    'user_id' => $file->user_id,
                    'original_filename' => $file->original_filename,
                    'category' => $file->category,
                    'indikator' => $file->indikator,
                    'status' => $file->status,
                    'status_label' => $file->status_label,
                    'status_color' => $file->status_color,
                    'uploaded_at' => $file->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $files
        ]);
    }
}
