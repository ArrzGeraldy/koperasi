<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Cicilan extends Model
{
    use HasFactory;

    protected $table = 'cicilans';

    // Status constants
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PENDING_VERIFICATION = 'pending_verification';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';

    protected $fillable = [
        'pinjaman_id',
        'no_cicilan',
        'due_date',
        'pokok',
        'bunga',
        'amount',
        'status',
        'paid_amount',
        'paid_date',
        'late_fee',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'pokok' => 'decimal:2',
        'bunga' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
    ];

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }

    /**
     * Relasi ke Pembayaran Cicilan
     */
    public function pembayaran()
    {
        return $this->hasOne(PembayaranCicilan::class);
    }

    /**
     * Scope: Belum lunas
     */
    public function scopeUnpaid($query)
    {
        return $query->where('paid_amount', '<', DB::raw('amount'));
    }

    /**
     * Scope: Sudah lunas
     */
    public function scopePaid($query)
    {
        return $query->where('paid_amount', '>=', DB::raw('amount'));
    }

    /**
     * Scope: Jatuh tempo
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('paid_amount', '<', DB::raw('amount'));
    }

    /**
     * Check apakah sudah lunas
     */
    public function isPaid()
    {
        return $this->paid_amount >= $this->amount;
    }

    /**
     * Check apakah jatuh tempo
     */
    public function isOverdue()
    {
        return !$this->isPaid() && $this->due_date < now();
    }

    /**
     * Get sisa pembayaran
     */
    public function getSisaBayarAttribute()
    {
        return max(0, $this->amount - $this->paid_amount);
    }

    /**
     * Get hari keterlambatan
     */
    public function getHariTerlambatAttribute()
    {
        if (!$this->isOverdue()) return 0;
        return Carbon::parse($this->due_date)->diffInDays(now());
    }

    /**
     * Get status cicilan untuk tampilan
     */
    public function getStatusLabelAttribute()
    {
        // Jika sudah ada status dalam database, gunakan mapping
        $dbStatus = $this->attributes['status'] ?? self::STATUS_UNPAID;
        
        return match($dbStatus) {
            self::STATUS_PAID => 'Lunas',
            self::STATUS_PENDING_VERIFICATION => 'Verifikasi',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_UNPAID => $this->isOverdue() ? 'Terlambat' : 'Belum Bayar',
            default => 'Belum Bayar',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $dbStatus = $this->attributes['status'] ?? self::STATUS_UNPAID;
        
        return match($dbStatus) {
            self::STATUS_PAID => 'green',
            self::STATUS_PENDING_VERIFICATION => 'blue',
            self::STATUS_REJECTED => 'red',
            self::STATUS_UNPAID => $this->isOverdue() ? 'red' : 'yellow',
            default => 'yellow',
        };
    }
}
