<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rencana extends Model
{
    use SoftDeletes;

    protected $table = 'rencana';

    protected $fillable = [
        'user_id',
        'indikator',
        'path_file',
        'file_upload_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the user that owns the rencana.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the file upload record.
     */
    public function fileUpload(): BelongsTo
    {
        return $this->belongsTo(FileUpload::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MentorComment::class, 'file_id')
            ->where('file_type', 'a5')
            ->with('mentor:id,name');
    }
}
