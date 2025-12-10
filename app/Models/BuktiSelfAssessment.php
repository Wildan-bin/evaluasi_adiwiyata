<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiSelfAssessment extends Model
{
    use SoftDeletes;

    protected $table = 'bukti_self_assessment';

    protected $fillable = [
        'user_id',
        'indikator',
        'path_file',
        'penjelasan',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the user that owns the bukti self assessment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}