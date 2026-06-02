<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config("app.name", "Laravel") }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
      href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
      rel="stylesheet"
    />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js',
    'resources/js/dashboard.js'])
  </head>
  <body class="font-sans antialiased bg-gray-50">
    <div class="w-full h-screen flex relative">
      <x-sidebar />

      <div
      class="flex-1 flex flex-col overflow-hidden ms-0 lg:ms-64"
      id="content"
      >
      <x-topbar />
      <!-- main content -->
        <main class="flex-1 overflow-y-auto p-6">
          <div class="max-w-7xl mx-auto">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
              {{-- <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a> --}}
              <span>/</span>
              <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">User</a>
              <span>/</span>
              <span class="text-gray-900 font-medium">Detail User</span>
            </div>

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail User</h1>
                <p class="text-sm text-gray-600 mt-1">Informasi lengkap data member dan limit pinjaman</p>
              </div>
              <div class="flex gap-3">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                  Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition">
                  Kembali
                </a>
              </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
              <!-- Left Column: User Info -->
              <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow p-6">
                  <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b">Informasi Pribadi</h2>
                  
                  <div class="grid md:grid-cols-2 gap-6">
                    <div>
                      <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                      <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                      <p class="text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-500 mb-1">No. Telepon</label>
                      <p class="text-gray-900">{{ $user->phone ?? '-' }}</p>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-500 mb-1">Role</label>
                      <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($user->role) }}
                      </span>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                      <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                        {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $user->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $user->status === 'inactive' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($user->status) }}
                      </span>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Daftar</label>
                      <p class="text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    <div class="md:col-span-2">
                      <label class="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                      <p class="text-gray-900">{{ $user->address ?? '-' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Rekening List -->
                <div class="bg-white rounded-lg shadow p-6">
                  <div class="flex items-center justify-between mb-4 pb-3 border-b">
                    <h2 class="text-lg font-bold text-gray-900">Rekening Pencairan</h2>
                    <span class="text-sm text-gray-600">{{ $user->rekenings->count() }} rekening</span>
                  </div>

                  @forelse($user->rekenings as $rekening)
                    <div class="border border-gray-200 rounded-lg p-4 mb-3 last:mb-0 hover:border-blue-300 transition">
                      <div class="flex items-start justify-between">
                        <div class="flex-1">
                          <div class="flex items-center gap-2 mb-2">
                            <h3 class="font-semibold text-gray-900">{{ $rekening->name }}</h3>
                            <span class="px-2 py-0.5 text-xs font-medium rounded {{ $rekening->type === 'bank' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                              {{ $rekening->type_label }}
                            </span>
                          </div>
                          <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span class="font-mono">{{ $rekening->number_rek }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  @empty
                    <div class="text-center py-8 text-gray-500">
                      <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                      </svg>
                      <p class="text-sm">Belum ada rekening terdaftar</p>
                    </div>
                  @endforelse
                </div>
              </div>

              <!-- Right Column: Financial Info -->
              <div class="space-y-6">
                <!-- Simpanan Cards -->
                @if($user->simpanan)
                  <div class="space-y-3">
                    <!-- Simpanan Pokok -->
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white">
                      <div class="flex items-center justify-between mb-1">
                        <h3 class="text-xs font-medium opacity-90">Simpanan Pokok</h3>
                      </div>
                      <p class="text-2xl font-bold">Rp {{ number_format($user->simpanan->simpanan_pokok, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white">
                      <div class="flex items-center justify-between mb-1">
                        <h3 class="text-xs font-medium opacity-90">Simpanan Wajib</h3>
                      </div>
                      <p class="text-2xl font-bold">Rp {{ number_format($user->simpanan->simpanan_wajib, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white">
                      <div class="flex items-center justify-between mb-1">
                        <h3 class="text-xs font-medium opacity-90">Simpanan Sukarela</h3>
                      </div>
                      <p class="text-2xl font-bold">Rp {{ number_format($user->simpanan->simpanan_sukarela, 0, ',', '.') }}</p>
                    </div>
                  </div>
                @else
                  <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
                    <p class="text-sm">Belum ada data simpanan</p>
                  </div>
                @endif

                <!-- Limit Pinjaman -->
                <div class="bg-white rounded-lg shadow p-6">
                  <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b">Limit Pinjaman</h2>
                  
                  @if($user->limitPinjaman)
                    <div class="space-y-4">
                      <!-- Max Limit -->
                      <div>
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-sm text-gray-600">Limit Maksimal</span>
                          <span class="text-lg font-bold text-gray-900">Rp {{ number_format($user->limitPinjaman->max_limit, 0, ',', '.') }}</span>
                        </div>
                     
                      </div>

                      <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-sm text-gray-600">Limit Tersedia</span>
                          <span class="text-lg font-bold text-green-600">Rp {{ number_format($user->limitPinjaman->available_limit, 0, ',', '.') }}</span>
                        </div>
                      </div>

                      <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-sm text-gray-600">Limit Terpakai</span>
                          <span class="text-lg font-bold text-red-600">Rp {{ number_format($user->limitPinjaman->limit_terpakai, 0, ',', '.') }}</span>
                        </div>
                      </div>

                      <!-- Progress Bar -->
                      <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-2">
                          <span class="text-sm font-medium text-gray-700">Penggunaan Limit</span>
                          <span class="text-sm font-bold text-gray-900">{{ number_format($user->limitPinjaman->persentase_terpakai, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                          <div class="h-3 rounded-full transition-all {{ $user->limitPinjaman->persentase_terpakai >= 80 ? 'bg-red-500' : ($user->limitPinjaman->persentase_terpakai >= 50 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                               style="width: {{ min($user->limitPinjaman->persentase_terpakai, 100) }}%"></div>
                        </div>
                      </div>

                      <!-- Status -->
                      <div class="bg-gray-50 rounded-lg p-3 text-center">
                        @if($user->limitPinjaman->available_limit > 0)
                          <p class="text-sm text-green-700 font-medium">✓ Bisa mengajukan pinjaman</p>
                        @else
                          <p class="text-sm text-red-700 font-medium">✗ Limit sudah habis</p>
                        @endif
                      </div>
                    </div>
                  @else
                    <div class="text-center py-8 text-gray-500">
                      <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                      <p class="text-sm">Belum ada limit pinjaman</p>
                      <p class="text-xs text-gray-400 mt-1">Tambahkan dana simpanan untuk aktivasi limit</p>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </body>
</html>
