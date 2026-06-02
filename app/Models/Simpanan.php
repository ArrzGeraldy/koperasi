<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $fillable = [
        'user_id',
        'simpanan_pokok',
        'simpanan_wajib',
        'simpanan_sukarela',
    ];

    protected $casts = [
        'simpanan_pokok' => 'decimal:2',
        'simpanan_wajib' => 'decimal:2',
        'simpanan_sukarela' => 'decimal:2',
    ];

    /**
     * Relasi ke User (One to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
