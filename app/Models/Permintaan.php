<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permintaan extends Model
{
    use SoftDeletes;

    protected $table = 'permintaan';

    protected $fillable = [
        'user_id',
        'permintaan_tim',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}