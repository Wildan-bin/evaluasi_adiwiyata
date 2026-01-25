<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdministrasiDasar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'administrasi_dasar';

    protected $fillable = [
        'user_id',
        'indikator',
        'path_file',
        'file_upload_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relation to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation to FileUpload
     */
    public function fileUpload()
    {
        return $this->belongsTo(FileUpload::class);
    }
}
