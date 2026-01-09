<?php
// filepath: app/Http/Controllers/AdministrasiSekolahController.php

namespace App\Http\Controllers;

use App\Models\AdministrasiSekolah;
use App\Models\Ketua;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdministrasiSekolahController extends Controller
{
    // GET /administrasi-sekolah
    public function create()
    {
        abort_if(Auth::user()->role !== 'user', 403);

        $administrasi = AdministrasiSekolah::with(['ketua.anggota'])
            ->where('user_id', Auth::id())
            ->first();

        return Inertia::render('Profile/AdministrasiSekolah', [
            'administrasi' => $administrasi,
            'mode' => $administrasi ? 'view' : 'create',
        ]);
    }

    // POST /administrasi-sekolah
    public function store(Request $request)
    {
        abort_if(Auth::user()->role !== 'user', 403);

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_maps_url' => 'nullable|url',
            'nama_kepala_sekolah' => 'required|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:255',
            'telp_kepala_sekolah' => 'nullable|string|max:20',
            'ketua.nama' => 'required|string|max:255',
            'ketua.nip' => 'nullable|string|max:255',
            'ketua.email' => 'nullable|email|max:255',
            'ketua.nomor_telepon' => 'nullable|string|max:20',
            'anggota' => 'nullable|array',
            'anggota.*.nama' => 'required|string|max:255',
            'anggota.*.nip' => 'nullable|string|max:255',
            'anggota.*.email' => 'nullable|email|max:255',
            'anggota.*.nomor_telepon' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $administrasi = AdministrasiSekolah::where('user_id', Auth::id())->first();

            $administrasiData = [
                'nama_sekolah' => $validated['nama_sekolah'],
                'npsn' => $validated['npsn'],
                'alamat' => $validated['alamat'],
                'kelurahan' => $validated['kelurahan'],
                'kecamatan' => $validated['kecamatan'],
                'kota' => $validated['kota'],
                'provinsi' => $validated['provinsi'],
                'kode_pos' => $validated['kode_pos'] ?? null,
                'telepon' => $validated['telepon'] ?? null,
                'email' => $validated['email'] ?? null,
                'website' => $validated['website'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'google_maps_url' => $validated['google_maps_url'] ?? null,
                'nama_kepala_sekolah' => $validated['nama_kepala_sekolah'],
                'nip_kepala_sekolah' => $validated['nip_kepala_sekolah'] ?? null,
                'telp_kepala_sekolah' => $validated['telp_kepala_sekolah'] ?? null,
            ];

            if ($administrasi) {
                // ✅ Update - hanya boleh edit jika status unverified
                if ($administrasi->status !== 'unverified') {
                    DB::rollBack();
                    return back()->withErrors(['status' => 'Data tidak dapat diedit. Status: ' . $administrasi->status]);
                }

                $administrasi->update([
                    ...$administrasiData,
                    'last_updated_by_user_at' => now(),
                    'status' => 'pending', // ✅ Set ke pending lagi setelah update
                ]);

                $message = 'Data berhasil diperbarui dan menunggu verifikasi!';
            } else {
                // ✅ Create - langsung pending
                $administrasi = AdministrasiSekolah::create([
                    'user_id' => Auth::id(),
                    ...$administrasiData,
                    'status' => 'pending', // ✅ Default pending
                    'submitted_at' => now(),
                ]);

                $message = 'Data berhasil disimpan dan menunggu verifikasi!';
            }

            // Upsert Ketua
            $ketua = Ketua::updateOrCreate(
                ['sekolah_id' => $administrasi->id],
                $validated['ketua']
            );

            // Sync Anggota
            Anggota::where('sekolah_id', $administrasi->id)->delete();

            if (isset($validated['anggota']) && is_array($validated['anggota']) && count($validated['anggota']) > 0) {
                foreach ($validated['anggota'] as $anggotaData) {
                    Anggota::create([
                        'ketua_id' => $ketua->id,
                        'sekolah_id' => $administrasi->id,
                        'nama' => $anggotaData['nama'],
                        'nip' => $anggotaData['nip'] ?? null,
                        'email' => $anggotaData['email'] ?? null,
                        'nomor_telepon' => $anggotaData['nomor_telepon'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('administrasi-sekolah')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // GET /administrasi-sekolah/preview/{id}
    public function preview($id)
    {
        // $id dikirim dari UI sebagai user_id; cari AdministrasiSekolah berdasarkan user_id
        $administrasi = AdministrasiSekolah::with(['user', 'ketua.anggota', 'verifiedByAdmin'])
            ->where('user_id', $id)
            ->firstOrFail();

        if (Auth::user()->role !== 'admin' && $administrasi->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika request dari AJAX/modal, return JSON dengan dokumen spesifik
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            $docType = request()->query('docType', 'rencana');
            $documents = [];
            $schoolName = $administrasi->nama_sekolah ?? '-';

            // Normalisasi path_file agar tidak double "storage/" atau "public/"
            $normalizePath = function ($path) {
                if (!$path) {
                    return null;
                }

                $path = ltrim($path, '/');

                if (str_starts_with($path, 'public/')) {
                    $path = substr($path, 7);
                }

                if (str_starts_with($path, 'storage/')) {
                    $path = substr($path, 8);
                }

                return ltrim($path, '/');
            };

            // Fetch dokumen berdasarkan tipe
            switch ($docType) {
                case 'rencana':
                    $documents = \App\Models\Rencana::where('user_id', $administrasi->user_id)
                        ->select('id', 'indikator as title', 'path_file', 'created_at')
                        ->get()
                        ->map(function ($item) use ($normalizePath) {
                            $item['path_file'] = $normalizePath($item['path_file']);
                            return $item;
                        })
                        ->toArray();
                    break;
                case 'self_assessment':
                    $documents = \App\Models\BuktiSelfAssessment::where('user_id', $administrasi->user_id)
                        ->select('id', 'kriteria as title', 'path_file', 'created_at')
                        ->get()
                        ->map(function ($item) use ($normalizePath) {
                            $item['path_file'] = $normalizePath($item['path_file']);
                            return $item;
                        })
                        ->toArray();
                    break;
                case 'kebutuhan_pendampingan':
                    $documents = \App\Models\Pendampingan::where('user_id', $administrasi->user_id)
                        ->select('id', 'topik as title', 'path_file', 'created_at')
                        ->get()
                        ->map(function ($item) use ($normalizePath) {
                            $item['path_file'] = $normalizePath($item['path_file']);
                            return $item;
                        })
                        ->toArray();
                    break;
                case 'pernyataan':
                    $documents = \App\Models\Pernyataan::where('user_id', $administrasi->user_id)
                        ->select('id', 'judul as title', 'path_file', 'created_at')
                        ->get()
                        ->map(function ($item) use ($normalizePath) {
                            $item['path_file'] = $normalizePath($item['path_file']);
                            return $item;
                        })
                        ->toArray();
                    break;
            }

            return response()->json([
                'documents' => $documents,
                'schoolName' => $schoolName,
            ]);
        }

        return Inertia::render('Profile/AdministrasiPreview', [
            'administrasi' => $administrasi,
            'user' => Auth::user(),
        ]);
    }

    // POST /administrasi-sekolah/request-edit (deprecated - tidak perlu lagi)
    public function requestEdit(Request $request)
    {
        return back()->withErrors(['deprecated' => 'Fitur ini tidak digunakan lagi']);
    }

    // ==================== ADMIN ENDPOINTS ====================

    // GET /administrasi-sekolah/submissions
    public function submissions(Request $request)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $query = AdministrasiSekolah::with(['user', 'ketua.anggota'])
            ->orderBy('submitted_at', 'desc');

        // ✅ Filter by status
        if ($request->status && in_array($request->status, ['pending', 'unverified', 'verified'])) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_sekolah', 'like', "%{$request->q}%")
                  ->orWhere('npsn', 'like', "%{$request->q}%")
                  ->orWhereHas('user', function ($q) use ($request) {
                      $q->where('name', 'like', "%{$request->q}%");
                  });
            });
        }

        $submissions = $query->paginate(12);

        return Inertia::render('Admin/AdministrasiSubmissions', [
            'submissions' => $submissions,
            'filters' => $request->only(['status', 'q']),
        ]);
    }

    // PATCH /administrasi-sekolah/{id}/verify
    public function verify(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $request->validate([
            'status' => 'required|in:pending,unverified,verified',
        ]);

        $administrasi = AdministrasiSekolah::findOrFail($id);

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->status === 'verified') {
            $updateData['verified_at'] = now();
            $updateData['verified_by_admin_id'] = Auth::id();
        } else {
            $updateData['verified_at'] = null;
            $updateData['verified_by_admin_id'] = null;
        }

        $administrasi->update($updateData);

        $statusText = match($request->status) {
            'pending' => 'Pending',
            'unverified' => 'Unverified (dapat diedit user)',
            'verified' => 'Verified',
        };

        return back()->with('success', "Status berhasil diubah menjadi: {$statusText}");
    }

    // PATCH /administrasi-sekolah/{id}/note
    public function updateNote(Request $request, $id)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $request->validate([
            'admin_note' => 'nullable|string|max:5000',
        ]);

        $administrasi = AdministrasiSekolah::findOrFail($id);
        $administrasi->update(['admin_note' => $request->admin_note]);

        return back()->with('success', 'Catatan admin berhasil disimpan!');
    }

    // PATCH /administrasi-sekolah/{id}/unlock (deprecated - pakai verify dengan status unverified)
    public function unlockForEdit($id)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $administrasi = AdministrasiSekolah::findOrFail($id);

        $administrasi->update([
            'status' => 'unverified',
            'verified_at' => null,
            'verified_by_admin_id' => null,
        ]);

        return back()->with('success', 'Data berhasil di-unlock untuk edit!');
    }

    // STREAM FILES SECURELY FOR ADMIN/OWNER
    // GET /administrasi-sekolah/{id}/file?path=path_file
    public function streamFile(Request $request, $id)
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Only admin or the owner (user id matches) can access
        if (!($user->role === 'admin' || (int) $user->id === (int) $id)) {
            abort(403);
        }

        $rawPath = (string) $request->query('path', '');
        if ($rawPath === '') {
            abort(404);
        }

        // Normalize path to avoid traversal and duplicated prefixes
        $path = ltrim($rawPath, '/');
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }

        // Prevent directory traversal
        if (str_contains($path, '..')) {
            abort(403);
        }

        // Ensure file exists in public disk
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        // Stream inline so browser can preview PDF/images
        return response()->file(Storage::disk('public')->path($path));
    }
}
