<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cicilan;
use App\Models\PembayaranCicilan;
use App\Services\WhatsAppservices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranCicilanController extends Controller
{
    /**
     * Admin: List pending payments
     */
    public function index()
    {
        $pembayarans = PembayaranCicilan::with(['cicilan.pinjaman.user'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notifikasi.index', compact('pembayarans'));
    }

    /**
     * Show form for uploading payment proof
     */
    public function create(Request $request, $cicilanId)
    {
        $user = Auth::user();
        
        $cicilan = Cicilan::with(['pinjaman', 'pembayaran'])->findOrFail($cicilanId);
        
        // Check if user owns this cicilan through pinjaman
        if ($cicilan->pinjaman->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }
        
        // Check if cicilan already has payment
        if ($cicilan->pembayaran) {
            return redirect()->route('anggota.cicilans', $cicilan->pinjaman_id)
                           ->with('error', 'Cicilan ini sudah memiliki bukti pembayaran yang sedang diverifikasi');
        }
        
        // Check if cicilan is already paid
        if ($cicilan->status === Cicilan::STATUS_PAID) {
            return redirect()->route('anggota.cicilans', $cicilan->pinjaman_id)
                           ->with('error', 'Cicilan ini sudah lunas');
        }
        
        $isFullRequested = $request->boolean('full');
        return view('anggota-ui.bayar-cicilan', compact('user', 'cicilan', 'isFullRequested'));
    }

    /**
     * Store payment proof
     */
    public function store(Request $request, $cicilanId)
    {
        $validated = $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'tanggal_transfer' => 'required|date|before_or_equal:today',
            'is_full_payment' => 'nullable|boolean',
        ], [
            'bukti_transfer.required' => 'Bukti transfer harus diupload',
            'bukti_transfer.image' => 'File harus berupa gambar',
            'bukti_transfer.mimes' => 'Format file harus jpg, jpeg, png, atau pdf',
            'bukti_transfer.max' => 'Ukuran file maksimal 2MB',
            'tanggal_transfer.required' => 'Tanggal transfer harus diisi',
            'tanggal_transfer.before_or_equal' => 'Tanggal transfer tidak boleh melebihi hari ini',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $cicilan = Cicilan::with('pinjaman')->findOrFail($cicilanId);

            // Check ownership
            if ($cicilan->pinjaman->user_id !== $user->id) {
                abort(403, 'Unauthorized access');
            }

            // Check if already has payment
            if ($cicilan->pembayaran) {
                throw new \Exception('Cicilan ini sudah memiliki bukti pembayaran');
            }

            // Upload file
            $file = $request->file('bukti_transfer');
            $filename = 'pembayaran_' . $cicilan->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');

            // Create payment record
            PembayaranCicilan::create([
                'cicilan_id' => $cicilan->id,
                'bukti_transfer' => $path,
                'tanggal_transfer' => $validated['tanggal_transfer'],
                'is_full_payment' => $request->has('is_full_payment') ? (bool) $request->get('is_full_payment') : false,
            ]);

            // Update cicilan status to pending_verification
            $cicilan->update([
                'status' => Cicilan::STATUS_PENDING_VERIFICATION,
            ]);

            DB::commit();

            $message = "*Notifikasi Pembayaran Cicilan*\n\n" .
                      "Nama: *" . $user->name . "*\n" .
                      "No. Pinjaman: *" . $cicilan->pinjaman->no_pinjaman . "*\n" .
                      "Cicilan Ke: *" . $cicilan->no_cicilan . "*\n" .
                      "Jumlah Pembayaran: *Rp " . number_format($cicilan->amount, 0, ',', '.') . "*\n" .
                      "Tanggal Transfer: *" . date('d-m-Y', strtotime($validated['tanggal_transfer'])) . "*\n\n" .
                      "Bukti pembayaran sedang menunggu verifikasi admin.\n";

            WhatsAppservices::send($user->phone, $message);

            return redirect()->route('anggota.cicilans', $cicilan->pinjaman_id)
                           ->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                           ->withInput()
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Verify payment (approve)
     */
    public function verify($pembayaranId)
    {
        DB::beginTransaction();
        try {
            $pembayaran = PembayaranCicilan::with(['cicilan.pinjaman'])->findOrFail($pembayaranId);
            
            // Check if already verified
            if ($pembayaran->isVerified()) {
                throw new \Exception('Pembayaran ini sudah diverifikasi');
            }

            $cicilan = $pembayaran->cicilan;
            $pinjaman = $cicilan->pinjaman;
            $user = $pinjaman->user;

            // Update pembayaran
            $pembayaran->update([
                'acc_by' => Auth::id(),
                'acc_at' => now(),
            ]);

            // If this payment is marked as full payment, mark all unpaid cicilans as paid
            if ($pembayaran->is_full_payment) {
                $unpaid = $pinjaman->cicilans()->where('status', '!=', Cicilan::STATUS_PAID)->get();
                $totalPaid = 0;
                foreach ($unpaid as $uc) {
                    $sisa = max(0, $uc->amount - $uc->paid_amount);
                    $uc->update([
                        'status' => Cicilan::STATUS_PAID,
                        'paid_amount' => $uc->amount,
                        'paid_date' => now(),
                    ]);
                    $totalPaid += $sisa;
                }

                // Update pinjaman totals
                $pinjaman->update([
                    'paid_amount' => $pinjaman->paid_amount + $totalPaid,
                    'remaining_amount' => max(0, $pinjaman->remaining_amount - $totalPaid),
                ]);

                // If loan fully paid, mark completed and restore limit
                if ($pinjaman->remaining_amount <= 0) {
                    $pinjaman->update([
                        'status' => 'completed',
                        'completed_date' => now(),
                    ]);

                    if ($pinjaman->user->limitPinjaman) {
                        $limitPinjaman = $pinjaman->user->limitPinjaman;
                        $limitPinjaman->update([
                            'available_limit' => $limitPinjaman->available_limit + $pinjaman->jumlah_pinjaman,
                        ]);
                    }
                }
            } else {
                // Update single cicilan
                $cicilan->update([
                    'status' => Cicilan::STATUS_PAID,
                    'paid_amount' => $cicilan->amount,
                    'paid_date' => now(),
                ]);

                // Update pinjaman
                $pinjaman->update([
                    'paid_amount' => $pinjaman->paid_amount + $cicilan->amount,
                    'remaining_amount' => $pinjaman->remaining_amount - $cicilan->amount,
                ]);
            }

            // Check if pinjaman is completed
            if ($pinjaman->remaining_amount <= 0) {
                $pinjaman->update([
                    'status' => 'completed',
                    'completed_date' => now(),
                ]);

                // Restore limit pinjaman
                if ($pinjaman->user->limitPinjaman) {
                    $limitPinjaman = $pinjaman->user->limitPinjaman;
                    $limitPinjaman->update([
                        'available_limit' => $limitPinjaman->available_limit + $pinjaman->jumlah_pinjaman,
                    ]);
                }
            }

            DB::commit();

            $message = "*Pembayaran Cicilan Diverifikasi*\n\n" .
                "No. Pinjaman: *" . $pinjaman->no_pinjaman . "*\n" .
                "No. Cicilan: *" . $cicilan->no_cicilan . "*\n" .
                "Status: *Lunas*\n\n" .
                "Pembayaran cicilan Anda telah berhasil diverifikasi.";

            if ($user && $user->phone) {
                WhatsAppservices::send($user->phone, $message);
            }

            return redirect()->back()
                           ->with('success', 'Pembayaran cicilan berhasil diverifikasi');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', $e->getMessage());
        }
    }

    /**
     * Admin: Reject payment
     */
    public function reject(Request $request, $pembayaranId)
    {
        $validated = $request->validate([
            'rejection_note' => 'required|string|max:500',
        ], [
            'rejection_note.required' => 'Alasan penolakan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $pembayaran = PembayaranCicilan::with(['cicilan.pinjaman.user'])->findOrFail($pembayaranId);
            
            // Check if already verified
            if ($pembayaran->isVerified()) {
                throw new \Exception('Pembayaran yang sudah diverifikasi tidak dapat ditolak');
            }

            $cicilan = $pembayaran->cicilan;
            $user = $cicilan->pinjaman->user;

            // Delete bukti transfer file
            if (Storage::disk('public')->exists($pembayaran->bukti_transfer)) {
                Storage::disk('public')->delete($pembayaran->bukti_transfer);
            }

            // Update pembayaran with rejection note before delete (for logging)
            $pembayaran->update([
                'rejection_note' => $validated['rejection_note'],
            ]);

            // Update cicilan status back to rejected/unpaid
            $cicilan->update([
                'status' => Cicilan::STATUS_REJECTED,
            ]);
            

            // Delete pembayaran record
            $pembayaran->delete();

            DB::commit();

            $message = "*Notifikasi Pembayaran Cicilan Ditolak*\n\n" .
                "No. Pinjaman: *" . $cicilan->pinjaman->no_pinjaman . "*\n" .
                "No. Cicilan: *" . $cicilan->no_cicilan . "*\n" .
                "Alasan Penolakan: *" . $validated['rejection_note'] . "*\n\n" .
                "Silakan upload ulang bukti pembayaran Anda.";

            if ($user && $user->phone) {
                WhatsAppservices::send($user->phone, $message);
            }

            return redirect()->back()
                           ->with('success', 'Pembayaran ditolak. Anggota dapat mengupload ulang bukti pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', $e->getMessage());
        }
    }
}
