<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembayaranCicilan extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_cicilans';

    protected $fillable = [
        'cicilan_id',
        'bukti_transfer',
        'tanggal_transfer',
        'acc_by',
        'acc_at',
        'rejection_note',
        'is_full_payment',
    ];

    protected $casts = [
        'tanggal_transfer' => 'date',
        'acc_at' => 'datetime',
        'is_full_payment' => 'boolean',
    ];

    /**
     * Relasi ke Cicilan
     */
    public function cicilan()
    {
        return $this->belongsTo(Cicilan::class);
    }

    /**
     * Relasi ke User (verifier)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'acc_by');
    }

    /**
     * Check apakah sudah diverifikasi
     */
    public function isVerified()
    {
        return !is_null($this->acc_by) && !is_null($this->acc_at);
    }

    /**
     * Check apakah pending verifikasi
     */
    public function isPending()
    {
        return is_null($this->acc_by);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        if ($this->isVerified()) return 'Terverifikasi';
        return 'Menunggu Verifikasi';
    }

    /**
     * Scope: Pending verification
     */
    public function scopePending($query)
    {
        return $query->whereNull('acc_by');
    }

    /**
     * Scope: Verified
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('acc_by');
    }
}
