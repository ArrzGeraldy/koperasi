<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\Cicilan;
use App\Models\LimitPinjaman;
use App\Models\Rekening;
use App\Models\PencairanDana;
use App\Services\WhatsAppservices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PinjamanController extends Controller
{
    /**
     * Display pending pinjaman (Admin)
     */
    public function pending(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        $query = Pinjaman::with(['user'])
            ->where('status', 'pending');

        // Search by nomor pinjaman or user name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pinjaman', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $pinjamans = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.pinjaman.pending', compact('pinjamans'));
    }

    /**
     * Display a listing of pinjaman (exclude pending - Admin)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Pinjaman::with(['user', 'approver'])
            ->where('status', '!=', 'pending'); // Exclude pending

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Search by nomor pinjaman or user name
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pinjaman', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $pinjamans = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.pinjaman.index', compact('pinjamans'));
    }

    /**
     * Show the form for creating a new pinjaman.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get user's limit pinjaman
        $limitPinjaman = $user->limitPinjaman;
        
        if (!$limitPinjaman || $limitPinjaman->available_limit <= 0) {
           dd("Limit pinjaman Anda tidak mencukupi atau belum tersedia");
        }

        return view('anggota-ui.pengajuan.create', compact('limitPinjaman'));
    }

    /**
     * Store a newly created pinjaman.
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'jumlah_pinjaman' => 'required|numeric|min:100000',
            'keperluan' => 'nullable|string|max:500',
        ], [
            'jumlah_pinjaman.required' => 'Jumlah pinjaman harus diisi',
            'jumlah_pinjaman.min' => 'Jumlah pinjaman minimal Rp 100.000',
        ]);

        DB::beginTransaction();
        try {
            // Check limit pinjaman
            $limitPinjaman = LimitPinjaman::where('user_id', $userId)->first();
            
            if (!$limitPinjaman) {
                throw new \Exception('Limit pinjaman tidak ditemukan');
            }

            if ($validated['jumlah_pinjaman'] > $limitPinjaman->available_limit) {
                throw new \Exception('Jumlah pinjaman melebihi limit tersedia: Rp ' . number_format($limitPinjaman->available_limit, 0, ',', '.'));
            }

            // Default settings
            $tenor = 10; // bulan
            $bungaTotal = 20; // persen (20% dari pokok)
            
            // Calculate
            $totalBunga = ($validated['jumlah_pinjaman'] * $bungaTotal / 100);
            $totalPayment = $validated['jumlah_pinjaman'] + $totalBunga;
            $monthlyPayment = $totalPayment / $tenor;

            // Generate nomor pinjaman
            $noPinjaman = Pinjaman::generateNoPinjaman();

            // Create pinjaman
            $pinjaman = Pinjaman::create([
                'user_id' => $userId,
                'no_pinjaman' => $noPinjaman,
                'jumlah_pinjaman' => $validated['jumlah_pinjaman'],
                'bunga' => $bungaTotal,
                'tenor' => $tenor,
                'monthly_payment' => $monthlyPayment,
                'total_payment' => $totalPayment,
                'paid_amount' => 0,
                'remaining_amount' => $totalPayment,
                'status' => 'pending',
                'applied_date' => now(),
            ]);

            DB::commit();

            // Send WhatsApp notification
            $message = "User " . Auth::user()->name . " mengajukan pinjaman sebesar Rp " . number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') . " mohon untuk di prosses";
            WhatsAppservices::send("08888406599", $message);

            return redirect()->route('anggota.dashboard')
                           ->with('success', 'Pengajuan pinjaman berhasil dibuat dengan nomor: ' . $noPinjaman);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pinjaman: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified pinjaman.
     */
    public function show($id)
    {
        $pinjaman = Pinjaman::with(['user', 'approver', 'pencairanDana', 'cicilans' => function($query) {
            $query->orderBy('no_cicilan');
        }])->findOrFail($id);

        return view('admin.pinjaman.show', compact('pinjaman'));
    }

    /**
     * Show form to approve pinjaman (Admin only)
     */
    public function approveForm($id)
    {
        $pinjaman = Pinjaman::with(['user.rekenings', 'user.limitPinjaman'])->findOrFail($id);

        if ($pinjaman->status !== 'pending') {
            return redirect()->route('admin.pinjaman.show', $id)
                           ->with('error', 'Pinjaman ini sudah diproses sebelumnya');
        }

        // Get user's rekening for disbursement
        $rekenings = $pinjaman->user->rekenings;

        if ($rekenings->isEmpty()) {
            return redirect()->route('admin.pinjaman.show', $id)
                           ->with('error', 'Member belum memiliki rekening untuk pencairan dana');
        }

        return view('admin.pinjaman.approve', compact('pinjaman', 'rekenings'));
    }

    /**
     * Approve pinjaman with disbursement (Admin only)
     */
    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'rekening_id' => 'required|exists:rekenings,id',
            'bukti_transfer' => 'required|image|mimes:jpeg,jpg,png,pdf|max:2048',
            'tanggal_transfer' => 'required|date',
            'catatan' => 'nullable|string|max:500',
        ], [
            'rekening_id.required' => 'Rekening pencairan harus dipilih',
            'bukti_transfer.required' => 'Bukti transfer harus diupload',
            'bukti_transfer.image' => 'Bukti transfer harus berupa gambar',
            'bukti_transfer.max' => 'Ukuran file maksimal 2MB',
            'tanggal_transfer.required' => 'Tanggal transfer harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::with('user')->findOrFail($id);


            if ($pinjaman->status !== 'pending') {
                throw new \Exception('Pinjaman ini sudah diproses sebelumnya');
            }

            // Validate rekening belongs to user
            $rekening = Rekening::where('id', $validated['rekening_id'])
                               ->where('user_id', $pinjaman->user_id)
                               ->first();
            
            if (!$rekening) {
                throw new \Exception('Rekening tidak valid atau bukan milik member ini');
            }

            // Upload bukti transfer
            $buktiPath = $request->file('bukti_transfer')->store('bukti-pencairan', 'public');

            // Update pinjaman status
            $pinjaman->update([
                'status' => 'disbursed',
                'approved_date' => now(),
                'approved_by' => Auth::id(),
                'disbursed_date' => $validated['tanggal_transfer'],
            ]);

            // Kurangi limit pinjaman
            $limitPinjaman = LimitPinjaman::where('user_id', $pinjaman->user_id)->first();
            if ($limitPinjaman) {
                $limitPinjaman->kurangiLimit($pinjaman->jumlah_pinjaman);
            }

            // Create pencairan dana record
            PencairanDana::create([
                'pinjaman_id' => $pinjaman->id,
                'metode_transfer' => $rekening->type_label, // Bank or E-Wallet
                'nama_pemilik' => $rekening->name,
                'nomor_rekening' => $rekening->number_rek,
                'jumlah_transfer' => $pinjaman->jumlah_pinjaman,
                'bukti_transfer' => $buktiPath,
                'tanggal_transfer' => $validated['tanggal_transfer'],
                'user_id' => Auth::id(), // Admin yang melakukan pencairan
            ]);

            // Generate cicilan schedule
            $this->generateCicilan($pinjaman);

            DB::commit();

            $message = "Pinjaman anda telah di transfer ke " . $rekening->number_rek . "(" . $rekening->name . ")  \nNominal: Rp. " . number_format($pinjaman->jumlah_pinjaman, 0, ',', '.');

            WhatsAppservices::send($pinjaman->user->phone, $message);

            return redirect()->route('admin.pinjaman.show', $id)
                           ->with('success', 'Pinjaman berhasil disetujui, dana telah dicairkan, dan jadwal cicilan telah dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving pinjaman: ' . $e->getMessage());
            
            // Delete uploaded file if exists
            if (isset($buktiPath) && Storage::disk('public')->exists($buktiPath)) {
                Storage::disk('public')->delete($buktiPath);
            }
            
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject pinjaman (Admin only)
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan' => 'required|string|max:500',
        ], [
            'alasan.required' => 'Alasan penolakan harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::with('user')->findOrFail($id);

            if ($pinjaman->status !== 'pending') {
                throw new \Exception('Pinjaman ini sudah diproses sebelumnya');
            }

            $pinjaman->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_date' => now(),
            ]);

            DB::commit();

            $message = "*Notifikasi Pinjaman Ditolak*\n\n" .
                "No. Pinjaman: *" . $pinjaman->no_pinjaman . "*\n" .
                "Alasan Penolakan: *" . $validated['alasan'] . "*\n\n" .
                "Pengajuan pinjaman Anda ditolak.";

            if ($pinjaman->user && $pinjaman->user->phone) {
                WhatsAppservices::send($pinjaman->user->phone, $message);
            }

            return redirect()->route('admin.pinjaman.show', $id)
                           ->with('success', 'Pinjaman berhasil ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting pinjaman: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate jadwal cicilan
     */
    private function generateCicilan(Pinjaman $pinjaman)
    {
        $pokokPerBulan = $pinjaman->jumlah_pinjaman / $pinjaman->tenor;
        $totalBunga = ($pinjaman->jumlah_pinjaman * $pinjaman->bunga / 100);
        $bungaPerBulan = $totalBunga / $pinjaman->tenor;

        for ($i = 1; $i <= $pinjaman->tenor; $i++) {
            $dueDate = Carbon::parse($pinjaman->approved_date)->addMonths($i);

            Cicilan::create([
                'pinjaman_id' => $pinjaman->id,
                'no_cicilan' => $i,
                'due_date' => $dueDate,
                'pokok' => $pokokPerBulan,
                'bunga' => $bungaPerBulan,
                'amount' => $pinjaman->monthly_payment,
                'paid_amount' => 0,
            ]);
        }
    }

    /**
     * Cancel pinjaman (by user, only if pending)
     */
    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::findOrFail($id);

            if ($pinjaman->user_id !== Auth::id()) {
                throw new \Exception('Anda tidak memiliki akses untuk membatalkan pinjaman ini');
            }

            if ($pinjaman->status !== 'pending') {
                throw new \Exception('Hanya pinjaman dengan status pending yang bisa dibatalkan');
            }

            $pinjaman->delete();

            DB::commit();

            return redirect()->route('pinjaman.index')
                           ->with('success', 'Pengajuan pinjaman berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

