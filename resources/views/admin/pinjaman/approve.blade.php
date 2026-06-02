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
          <!-- Breadcrumb -->
          <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
              <li><a href="{{ route('admin.pinjaman.pending') }}" class="hover:text-blue-600">Pending</a></li>
              <li><span class="mx-2">/</span></li>
              <li><a href="{{ route('admin.pinjaman.show', $pinjaman->id) }}" class="hover:text-blue-600">Detail</a></li>
              <li><span class="mx-2">/</span></li>
              <li class="text-gray-900 font-medium">Approval</li>
            </ol>
          </nav>

          <!-- Page Header -->
          <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Persetujuan Pinjaman</h1>
            <p class="text-gray-600 mt-1">{{ $pinjaman->no_pinjaman }}</p>
          </div>

          <x-flash-message />

          <div class="grid lg:grid-cols-3 gap-6">
            <!-- Form Section -->
            <div class="lg:col-span-2">
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Form Persetujuan & Pencairan</h2>

                <!-- Approve Form -->
                <form action="{{ route('admin.pinjaman.approve', $pinjaman->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                  @csrf

                  <!-- Rekening Tujuan -->
                  <div>
                    <label for="rekening_id" class="block text-sm font-semibold text-gray-700 mb-2">
                      Rekening Tujuan Pencairan <span class="text-red-500">*</span>
                    </label>
                    <select 
                      name="rekening_id" 
                      id="rekening_id" 
                      required
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('rekening_id') border-red-500 @enderror"
                    >
                      <option value="">Pilih Rekening</option>
                      @foreach($rekenings as $rekening)
                      <option value="{{ $rekening->id }}" {{ old('rekening_id') == $rekening->id ? 'selected' : '' }}>
                        {{ $rekening->type_label }} - {{ $rekening->number_rek }} ({{ $rekening->name }})
                      </option>
                      @endforeach
                    </select>
                    @error('rekening_id')
                      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                  </div>

                  <!-- Tanggal Transfer -->
                  <div>
                    <label for="tanggal_transfer" class="block text-sm font-semibold text-gray-700 mb-2">
                      Tanggal Transfer <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="date" 
                      name="tanggal_transfer" 
                      id="tanggal_transfer" 
                      value="{{ old('tanggal_transfer', now()->format('Y-m-d')) }}"
                      required
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_transfer') border-red-500 @enderror"
                    >
                    @error('tanggal_transfer')
                      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                  </div>

                  <!-- Bukti Transfer -->
                  <div>
                    <label for="bukti_transfer" class="block text-sm font-semibold text-gray-700 mb-2">
                      Bukti Transfer <span class="text-red-500">*</span>
                    </label>
                    <input 
                      type="file" 
                      name="bukti_transfer" 
                      id="bukti_transfer" 
                      accept="image/*,application/pdf"
                      required
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bukti_transfer') border-red-500 @enderror"
                    >
                    @error('bukti_transfer')
                      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, PDF. Maksimal 2MB</p>
                  </div>

                  <!-- Catatan -->
                  <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">
                      Catatan (Opsional)
                    </label>
                    <textarea 
                      name="catatan" 
                      id="catatan" 
                      rows="4"
                      maxlength="500"
                      placeholder="Tambahkan catatan jika diperlukan..."
                      class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none @error('catatan') border-red-500 @enderror"
                    >{{ old('catatan') }}</textarea>
                    @error('catatan')
                      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                  </div>

                  <!-- Action Buttons -->
                  <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button 
                      type="submit" 
                      class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                      Setujui & Cairkan Dana
                    </button>
                    <a 
                      href="{{ route('admin.pinjaman.show', $pinjaman->id) }}" 
                      class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg text-center transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    >
                      Batal
                    </a>
                  </div>
                </form>

                <!-- Reject Form -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h3 class="text-md font-semibold text-gray-900 mb-4">Tolak Pengajuan</h3>
                  
                  <form action="{{ route('admin.pinjaman.reject', $pinjaman->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                      <label for="alasan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                      </label>
                      <textarea 
                        name="alasan" 
                        id="alasan" 
                        rows="3"
                        maxlength="500"
                        required
                        placeholder="Jelaskan alasan penolakan..."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                      ></textarea>
                    </div>

                    <button 
                      type="submit" 
                      onclick="return confirm('Yakin ingin menolak pengajuan ini?')"
                      class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                      Tolak Pengajuan
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Summary Section -->
            <div class="space-y-6">
              <!-- Ringkasan Pinjaman -->
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pinjaman</h2>
                
                <div class="space-y-4">
                  <div class="pb-4 border-b border-gray-200">
                    <p class="text-sm text-gray-600">Member</p>
                    <p class="font-semibold text-gray-900">{{ $pinjaman->user->name }}</p>
                  </div>

                  <div class="pb-4 border-b border-gray-200">
                    <p class="text-sm text-gray-600">Jumlah Pinjaman</p>
                    <p class="text-xl font-bold text-blue-600">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                  </div>

                  <div class="pb-4 border-b border-gray-200">
                    <p class="text-sm text-gray-600">Bunga ({{ $pinjaman->bunga }}%)</p>
                    <p class="font-semibold text-gray-900">Rp {{ number_format($pinjaman->jumlah_pinjaman * $pinjaman->bunga / 100, 0, ',', '.') }}</p>
                  </div>

                  <div class="pb-4 border-b border-gray-200">
                    <p class="text-sm text-gray-600">Total Pembayaran</p>
                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($pinjaman->total_payment, 0, ',', '.') }}</p>
                  </div>

                  <div class="pb-4 border-b border-gray-200">
                    <p class="text-sm text-gray-600">Cicilan per Bulan</p>
                    <p class="font-semibold text-green-600">Rp {{ number_format($pinjaman->monthly_payment, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $pinjaman->tenor }} bulan</p>
                  </div>

                  <div>
                    <p class="text-sm text-gray-600">Tanggal Pengajuan</p>
                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pinjaman->applied_date)->format('d F Y') }}</p>
                  </div>
                </div>
              </div>

              <!-- Limit Info -->
              @if($pinjaman->user->limitPinjaman)
              <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-sm opacity-90 mb-1">Limit Member</h3>
                <p class="text-2xl font-bold">Rp {{ number_format($pinjaman->user->limitPinjaman->available_limit, 0, ',', '.') }}</p>
                <p class="text-xs opacity-75 mt-2">Tersedia dari Rp {{ number_format($pinjaman->user->limitPinjaman->max_limit, 0, ',', '.') }}</p>
                
                @if($pinjaman->jumlah_pinjaman > $pinjaman->user->limitPinjaman->available_limit)
                <div class="mt-4 p-3 bg-red-500 bg-opacity-30 rounded-lg">
                  <p class="text-xs">⚠️ Jumlah pinjaman melebihi limit tersedia!</p>
                </div>
                @endif
              </div>
              @endif

              <!-- Info Box -->
              <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                  <svg class="w-5 h-5 text-yellow-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  <div class="text-sm text-yellow-700">
                    <p class="font-semibold mb-1">Perhatian!</p>
                    <p>Pastikan semua data sudah benar sebelum menyetujui. Tindakan ini tidak dapat dibatalkan.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  </body>
</html>
