<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'number_rek',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get label type rekening dalam bahasa Indonesia
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'bank' => 'Bank',
            'ewallet' => 'E-Wallet',
            default => $this->type,
        };
    }

    /**
     * Scope untuk filter rekening bank
     */
    public function scopeBank($query)
    {
        return $query->where('type', 'bank');
    }

    /**
     * Scope untuk filter e-wallet
     */
    public function scopeEwallet($query)
    {
        return $query->where('type', 'ewallet');
    }
}
