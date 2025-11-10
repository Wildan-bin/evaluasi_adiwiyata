<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrasiLog extends Model
{
    use HasFactory;

    protected $table = 'administrasi_logs';

    public $timestamps = false;

    protected $fillable = [
        'administrasi_sekolah_id',
        'user_id',
        'action',
        'description',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    public function administrasiSekolah()
    {
        return $this->belongsTo(AdministrasiSekolah::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
