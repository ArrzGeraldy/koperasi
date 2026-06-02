<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Rekening;
use App\Models\LimitPinjaman;
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
            'dana_simpanan' => 'required|numeric|min:0',
            'role' => ['required', Rule::in(['admin', 'member'])],
            'status' => ['nullable', Rule::in(['pending', 'active', 'inactive'])],
            
            // Validasi rekening (array)
            'rekenings' => 'required|array|min:1',
            'rekenings.*.name' => 'required|string|max:255',
            'rekenings.*.type' => ['required', Rule::in(['bank', 'ewallet'])],
            'rekenings.*.number_rek' => 'required|string|max:100',
        ], [
            'dana_simpanan.required' => 'Dana simpanan harus diisi',
            'dana_simpanan.min' => 'Dana simpanan minimal 0',
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
                'dana_simpanan' => $validated['dana_simpanan'],
                'role' => $validated['role'],
                'status' => $validated['status'] ?? 'active', // Default active jika ada dana simpanan
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

            // Auto-create limit pinjaman jika ada dana simpanan
            if ($validated['dana_simpanan'] > 0) {
                $multiplier = 3; // Default 5x dana simpanan
                $maxLimit = $validated['dana_simpanan'] * $multiplier;
                
                LimitPinjaman::create([
                    'user_id' => $user->id,
                    'max_limit' => $maxLimit,
                    'available_limit' => $maxLimit,
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
        $user = User::with(['rekenings', 'limitPinjaman'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::with(['rekenings'])->findOrFail($id);
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
            'dana_simpanan' => 'sometimes|required|numeric|min:0',
            'role' => ['sometimes', 'required', Rule::in(['admin', 'member'])],
            'status' => ['sometimes', 'required', Rule::in(['pending', 'active', 'inactive'])],
            
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

            // Simpan dana simpanan lama untuk cek perubahan
            $danaSimpananLama = $user->dana_simpanan;

            // Update user data
            $user->update($validated);

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

            // Update limit pinjaman jika dana simpanan berubah
            if (isset($validated['dana_simpanan']) && $validated['dana_simpanan'] != $danaSimpananLama) {
                $multiplier = 5; // Default 5x
                $maxLimit = $validated['dana_simpanan'] * $multiplier;
                
                LimitPinjaman::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'max_limit' => $maxLimit,
                        'available_limit' => $maxLimit,
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
