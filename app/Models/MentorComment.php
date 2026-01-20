<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorComment extends Model
{
    protected $fillable = [
        'mentor_id',
        'file_id',
        'file_type',
        'comment',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function rencana(): BelongsTo
    {
        return $this->belongsTo(Rencana::class, 'file_id')
            ->where('file_type', 'a5');
    }

    public function buktiSelfAssessment(): BelongsTo
    {
        return $this->belongsTo(BuktiSelfAssessment::class, 'file_id')
            ->where('file_type', 'a6');
    }

    public function pernyataan(): BelongsTo
    {
        return $this->belongsTo(Pernyataan::class, 'file_id')
            ->where('file_type', 'a8');
    }
}