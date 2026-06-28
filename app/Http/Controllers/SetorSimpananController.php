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
            'simpanan_type' => 'required|array|min:1',
            'simpanan_type.*' => 'in:pokok,wajib,sukarela',
            'amount.pokok' => 'nullable|numeric|min:1000',
            'amount.wajib' => 'nullable|numeric|min:1000',
            'amount.sukarela' => 'nullable|numeric|min:1000',
            'bukti_setor.pokok' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'bukti_setor.wajib' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'bukti_setor.sukarela' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ], [
            'simpanan_type.required' => 'Pilih minimal satu jenis simpanan',
            'simpanan_type.array' => 'Pilihan jenis simpanan tidak valid',
            'simpanan_type.*.in' => 'Jenis simpanan tidak valid',
            'amount.pokok.numeric' => 'Jumlah setor Simpanan Pokok harus berupa angka',
            'amount.pokok.min' => 'Jumlah setor Simpanan Pokok minimal Rp 1.000',
            'amount.wajib.numeric' => 'Jumlah setor Simpanan Wajib harus berupa angka',
            'amount.wajib.min' => 'Jumlah setor Simpanan Wajib minimal Rp 1.000',
            'amount.sukarela.numeric' => 'Jumlah setor Simpanan Sukarela harus berupa angka',
            'amount.sukarela.min' => 'Jumlah setor Simpanan Sukarela minimal Rp 1.000',
            'bukti_setor.pokok.file' => 'Bukti setor Simpanan Pokok harus berupa file',
            'bukti_setor.pokok.mimes' => 'File bukti setor Simpanan Pokok harus berformat JPEG, PNG, JPG, atau PDF',
            'bukti_setor.pokok.max' => 'Ukuran file bukti setor Simpanan Pokok maksimal 5MB',
            'bukti_setor.wajib.file' => 'Bukti setor Simpanan Wajib harus berupa file',
            'bukti_setor.wajib.mimes' => 'File bukti setor Simpanan Wajib harus berformat JPEG, PNG, JPG, atau PDF',
            'bukti_setor.wajib.max' => 'Ukuran file bukti setor Simpanan Wajib maksimal 5MB',
            'bukti_setor.sukarela.file' => 'Bukti setor Simpanan Sukarela harus berupa file',
            'bukti_setor.sukarela.mimes' => 'File bukti setor Simpanan Sukarela harus berformat JPEG, PNG, JPG, atau PDF',
            'bukti_setor.sukarela.max' => 'Ukuran file bukti setor Simpanan Sukarela maksimal 5MB',
        ]);

        $user = Auth::user();
        $selectedTypes = $validated['simpanan_type'] ?? [];
        $amounts = $validated['amount'] ?? [];

        $fileUploads = $request->file('bukti_setor', []);

        foreach ($selectedTypes as $type) {
            if (empty($amounts[$type]) || !is_numeric($amounts[$type]) || $amounts[$type] < 1000) {
                return back()
                    ->withErrors(['amount.' . $type => 'Jumlah setor untuk ' . ($type === 'pokok' ? 'Simpanan Pokok' : ($type === 'wajib' ? 'Simpanan Wajib' : 'Simpanan Sukarela')) . ' harus diisi minimal Rp 1.000'])
                    ->withInput();
            }

            if (empty($fileUploads[$type]) || !$fileUploads[$type]->isValid()) {
                return back()
                    ->withErrors(['bukti_setor.' . $type => 'Bukti setor untuk ' . ($type === 'pokok' ? 'Simpanan Pokok' : ($type === 'wajib' ? 'Simpanan Wajib' : 'Simpanan Sukarela')) . ' harus diunggah'])
                    ->withInput();
            }
        }

        // Handle file uploads per selected type
        $filePaths = [];
        foreach ($selectedTypes as $type) {
            if (!empty($fileUploads[$type]) && $fileUploads[$type]->isValid()) {
                $filePaths[$type] = $fileUploads[$type]->store('setor-simpanan/' . $user->id, 'public');
            }
        }

        // Create setor simpanan history records for each selected type
        foreach ($selectedTypes as $type) {
            SetorSimpananHistory::create([
                'user_id' => $user->id,
                'simpanan_type' => $type,
                'amount' => $amounts[$type],
                'bukti_setor' => $filePaths[$type] ?? null,
            ]);
        }

        // Send WhatsApp notification to admin
        $jenisSimpananText = collect($selectedTypes)->map(function ($type) {
            return match ($type) {
                'pokok' => 'Simpanan Pokok',
                'wajib' => 'Simpanan Wajib',
                'sukarela' => 'Simpanan Sukarela',
            };
        })->implode(', ');

        $jumlahText = collect($selectedTypes)->map(function ($type) use ($amounts) {
            return match ($type) {
                'pokok' => 'Pokok: Rp ' . number_format($amounts[$type], 0, ',', '.'),
                'wajib' => 'Wajib: Rp ' . number_format($amounts[$type], 0, ',', '.'),
                'sukarela' => 'Sukarela: Rp ' . number_format($amounts[$type], 0, ',', '.'),
            };
        })->implode('\n');

        $message = "NOTIFIKASI SETOR SIMPANAN BARU\n\n"
            . "Nama Member: {$user->name}\n"
            . "No. HP: {$user->phone}\n"
            . "Jenis Simpanan: {$jenisSimpananText}\n"
            . "Jumlah:\n{$jumlahText}\n"
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
