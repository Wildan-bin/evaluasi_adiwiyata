<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rencana extends Model
{
    use SoftDeletes;

    protected $table = 'rencana';

    protected $fillable = [
        'user_id',
        'indikator',
        'path_file',
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
}