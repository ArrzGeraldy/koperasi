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
              <li><a href="{{ route('admin.pinjaman.index') }}" class="hover:text-blue-600">Pinjaman</a></li>
              <li><span class="mx-2">/</span></li>
              <li class="text-gray-900 font-medium">Detail</li>
            </ol>
          </nav>

          <!-- Page Header -->
          <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pinjaman</h1>
                <p class="text-gray-600 mt-1">{{ $pinjaman->no_pinjaman }}</p>
              </div>
              <div class="mt-4 sm:mt-0 flex gap-2">
                @if($pinjaman->status === 'pending')
                <a href="{{ route('admin.pinjaman.approve-form', $pinjaman->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                  Proses Approval
                </a>
                @endif
                <a href="{{ route('admin.pinjaman.pending') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                  Kembali
                </a>
              </div>
            </div>
          </div>

          <x-flash-message />

          <div class="grid lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Status Card -->
              <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Status Pinjaman</h2>
                  <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $pinjaman->status_color }}">
                    {{ $pinjaman->status_label }}
                  </span>
                </div>

                @if($pinjaman->status === 'disbursed')
                <div class="mb-4">
                  <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600">Progress Pembayaran</span>
                    <span class="font-semibold text-gray-900">{{ number_format($pinjaman->persentase_lunas, 1) }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $pinjaman->persentase_lunas }}%"></div>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-xs text-gray-600 mb-1">Sudah Dibayar</p>
                    <p class="text-lg font-bold text-green-700">Rp {{ number_format($pinjaman->paid_amount, 0, ',', '.') }}</p>
                  </div>
                  <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <p class="text-xs text-gray-600 mb-1">Sisa Pinjaman</p>
                    <p class="text-lg font-bold text-orange-700">Rp {{ number_format($pinjaman->remaining_amount, 0, ',', '.') }}</p>
                  </div>
                </div>
                @endif
              </div>

              <!-- Detail Pinjaman -->
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Pinjaman</h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                  <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-sm text-gray-600">Jumlah Pinjaman</p>
                    <p class="text-xl font-bold text-gray-900">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                  </div>
                  <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-sm text-gray-600">Total Pembayaran</p>
                    <p class="text-xl font-bold text-gray-900">Rp {{ number_format($pinjaman->total_payment, 0, ',', '.') }}</p>
                  </div>
                  <div class="border-l-4 border-purple-500 pl-4">
                    <p class="text-sm text-gray-600">Cicilan per Bulan</p>
                    <p class="text-xl font-bold text-gray-900">Rp {{ number_format($pinjaman->monthly_payment, 0, ',', '.') }}</p>
                  </div>
                  <div class="border-l-4 border-yellow-500 pl-4">
                    <p class="text-sm text-gray-600">Bunga</p>
                    <p class="text-xl font-bold text-gray-900">{{ $pinjaman->bunga }}% ({{ $pinjaman->tenor }} bulan)</p>
                  </div>
                </div>

                @if($pinjaman->keperluan)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                  <p class="text-sm text-gray-600 mb-1">Keperluan</p>
                  <p class="text-gray-900">{{ $pinjaman->keperluan }}</p>
                </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-gray-600">Tanggal Pengajuan</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($pinjaman->applied_date)->format('d F Y, H:i') }}</p>
                  </div>
                  @if($pinjaman->approved_date)
                  <div>
                    <p class="text-gray-600">Tanggal Disetujui</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($pinjaman->approved_date)->format('d F Y, H:i') }}</p>
                  </div>
                  @endif
                  @if($pinjaman->disbursed_date)
                  <div>
                    <p class="text-gray-600">Tanggal Pencairan</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($pinjaman->disbursed_date)->format('d F Y, H:i') }}</p>
                  </div>
                  @endif
                </div>
              </div>

              <!-- Jadwal Cicilan -->
              @if($pinjaman->cicilans->isNotEmpty())
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Cicilan</h2>
                
                <div class="overflow-x-auto">
                  <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ke-</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibayar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                      @foreach($pinjaman->cicilans as $cicilan)
                      <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $cicilan->no_cicilan }}</td>
                        <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($cicilan->due_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm font-medium">Rp {{ number_format($cicilan->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm">
                          <div>Rp {{ number_format($cicilan->paid_amount, 0, ',', '.') }}</div>
                          @if($cicilan->paid_date)
                          <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($cicilan->paid_date)->format('d M Y') }}</div>
                          @endif
                        </td>
                        <td class="px-4 py-3">
                          <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cicilan->status_color }}">
                            {{ $cicilan->status }}
                          </span>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              @endif

              <!-- Pencairan Dana -->
              @if($pinjaman->pencairanDana)
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pencairan Dana</h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                  <div>
                    <p class="text-sm text-gray-600">Metode Transfer</p>
                    <p class="font-medium text-gray-900">{{ $pinjaman->pencairanDana->metode_transfer }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Nama Pemilik</p>
                    <p class="font-medium text-gray-900">{{ $pinjaman->pencairanDana->nama_pemilik }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Nomor Rekening</p>
                    <p class="font-medium text-gray-900">{{ $pinjaman->pencairanDana->nomor_rekening }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Jumlah Transfer</p>
                    <p class="font-medium text-gray-900">Rp {{ number_format($pinjaman->pencairanDana->jumlah_transfer, 0, ',', '.') }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Tanggal Transfer</p>
                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pinjaman->pencairanDana->tanggal_transfer)->format('d F Y') }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-600">Diproses oleh</p>
                    <p class="font-medium text-gray-900">{{ $pinjaman->pencairanDana->user->name ?? '-' }}</p>
                  </div>
                </div>

                @if($pinjaman->pencairanDana->bukti_transfer)
                <div class="mt-4">
                  <p class="text-sm text-gray-600 mb-2">Bukti Transfer</p>
                  <a href="{{ $pinjaman->pencairanDana->bukti_transfer_url }}" target="_blank" class="inline-block">
                    <img src="{{ $pinjaman->pencairanDana->bukti_transfer_url }}" alt="Bukti Transfer" class="max-w-md rounded-lg border border-gray-200 hover:shadow-lg transition">
                  </a>
                </div>
                @endif
              </div>
              @endif
            </div>

            <!-- Right Column - Member Info -->
            <div class="space-y-6">
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Member</h2>
                
                <div class="flex items-center mb-4">
                  <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr($pinjaman->user->name, 0, 2)) }}
                  </div>
                  <div class="ml-4">
                    <p class="font-semibold text-gray-900">{{ $pinjaman->user->name }}</p>
                    <p class="text-sm text-gray-600">ID: {{ $pinjaman->user->id }}</p>
                  </div>
                </div>

                <div class="space-y-3 text-sm">
                  <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ $pinjaman->user->email }}
                  </div>
                  @if($pinjaman->user->phone)
                  <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    {{ $pinjaman->user->phone }}
                  </div>
                  @endif
                  @if($pinjaman->user->address)
                  <div class="flex items-start text-gray-600">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ $pinjaman->user->address }}
                  </div>
                  @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                  <a href="{{ route('admin.users.show', $pinjaman->user->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat Profile Lengkap →
                  </a>
                </div>
              </div>

              @if($pinjaman->approver)
              <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Disetujui Oleh</h2>
                
                <div class="flex items-center">
                  <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($pinjaman->approver->name, 0, 2)) }}
                  </div>
                  <div class="ml-3">
                    <p class="font-semibold text-gray-900">{{ $pinjaman->approver->name }}</p>
                    <p class="text-sm text-gray-600">Admin</p>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
        </main>
      </div>
    </div>
  </body>
</html>
