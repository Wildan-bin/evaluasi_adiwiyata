<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $role
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',  // ✅ HARUS ADA
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rencana()
    {
        return $this->hasMany(Rencana::class);
    }

    public function buktiSelfAssessment()
    {
        return $this->hasMany(BuktiSelfAssessment::class);
    }

    public function kemajuan()
    {
        return $this->hasMany(Kemajuan::class);
    }

    public function pendampingan()
    {
        return $this->hasMany(Pendampingan::class);
    }

    public function permintaan()
    {
        return $this->hasMany(Permintaan::class);
    }

    public function pernyataan()
    {
        return $this->hasMany(Pernyataan::class);
    }

    public function fileUploads()
    {
        return $this->hasMany(FileUpload::class);
    }

    public function administrasiSekolah()
    {
        return $this->hasOne(AdministrasiSekolah::class);
    }
}
