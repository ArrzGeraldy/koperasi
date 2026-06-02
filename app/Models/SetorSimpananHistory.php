<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetorSimpananHistory extends Model
{
    protected $table = 'setor_simpanan_histories';

    protected $fillable = [
        'user_id',
        'simpanan_type',
        'amount',
        'bukti_setor',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the setor simpanan history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
