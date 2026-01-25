<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkTim extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sk_tim';

    protected $fillable = [
        'user_id',
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
