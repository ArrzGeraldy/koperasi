<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LimitPinjaman;
use App\Models\SetorSimpananHistory;
use App\Models\Simpanan;
use App\Services\WhatsAppservices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SetorSimpananController extends Controller
{

    public function indexAdmin()
    {
        // Ambil setor simpanan yang pending, ordered by newest first
        $setorSimpanans = SetorSimpananHistory::with(['user'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();
        
        return view('admin.setor.index', compact('setorSimpanans'));
    }


    public function create()
    {
        return view('anggota-ui.simpanan.setor');
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'simpanan_type' => 'required|in:pokok,wajib,sukarela',
            'amount' => 'required|numeric|min:1000',
            'bukti_setor' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ], [
            'simpanan_type.required' => 'Jenis simpanan harus dipilih',
            'simpanan_type.in' => 'Jenis simpanan tidak valid',
            'amount.required' => 'Jumlah setor harus diisi',
            'amount.numeric' => 'Jumlah setor harus berupa angka',
            'amount.min' => 'Jumlah setor minimal Rp 1.000',
            'bukti_setor.required' => 'Bukti setor harus diunggah',
            'bukti_setor.mimes' => 'File harus berformat JPEG, PNG, JPG, atau PDF',
            'bukti_setor.max' => 'Ukuran file maksimal 5MB',
        ]);

        $user = Auth::user();

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('bukti_setor')) {
            $file = $request->file('bukti_setor');
            $filePath = $file->store('setor-simpanan/' . $user->id, 'public');
        }

        // Create setor simpanan history record
        $setorHistory = SetorSimpananHistory::create([
            'user_id' => $user->id,
            'simpanan_type' => $validated['simpanan_type'],
            'amount' => $validated['amount'],
            'bukti_setor' => $filePath,
        ]);

        // Format jenis simpanan
        $jenisSimpanan = match($validated['simpanan_type']) {
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
        };

        // Send WhatsApp notification to admin
        $message = "NOTIFIKASI SETOR SIMPANAN BARU\n\n"
            . "Nama Member: {$user->name}\n"
            . "No. HP: {$user->phone}\n"
            . "Jenis Simpanan: {$jenisSimpanan}\n"
            . "Jumlah: Rp " . number_format($validated['amount'], 0, ',', '.') . "\n"
            . "Status: Menunggu Persetujuan\n"
            . "Silakan cek dan proses segera!";

        WhatsAppservices::sendToAdmin($message);

        return redirect()->route('anggota.simpanan.index')
            ->with('success', 'Setor simpanan berhasil dikirim! Admin akan memproses dalam waktu singkat.');
    }

    public function reject(Request $req, $id)
    {
        // 1. Validasi input alasan dari body request
        $req->validate([
            'rejection_note' => 'required|string|max:255'
        ]);

        // 2. Cari data setoran berdasarkan ID
        $setorSimpanan = SetorSimpananHistory::with('user')->findOrFail($id);

        // 3. Update status menjadi 'reject' dan simpan alasan penolakan
        $setorSimpanan->update([
            'status' => 'rejected',
            'rejection_note' => $req->rejection_note
        ]);


        // 5. Buatkan format pesan WhatsApp dinamis
        $namaUser = $setorSimpanan->user->name ?? 'Anggota';
        $message = "Halo *{$namaUser}*,\n\n";
        $message .= "Setoran simpanan Anda telah *DITOLAK*.\n";
        $message .= "Alasan penolakan: *{$req->rejection_note}*\n\n";
        $message .= "Silakan periksa kembali nominal atau bukti transfer Anda. Terima kasih.";

        // 6. Kirim WhatsApp menggunakan nomor HP dari Auth::user()
        WhatsAppservices::send($setorSimpanan->user->phone, $message);

        // 7. Kembalikan respons kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Setoran berhasil ditolak dan notifikasi WA telah dikirim.');
    }
    


    public function verify($id)
    {
        // Menggunakan DB Transaction agar eksekusi data aman dan konsisten
        DB::beginTransaction();

        try {
            // 1. Ambil data setoran history beserta data usernya
            $setorSimpanan = SetorSimpananHistory::with('user')->findOrFail($id);

            // Jika status sudah tidak pending (misal double click), hentikan proses
            if ($setorSimpanan->status !== 'pending') {
                return redirect()->back()->with('error', 'Setoran ini sudah diproses sebelumnya.');
            }

            // 2. Update status setoran menjadi 'approved'
            $setorSimpanan->update([
                'status' => 'approved',
            ]);

            // 3. Update nominal di tabel 'simpanans' berdasarkan tipenya
            // Ambil nama kolom database sesungguhnya berdasarkan 'simpanan_type'
            $kolomSimpanan = match($setorSimpanan->simpanan_type) {
                'pokok'    => 'simpanan_pokok',
                'wajib'    => 'simpanan_wajib',
                'sukarela' => 'simpanan_sukarela',
            };

            // Cari atau buat data simpanan baru jika user tersebut belum punya baris di tabel simpanan
            $simpanan = Simpanan::firstOrCreate(
                ['user_id' => $setorSimpanan->user_id],
                ['simpanan_pokok' => 0, 'simpanan_wajib' => 0, 'simpanan_sukarela' => 0]
            );

            // Tambahkan nominal setoran ke simpanan saat ini
            $simpanan->increment($kolomSimpanan, $setorSimpanan->amount);


            // 4. Update limit_pinjaman (max_limit & available_limit)
            // Hitung limit baru: Simpanan Wajib + Simpanan Sukarela terbaru
            $limitBaru = $simpanan->simpanan_wajib + $simpanan->simpanan_sukarela;

            // Cari atau buat data limit pinjaman user
            $limitPinjaman = LimitPinjaman::firstOrCreate(
                ['user_id' => $setorSimpanan->user_id],
                ['max_limit' => 0, 'available_limit' => 0]
            );

            // Hitung selisih kenaikan limit untuk ditambahkan ke available_limit saat ini
            $selisihKenaikan = $limitBaru - $limitPinjaman->max_limit;

            // Update nilai max_limit baru dan tambahkan selisihnya ke available_limit
            $limitPinjaman->update([
                'max_limit'       => $limitBaru,
                'available_limit' => $limitPinjaman->available_limit + $selisihKenaikan
            ]);

            // Jika semua query database sukses, simpan permanen
            DB::commit();

            // 5. Buatkan format pesan WhatsApp Sukses (Bukan pesan ditolak)
            $namaUser = $setorSimpanan->user->name ?? 'Anggota';
            $jenisSimpananTeks = match($setorSimpanan->simpanan_type) {
                'pokok'    => 'Simpanan Pokok',
                'wajib'    => 'Simpanan Wajib',
                'sukarela' => 'Simpanan Sukarela',
            };

            $message = "Halo *{$namaUser}*,\n\n";
            $message .= "Setoran *{$jenisSimpananTeks}* Anda sebesar *Rp " . number_format($setorSimpanan->amount, 0, ',', '.') . "* telah *DISETUJUI* dan diverifikasi oleh Admin.\n\n";
            $message .= "Saldo simpanan dan limit pinjaman Anda telah diperbarui secara otomatis. Terima kasih.";

            // 6. Kirim WhatsApp ke nomor HP user (Gunakan nama kolom yang sesuai di database: phone / no_hp)
            if ($setorSimpanan->user && $setorSimpanan->user->phone) {
                WhatsAppservices::send($setorSimpanan->user->phone, $message);
            }

            return redirect()->back()->with('success', 'Setoran berhasil disetujui, saldo simpanan, limit pinjaman, dan notifikasi WA telah diperbarui.');

        } catch (\Exception $e) {
            // Jika ada error di tengah jalan, batalkan semua perubahan database di atas
            DB::rollBack();
            Log::info('error verify setor: '.  $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem');
        }
    }
}
