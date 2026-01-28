<?php

// filepath: /home/wildanrobin/Projects/evaluasi_adiwiyata/app/Models/AssignMentor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignMentor extends Model
{
    protected $table = 'assign_mentor';

    protected $fillable = [
        'user_id_sekolah',
        'user_id_mentor',
        'assign_time_begin',
        'assign_time_finished',
    ];

    protected $casts = [
        'assign_time_begin' => 'datetime',
        'assign_time_finished' => 'datetime',
    ];

    /**
     * Relationship dengan User (Sekolah)
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_sekolah');
    }

    /**
     * Relationship dengan User (Mentor)
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_mentor');
    }

    /**
     * Relationship dengan AdministrasiSekolah
     */
    public function administrasiSekolah()
    {
        return $this->hasOneThrough(
            AdministrasiSekolah::class,
            User::class,
            'id', // Foreign key on users table
            'user_id', // Foreign key on administrasi_sekolah table
            'user_id_sekolah', // Local key on assign_mentor table
            'id' // Local key on users table
        );
    }

    /**
     * Scope untuk assignment yang masih aktif
     */
    public function scopeActive($query)
    {
        return $query->whereNull('assign_time_finished');
    }

    /**
     * Scope untuk assignment yang sudah selesai
     */
    public function scopeFinished($query)
    {
        return $query->whereNotNull('assign_time_finished');
    }

    /**
     * Check apakah assignment masih aktif
     */
    public function isActive(): bool
    {
        return is_null($this->assign_time_finished);
    }

    /**
     * Finish assignment
     */
    public function finish(): bool
    {
        $this->assign_time_finished = now();
        return $this->save();
    }
}
