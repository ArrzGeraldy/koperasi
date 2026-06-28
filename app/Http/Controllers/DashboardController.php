<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\LimitPinjaman;
use App\Models\Simpanan;
use App\Models\User;
use App\Models\PembayaranCicilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function admin()
    {
        // Total anggota aktif (exclude admin)
        $totalAnggota = User::where('role', 'member')
                           ->where('status', 'active')
                           ->count();
        
        // Total dana simpanan dari semua anggota
        $danaSimpanan = Simpanan::selectRaw('SUM(simpanan_pokok + simpanan_wajib + simpanan_sukarela) as total')->value('total') ?? 0;
        
        // Total semua pinjaman yang pernah diajukan
        $totalPinjaman = Pinjaman::sum('jumlah_pinjaman');
        
        // Total pinjaman aktif (approved, disbursed)
        $pinjamanAktif = Pinjaman::whereIn('status', ['approved', 'disbursed'])
                                ->count();
        
        // List pembayaran cicilan terbaru yang sudah diverifikasi
        $recentPayments = PembayaranCicilan::with([
            'cicilan.pinjaman.user'
        ])
        ->whereNotNull('acc_at')
        ->orderBy('acc_at', 'desc')
        ->limit(15)
        ->get();
        
        return view('admin.dashboard', compact(
            'totalAnggota',
            'danaSimpanan',
            'totalPinjaman',
            'pinjamanAktif',
            'recentPayments'
        ));
    }
    public function anggota()
    {
        $user = Auth::user();
        
        // Get limit pinjaman
        $limitPinjaman = $user->limitPinjaman;
        
        // Get pinjaman aktif (exclude completed and rejected)
        $pinjamans = Pinjaman::where("user_id", "=", $user->id)->whereNotIn('status', ['completed', 'rejected'])->get();
        // dd($pinjamans);
        return view('anggota-ui.dashboard', compact('user', 'limitPinjaman', 'pinjamans'));
    }

    public function cicilans($pinjamanId)
    {
        $user = Auth::user();
        
        // Get pinjaman with cicilans
        $pinjaman = Pinjaman::with(['cicilans' => function($query) {
            $query->orderBy('no_cicilan');
        }])->findOrFail($pinjamanId);
        
        // Check if user owns this pinjaman
        if ($pinjaman->user_id !== $user->id) {
            abort(403, 'Unauthorized access');
        }
        
        return view('anggota-ui.cicilan', compact('user', 'pinjaman'));
    }

    public function riwayat()
    {
        $user = Auth::user();
        
        // Get all pinjaman history with pagination
        $pinjamans = Pinjaman::where("user_id", "=", $user->id)
                             ->orderBy('created_at', 'desc')
                             ->paginate(10);
        
        return view('anggota-ui.riwayat', compact('user', 'pinjamans'));
    }
}
