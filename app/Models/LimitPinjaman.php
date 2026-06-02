<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LimitPinjaman extends Model
{
    use HasFactory;

    protected $table = 'limit_pinjaman';

    protected $fillable = [
        'user_id',
        'max_limit',
        'available_limit',
    ];

    protected $casts = [
        'max_limit' => 'decimal:2',
        'available_limit' => 'decimal:2',
    ];

    /**
     * Relasi ke User (One to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung limit yang sudah terpakai
     */
    public function getLimitTerpakaiAttribute()
    {
        return $this->max_limit - $this->available_limit;
    }

    /**
     * Hitung persentase limit terpakai
     */
    public function getPersentaseTerpakaiAttribute()
    {
        if ($this->max_limit == 0) {
            return 0;
        }
        return ($this->limit_terpakai / $this->max_limit) * 100;
    }

    /**
     * Cek apakah limit cukup untuk pinjaman
     */
    public function cekLimitCukup($jumlahPinjaman)
    {
        return $this->available_limit >= $jumlahPinjaman;
    }

    /**
     * Kurangi limit tersedia (saat pinjaman disetujui)
     */
    public function kurangiLimit($jumlah)
    {
        $this->available_limit -= $jumlah;
        $this->save();
    }

    /**
     * Kembalikan limit tersedia (saat pinjaman lunas)
     */
    public function kembalikanLimit($jumlah)
    {
        $this->available_limit += $jumlah;
        // Pastikan tidak melebihi max_limit
        if ($this->available_limit > $this->max_limit) {
            $this->available_limit = $this->max_limit;
        }
        $this->save();
    }
}
