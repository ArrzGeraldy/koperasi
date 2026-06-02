<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PencairanDana extends Model
{
    use HasFactory;

    protected $table = "pencairan_dana";

    protected $fillable = [
        'pinjaman_id',
        'metode_transfer',
        'nama_pemilik',
        'nomor_rekening',
        'jumlah_transfer',
        'bukti_transfer',
        'tanggal_transfer',
        'user_id',
    ];

    protected $casts = [
        'jumlah_transfer' => 'decimal:2',
        'tanggal_transfer' => 'date',
    ];

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }

    /**
     * Relasi ke User (Admin yang melakukan pencairan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get bukti transfer URL
     */
    public function getBuktiTransferUrlAttribute()
    {
        return $this->bukti_transfer ? asset('storage/' . $this->bukti_transfer) : null;
    }
}
