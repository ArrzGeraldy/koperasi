<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjaman';

    protected $fillable = [
        'user_id',
        'no_pinjaman',
        'jumlah_pinjaman',
        'bunga',
        'tenor',
        'monthly_payment',
        'total_payment',
        'paid_amount',
        'remaining_amount',
        'status',
        'applied_date',
        'approved_date',
        'approved_by',
        'disbursed_date',
        'completed_date',
    ];

    protected $casts = [
        'jumlah_pinjaman' => 'decimal:2',
        'bunga' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'total_payment' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'applied_date' => 'date',
        'approved_date' => 'date',
        'disbursed_date' => 'date',
        'completed_date' => 'date',
    ];

    /**
     * Relasi ke User (Peminjam)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke User (Approver)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi ke Cicilan
     */
    public function cicilans()
    {
        return $this->hasMany(Cicilan::class);
    }

    /**
     * Relasi ke Pencairan Dana
     */
    public function pencairanDana()
    {
        return $this->hasOne(PencairanDana::class);
    }

    /**
     * Relasi ke Rekening
     */
    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pinjaman pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Pinjaman approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Pinjaman aktif (sudah dicairkan)
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'disbursed');
    }

    /**
     * Get status label dengan warna
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'disbursed' => 'Aktif',
            'completed' => 'Lunas',
            default => 'Unknown',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'rejected' => 'red',
            'disbursed' => 'green',
            'completed' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get persentase pembayaran
     */
    public function getPersentaseLunasAttribute()
    {
        if ($this->total_payment == 0) return 0;
        return ($this->paid_amount / $this->total_payment) * 100;
    }

    /**
     * Generate nomor pinjaman otomatis
     */
    public static function generateNoPinjaman()
    {
        $prefix = 'PJM';
        $year = date('Y');
        $month = date('m');
        
        $lastPinjaman = self::whereYear('created_at', $year)
                           ->whereMonth('created_at', $month)
                           ->orderBy('id', 'desc')
                           ->first();
        
        $sequence = $lastPinjaman ? (int) substr($lastPinjaman->no_pinjaman, -4) + 1 : 1;
        
        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }
}
