<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cicilan;
use App\Models\Pinjaman;
use App\Models\LimitPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CicilanController extends Controller
{
    /**
     * Display a listing of cicilan for a pinjaman.
     */
    public function index($pinjamanId)
    {
        $pinjaman = Pinjaman::with(['user', 'cicilans' => function($query) {
            $query->orderBy('no_cicilan');
        }])->findOrFail($pinjamanId);

        return view('cicilan.index', compact('pinjaman'));
    }

    /**
     * Show the form for paying a cicilan.
     */
    public function create($cicilanId)
    {
        $cicilan = Cicilan::with(['pinjaman.user'])->findOrFail($cicilanId);

        if ($cicilan->isPaid()) {
            return redirect()->route('pinjaman.show', $cicilan->pinjaman_id)
                           ->with('error', 'Cicilan ini sudah lunas');
        }

        return view('cicilan.create', compact('cicilan'));
    }

    /**
     * Process cicilan payment.
     */
    public function store(Request $request, $cicilanId)
    {
        $validated = $request->validate([
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string|in:transfer,tunai,ewallet',
            'bukti_pembayaran' => 'nullable|image|max:2048',
        ], [
            'jumlah_bayar.required' => 'Jumlah pembayaran harus diisi',
            'jumlah_bayar.min' => 'Jumlah pembayaran minimal Rp 1',
            'metode_pembayaran.required' => 'Metode pembayaran harus dipilih',
        ]);

        DB::beginTransaction();
        try {
            $cicilan = Cicilan::with('pinjaman')->findOrFail($cicilanId);

            // Calculate sisa pembayaran
            $sisaBayar = $cicilan->amount - $cicilan->paid_amount;

            // Validate jumlah bayar
            if ($validated['jumlah_bayar'] > $sisaBayar + $cicilan->late_fee) {
                throw new \Exception('Jumlah pembayaran melebihi sisa tagihan');
            }

            // Calculate late fee if overdue
            $lateFee = 0;
            if ($cicilan->isOverdue()) {
                $hariTerlambat = $cicilan->hari_terlambat;
                $dendaPerHari = 5000; // Rp 5.000 per hari
                $lateFee = $hariTerlambat * $dendaPerHari;
                
                $cicilan->update(['late_fee' => $lateFee]);
            }

            // Update paid amount
            $newPaidAmount = $cicilan->paid_amount + $validated['jumlah_bayar'];
            $isFullyPaid = $newPaidAmount >= ($cicilan->amount + $lateFee);
            
            $cicilan->update([
                'paid_amount' => $newPaidAmount,
                'paid_date' => $isFullyPaid ? now() : $cicilan->paid_date,
                'status' => $isFullyPaid ? Cicilan::STATUS_PAID : Cicilan::STATUS_UNPAID,
            ]);

            // Update pinjaman paid_amount and remaining_amount
            $pinjaman = $cicilan->pinjaman;
            $pinjaman->update([
                'paid_amount' => $pinjaman->paid_amount + $validated['jumlah_bayar'],
                'remaining_amount' => $pinjaman->remaining_amount - $validated['jumlah_bayar'],
            ]);

            // Check if pinjaman is completed
            if ($pinjaman->remaining_amount <= 0) {
                $pinjaman->update([
                    'status' => 'completed',
                    'completed_date' => now(),
                ]);

                // Kembalikan limit pinjaman
                $limitPinjaman = LimitPinjaman::where('user_id', $pinjaman->user_id)->first();
                if ($limitPinjaman) {
                    $limitPinjaman->kembalikanLimit($pinjaman->jumlah_pinjaman);
                }
            }

            DB::commit();

            $message = 'Pembayaran cicilan berhasil';
            if ($cicilan->isPaid()) {
                $message .= '. Cicilan ke-' . $cicilan->no_cicilan . ' sudah lunas';
            }
            if ($pinjaman->status === 'completed') {
                $message .= '. Selamat! Pinjaman Anda sudah lunas';
            }

            return redirect()->route('pinjaman.show', $pinjaman->id)
                           ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing cicilan payment: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show payment history for a cicilan.
     */
    public function show($cicilanId)
    {
        $cicilan = Cicilan::with(['pinjaman.user'])->findOrFail($cicilanId);
        
        return view('cicilan.show', compact('cicilan'));
    }

    /**
     * Get overdue cicilan list.
     */
    public function overdue()
    {
        $cicilans = Cicilan::with(['pinjaman.user'])
                          ->overdue()
                          ->orderBy('due_date')
                          ->paginate(15);

        return view('cicilan.overdue', compact('cicilans'));
    }

    /**
     * Get upcoming cicilan (jatuh tempo dalam 7 hari).
     */
    public function upcoming()
    {
        $cicilans = Cicilan::with(['pinjaman.user'])
                          ->whereBetween('due_date', [now(), now()->addDays(7)])
                          ->unpaid()
                          ->orderBy('due_date')
                          ->paginate(15);

        return view('cicilan.upcoming', compact('cicilans'));
    }
}

