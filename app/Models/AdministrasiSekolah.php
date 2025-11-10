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
        'tim_adiwiyata',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'tim_adiwiyata' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(AdministrasiLog::class);
    }
}
