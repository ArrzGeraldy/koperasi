<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rekening;
use App\Models\LimitPinjaman;
use App\Models\Simpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with pagination.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');
        $role = $request->input('role');
        $status = $request->input('status');

        $query = User::query();

        // Search by name or email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role) {
            $query->where('role', $role);
        }

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => ['required', Rule::in(['admin', 'member'])],
            'status' => ['nullable', Rule::in(['pending', 'active', 'inactive'])],
            
            // Validasi simpanan
            'simpanan_pokok' => 'required|numeric|min:0',
            'simpanan_wajib' => 'required|numeric|min:0',
            'simpanan_sukarela' => 'nullable|numeric|min:0',
            
            // Validasi rekening (array)
            'rekenings' => 'required|array|min:1',
            'rekenings.*.name' => 'required|string|max:255',
            'rekenings.*.type' => ['required', Rule::in(['bank', 'ewallet'])],
            'rekenings.*.number_rek' => 'required|string|max:100',
        ], [
            'simpanan_pokok.required' => 'Simpanan pokok harus diisi',
            'simpanan_pokok.min' => 'Simpanan pokok minimal 0',
            'simpanan_wajib.required' => 'Simpanan wajib harus diisi',
            'simpanan_wajib.min' => 'Simpanan wajib minimal 0',
            'simpanan_sukarela.min' => 'Simpanan sukarela minimal 0',
            'rekenings.required' => 'Rekening harus diisi minimal 1',
            'rekenings.*.name.required' => 'Nama rekening harus diisi',
            'rekenings.*.type.required' => 'Tipe rekening harus dipilih',
            'rekenings.*.type.in' => 'Tipe rekening harus Bank atau E-Wallet',
            'rekenings.*.number_rek.required' => 'Nomor rekening harus diisi',
        ]);

        // Custom validation untuk unique number_rek
        foreach ($request->rekenings as $index => $rekening) {
            $exists = Rekening::where('number_rek', $rekening['number_rek'])->exists();
            if ($exists) {
                return redirect()->back()->withInput()->withErrors([
                    "rekenings.{$index}.number_rek" => "Nomor rekening {$rekening['number_rek']} sudah terdaftar"
                ]);
            }
        }

        $validated = $request->all();

        DB::beginTransaction();
        try {
            // Buat user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'role' => $validated['role'],
                'status' => $validated['status'] ?? 'active',
            ]);

            // Buat simpanan
            $simpanan = Simpanan::create([
                'user_id' => $user->id,
                'simpanan_pokok' => $validated['simpanan_pokok'],
                'simpanan_wajib' => $validated['simpanan_wajib'],
                'simpanan_sukarela' => $validated['simpanan_sukarela'] ?? 0,
            ]);

            // Simpan rekening-rekening
            foreach ($validated['rekenings'] as $rekeningData) {
                Rekening::create([
                    'user_id' => $user->id,
                    'name' => $rekeningData['name'],
                    'type' => $rekeningData['type'],
                    'number_rek' => $rekeningData['number_rek'],
                ]);
            }

            // Auto-create limit pinjaman dari simpanan_wajib + simpanan_sukarela
            $totalSimpanan = $simpanan->simpanan_wajib + $simpanan->simpanan_sukarela;
            if ($totalSimpanan > 0) {
                LimitPinjaman::create([
                    'user_id' => $user->id,
                    'max_limit' => $totalSimpanan,
                    'available_limit' => $totalSimpanan,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat dengan ' . count($validated['rekenings']) . ' rekening');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ERROR: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::with(['rekenings', 'limitPinjaman', 'simpanan'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::with(['rekenings', 'simpanan'])->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => ['sometimes', 'required', Rule::in(['admin', 'member'])],
            'status' => ['sometimes', 'required', Rule::in(['pending', 'active', 'inactive'])],
            
            // Validasi simpanan - optional saat update
            'simpanan_pokok' => 'sometimes|numeric|min:0',
            'simpanan_wajib' => 'sometimes|numeric|min:0',
            'simpanan_sukarela' => 'sometimes|numeric|min:0',
            
            // Validasi rekening (array) - optional saat update
            'rekenings' => 'sometimes|array',
            'rekenings.*.id' => 'sometimes|exists:rekenings,id',
            'rekenings.*.name' => 'required_with:rekenings|string|max:255',
            'rekenings.*.type' => ['required_with:rekenings', Rule::in(['bank', 'ewallet'])],
            'rekenings.*.number_rek' => 'required_with:rekenings|string|max:100',
        ]);

        // Custom validation untuk unique number_rek
        if ($request->has('rekenings')) {
            foreach ($request->rekenings as $index => $rekening) {
                $query = Rekening::where('number_rek', $rekening['number_rek']);
                
                // Jika update rekening existing, ignore rekening ini sendiri
                if (isset($rekening['id'])) {
                    $query->where('id', '!=', $rekening['id']);
                }
                
                $exists = $query->exists();
                if ($exists) {
                    return redirect()->back()->withInput()->withErrors([
                        "rekenings.{$index}.number_rek" => "Nomor rekening {$rekening['number_rek']} sudah terdaftar"
                    ]);
                }
            }
        }

        $validated = $request->all();

        DB::beginTransaction();
        try {
            // Update password only if provided
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Simpan simpanan data lama untuk cek perubahan
            $simpananLama = $user->simpanan ? [
                'wajib' => $user->simpanan->simpanan_wajib,
                'sukarela' => $user->simpanan->simpanan_sukarela,
            ] : null;

            // Update user data (exclude simpanan fields)
            $userData = collect($validated)->except(['simpanan_pokok', 'simpanan_wajib', 'simpanan_sukarela', 'rekenings'])->toArray();
            $user->update($userData);

            // Update simpanan jika ada
            if (isset($validated['simpanan_pokok']) || isset($validated['simpanan_wajib']) || isset($validated['simpanan_sukarela'])) {
                if ($user->simpanan) {
                    $user->simpanan->update([
                        'simpanan_pokok' => $validated['simpanan_pokok'] ?? $user->simpanan->simpanan_pokok,
                        'simpanan_wajib' => $validated['simpanan_wajib'] ?? $user->simpanan->simpanan_wajib,
                        'simpanan_sukarela' => $validated['simpanan_sukarela'] ?? $user->simpanan->simpanan_sukarela,
                    ]);
                }
            }

            // Update rekening jika ada
            if (isset($validated['rekenings'])) {
                // Hapus rekening lama yang tidak ada di request
                $rekeningIds = collect($validated['rekenings'])->pluck('id')->filter();
                $user->rekenings()->whereNotIn('id', $rekeningIds)->delete();

                // Update atau create rekening
                foreach ($validated['rekenings'] as $rekeningData) {
                    if (isset($rekeningData['id'])) {
                        // Update existing rekening
                        $rekening = Rekening::find($rekeningData['id']);
                        if ($rekening && $rekening->user_id === $user->id) {
                            $rekening->update([
                                'name' => $rekeningData['name'],
                                'type' => $rekeningData['type'],
                                'number_rek' => $rekeningData['number_rek'],
                            ]);
                        }
                    } else {
                        // Create new rekening
                        Rekening::create([
                            'user_id' => $user->id,
                            'name' => $rekeningData['name'],
                            'type' => $rekeningData['type'],
                            'number_rek' => $rekeningData['number_rek'],
                        ]);
                    }
                }
            }

            // Update limit pinjaman jika simpanan wajib atau sukarela berubah
            $simpananBaru = $user->simpanan ? [
                'wajib' => $user->simpanan->simpanan_wajib,
                'sukarela' => $user->simpanan->simpanan_sukarela,
            ] : null;
            
            if ($simpananLama && $simpananBaru && ($simpananLama != $simpananBaru)) {
                $totalSimpanan = $simpananBaru['wajib'] + $simpananBaru['sukarela'];
                
                LimitPinjaman::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'max_limit' => $totalSimpanan,
                        'available_limit' => $totalSimpanan,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself (optional)
        if (Auth::check() && Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }

  
}
