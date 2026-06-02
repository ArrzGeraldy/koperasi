<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'phone',
        'address',
        'role',
        'status',
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

    /**
     * Relasi ke Rekening (One to Many)
     */
    public function rekenings()
    {
        return $this->hasMany(Rekening::class);
    }

    /**
     * Relasi ke Limit Pinjaman (One to One)
     */
    public function limitPinjaman()
    {
        return $this->hasOne(LimitPinjaman::class);
    }

    /**
     * Relasi ke Simpanan (One to One)
     */
    public function simpanan()
    {
        return $this->hasOne(Simpanan::class);
    }

    /**
     * Relasi ke Setor Simpanan History (One to Many)
     */
    public function setorSimpananHistories()
    {
        return $this->hasMany(SetorSimpananHistory::class);
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah member
     */
    public function isMember()
    {
        return $this->role === 'member';
    }

    /**
     * Cek apakah user sudah active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
