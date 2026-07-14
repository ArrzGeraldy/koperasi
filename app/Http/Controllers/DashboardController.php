<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\LimitPinjaman;
use App\Models\Simpanan;
use App\Models\User;
use App\Models\PembayaranCicilan;
use App\Models\SetorSimpananHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
    /**
     * Print members summary (A4 landscape)
     */
    public function printMembers(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        try {
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        } catch (\Exception $e) {
            $endDate = now()->endOfMonth()->toDateString();
        }

        $members = User::where('role', 'member')
                       ->orderBy('name')
                       ->get();

        $data = $members->map(function($user) use ($endDate) {
            // Sum approved setoran sampai dengan endDate
            $pokok = SetorSimpananHistory::where('user_id', $user->id)
                        ->where('simpanan_type', 'pokok')
                        ->where('status', 'approved')
                        ->whereDate('created_at', '<=', $endDate)
                        ->sum('amount');

            $wajib = SetorSimpananHistory::where('user_id', $user->id)
                        ->where('simpanan_type', 'wajib')
                        ->where('status', 'approved')
                        ->whereDate('created_at', '<=', $endDate)
                        ->sum('amount');

            $sukarela = SetorSimpananHistory::where('user_id', $user->id)
                        ->where('simpanan_type', 'sukarela')
                        ->where('status', 'approved')
                        ->whereDate('created_at', '<=', $endDate)
                        ->sum('amount');

            // Fallback to current simpanan if history not present
            $simpanan = $user->simpanan;
            if (!$pokok && $simpanan) $pokok = $simpanan->simpanan_pokok ?? 0;
            if (!$wajib && $simpanan) $wajib = $simpanan->simpanan_wajib ?? 0;
            if (!$sukarela && $simpanan) $sukarela = $simpanan->simpanan_sukarela ?? 0;

            $total_simpanan = $pokok + $wajib + $sukarela;

            $pinjamanCount = Pinjaman::where('user_id', $user->id)
                            ->whereDate('applied_date', '<=', $endDate)
                            ->count();

            $totalPinjaman = Pinjaman::where('user_id', $user->id)
                            ->whereDate('applied_date', '<=', $endDate)
                            ->sum('jumlah_pinjaman');

            $outstanding = Pinjaman::where('user_id', $user->id)
                            ->whereNotNull('disbursed_date')
                            ->whereDate('disbursed_date', '<=', $endDate)
                            ->sum('remaining_amount');

            return (object) [
                'user' => $user,
                'simpanan_pokok' => $pokok,
                'simpanan_wajib' => $wajib,
                'simpanan_sukarela' => $sukarela,
                'total_simpanan' => $total_simpanan,
                'pinjaman_count' => $pinjamanCount,
                'total_pinjaman' => $totalPinjaman,
                'outstanding' => $outstanding,
            ];
        });

        return view('admin.print.members_summary', [
            'members' => $data,
            'selectedMonth' => $month,
            'selectedYear' => $year,
        ]);
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
