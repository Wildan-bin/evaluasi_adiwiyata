<?php

namespace App\Http\Controllers;

use App\Models\AdministrasiSekolah;
use App\Models\AdministrasiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdministrasiSekolahController extends Controller
{
    // Tampilkan form (untuk user)
    public function create()
    {
        abort_if(Auth::user()->role !== 'user', 403);

        // Cek apakah user sudah punya data sebelumnya
        $existingData = AdministrasiSekolah::where('user_id', Auth::id())->first();

        return Inertia::render('Profile/AdministrasiSekolah', [
            'existingData' => $existingData,
        ]);
    }

    // Simpan data (create atau update)
    public function store(Request $request)
    {
        abort_if(Auth::user()->role !== 'user', 403);

        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'required|string|max:255|unique:administrasi_sekolah,npsn,' . Auth::id() . ',user_id',
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
            'tim_adiwiyata' => 'nullable|array',
        ]);

        // Cek apakah data sudah ada
        $administrasi = AdministrasiSekolah::where('user_id', Auth::id())->first();

        if ($administrasi) {
            // Update existing data
            $oldData = $administrasi->toArray();
            $administrasi->update($validated);

            // Create log
            AdministrasiLog::create([
                'administrasi_sekolah_id' => $administrasi->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'User memperbarui data administrasi sekolah',
                'old_data' => $oldData,
                'new_data' => $validated,
                'created_at' => now(),
            ]);

            $message = 'Data berhasil diperbarui!';
        } else {
            // Create new data
            $administrasi = AdministrasiSekolah::create([
                'user_id' => Auth::id(),
                ...$validated,
            ]);

            // Create log
            AdministrasiLog::create([
                'administrasi_sekolah_id' => $administrasi->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => 'User membuat data administrasi sekolah baru',
                'old_data' => null,
                'new_data' => $validated,
                'created_at' => now(),
            ]);

            $message = 'Data berhasil disimpan!';
        }

        // Redirect ke preview
        return redirect()->route('administrasi-preview', $administrasi->id)
            ->with('success', $message);
    }

    // Preview data
    public function preview($id)
    {
        $administrasi = AdministrasiSekolah::with('user')->findOrFail($id);

        // Pastikan user hanya bisa lihat data miliknya sendiri (kecuali admin)
        if (Auth::user()->role !== 'admin' && $administrasi->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Profile/AdministrasiPreview', [
            'administrasi' => $administrasi,
        ]);
    }

    // Submit untuk review admin
    public function submit($id)
    {
        $administrasi = AdministrasiSekolah::findOrFail($id);

        abort_if($administrasi->user_id !== Auth::id(), 403);
        abort_if($administrasi->status !== 'draft', 400, 'Data sudah disubmit sebelumnya');

        $administrasi->update(['status' => 'submitted']);

        // Create log
        AdministrasiLog::create([
            'administrasi_sekolah_id' => $administrasi->id,
            'user_id' => Auth::id(),
            'action' => 'submitted',
            'description' => 'User submit data untuk review admin',
            'old_data' => ['status' => 'draft'],
            'new_data' => ['status' => 'submitted'],
            'created_at' => now(),
        ]);

        return redirect()->route('administrasi-preview', $id)
            ->with('success', 'Data berhasil disubmit untuk review!');
    }

    // List logs (untuk admin)
    public function logs()
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $logs = AdministrasiLog::with(['administrasiSekolah', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Profile/AdministrasiLogs', [
            'logs' => $logs,
        ]);
    }

    // Detail log (untuk admin)
    public function logDetail($id)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $log = AdministrasiLog::with(['administrasiSekolah', 'user'])->findOrFail($id);

        return Inertia::render('Profile/AdministrasiLogDetail', [
            'log' => $log,
        ]);
    }
}
