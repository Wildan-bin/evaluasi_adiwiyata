<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileUpload extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'original_filename',
        'system_filename',
        'file_path',
        'file_size',
        'mime_type',
        'category',
        'indikator',
        'section',
        'status',
        'reviewed_by',
        'reviewed_at',
        'mentor_comment',
        'parent_file_id',
        'revision_number',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the file upload.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mentor who reviewed this file.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the parent file if this is a revision.
     */
    public function parentFile(): BelongsTo
    {
        return $this->belongsTo(FileUpload::class, 'parent_file_id');
    }

    /**
     * Get all revisions of this file.
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(FileUpload::class, 'parent_file_id');
    }

    /**
     * Scope for pending files.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved files.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected files.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for files by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get file size in human readable format.
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is awaiting review.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if file is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if file is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected' || $this->status === 'revision_requested';
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'revision_requested' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Review',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_requested' => 'Perlu Revisi',
            default => 'Tidak Diketahui',
        };
    }
}
