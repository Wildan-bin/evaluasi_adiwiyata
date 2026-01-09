<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrasiSekolah extends Model
{
    use HasFactory;

    protected $table = 'administrasi_sekolah';

    protected $fillable = [
        'user_id',
        'nama_sekolah',
        'npsn',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'website',
        'latitude',
        'longitude',
        'google_maps_url',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah',
        'telp_kepala_sekolah',
        'status', // ✅ pending, unverified, verified
        'verified_at',
        'verified_by_admin_id',
        'submitted_at',
        'last_updated_by_user_at',
        'admin_note',
        'edit_requested',
        'edit_request_reason',
        'edit_requested_at',
        'edit_request_handled_at',
        'edit_request_handled_by_admin_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'edit_requested' => 'boolean',
        'verified_at' => 'datetime',
        'submitted_at' => 'datetime',
        'last_updated_by_user_at' => 'datetime',
        'edit_requested_at' => 'datetime',
        'edit_request_handled_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ketua()
    {
        return $this->hasOne(Ketua::class, 'sekolah_id');
    }

    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'sekolah_id');
    }

    public function verifiedByAdmin()
    {
        return $this->belongsTo(User::class, 'verified_by_admin_id');
    }

    public function editRequestHandledByAdmin()
    {
        return $this->belongsTo(User::class, 'edit_request_handled_by_admin_id');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isUnverified()
    {
        return $this->status === 'unverified';
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }

    public function canBeEditedByUser()
    {
        return $this->status === 'unverified';
    }
}
