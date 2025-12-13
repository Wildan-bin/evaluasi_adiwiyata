<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            // ====================================================================
            // FILE VALIDATION - Strict backend validation (UX validation was done frontend)
            // ====================================================================
            'evaluasi_diri_sekolah' => 'required|file|mimes:pdf|max:5120', // 5MB
            'hasil_ipmlh' => 'required|file|mimes:pdf|max:5120',
            'rencana_gpblhs_4_tahunan' => 'required|file|mimes:pdf|max:5120',
            'rencana_gpblhs_tahunan' => 'required|file|mimes:pdf|max:5120',
            'dokumentasi_penyusunan' => 'required|file|mimes:pdf|max:5120',
            'sk_tim_sekolah' => 'required|file|mimes:pdf|max:5120',
            'dokumen_persetujuan' => 'required|file|mimes:pdf|max:5120',

            // ====================================================================
            // PAYLOAD (JSON) - All non-file data
            // ====================================================================
            'payload' => 'required|json',
            'payload.ketua_tim' => 'required|string|min:3|max:100',
            'payload.jumlah_kader_adiwiyata' => 'required|integer|min:1|max:1000',
            'payload.anggota_tim' => 'required|array|min:1|max:50',
            'payload.anggota_tim.*.name' => 'required|string|min:3|max:100',
            'payload.anggota_tim.*.role' => 'required|string|min:3|max:100',
            'payload.anggota_tim.*.contact' => 'required|string|min:5|max:100',

            // A6 indicators
            'payload.indicators' => 'required|array',

            // A7 kebutuhan pendampingan
            'payload.kebutuhan_pendampingan' => 'required|array|min:1',
            'payload.deskripsi_masalah' => 'required|string|min:20|max:5000',
            'payload.tanggal_permintaan' => 'required|date',

            // A8 agreements
            'payload.persetujuan_kebenaran_data' => 'required|boolean|in:true',
            'payload.persetujuan_publikasi' => 'required|boolean|in:true'
        ];
    }

    public function messages()
    {
        return [
            'evaluasi_diri_sekolah.required' => 'Dokumen evaluasi diri sekolah wajib diunggah',
            'evaluasi_diri_sekolah.mimes' => 'Dokumen harus berformat PDF',
            'evaluasi_diri_sekolah.max' => 'Ukuran dokumen tidak boleh lebih dari 5MB',

            'payload.required' => 'Data proposal tidak lengkap',
            'payload.ketua_tim.required' => 'Nama ketua tim wajib diisi',
            'payload.persetujuan_kebenaran_data.in' => 'Anda harus menyetujui kebenaran data',
            'payload.persetujuan_publikasi.in' => 'Anda harus menyetujui publikasi praktik baik'
        ];
    }
}