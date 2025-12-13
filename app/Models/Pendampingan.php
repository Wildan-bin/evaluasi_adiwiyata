<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendampingan extends Model
{
    use SoftDeletes;

    protected $table = 'pendampingan';

    protected $fillable = [
        'user_id',
        'id_permintaan',
        'kebutuhan',
        'waktu_pendampingan',
    ];

    protected $dates = [
        'waktu_pendampingan',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'waktu_pendampingan' => 'date',
    ];

    /**
     * Get the user that owns the pendampingan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the permintaan that this pendampingan belongs to.
     */
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan');
    }
}