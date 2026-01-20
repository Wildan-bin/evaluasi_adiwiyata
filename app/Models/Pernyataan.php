<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pernyataan extends Model
{
    use SoftDeletes;

    protected $table = 'pernyataan';

    protected $fillable = [
        'user_id',
        'pernyataan_data',
        'persetujuan_publikasi',
        'bukti_persetujuan',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'pernyataan_data' => 'string',
        'persetujuan_publikasi' => 'string',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(MentorComment::class, 'file_id')
            ->where('file_type', 'a8')
            ->with('mentor:id,name');
    }
}