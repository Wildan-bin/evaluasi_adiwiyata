<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kemajuan extends Model
{
    use SoftDeletes;

    protected $table = 'kemajuan';

    protected $fillable = [
        'user_id',
        'progress',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'progress' => 'integer',
    ];

    /**
     * Get the user that owns the kemajuan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}