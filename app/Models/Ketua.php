<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ketua extends Model
{
    use HasFactory;

    protected $table = 'ketua';

    protected $fillable = [
        'sekolah_id',
        'nama',
        'nip',
        'email',
        'nomor_telepon',
    ];

    public function sekolah()
    {
        return $this->belongsTo(AdministrasiSekolah::class, 'sekolah_id');
    }

    public function anggota()
    {
        return $this->hasMany(Anggota::class, 'ketua_id');
    }
}
