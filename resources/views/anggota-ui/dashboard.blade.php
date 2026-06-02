<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <x-nav-anggota :user="$user" />

        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">

                <!-- Informasi Limit Pinjaman -->
                @if($limitPinjaman)
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <p class="text-sm opacity-90">Limit Pinjaman Maksimal Anda</p>
                            <p class="text-4xl font-bold mt-2">Rp {{ number_format($limitPinjaman->max_limit, 0, ',', '.') }}</p>
                            <p class="text-sm opacity-90 mt-1">Limit tersedia: <span class="font-semibold">Rp {{ number_format($limitPinjaman->available_limit, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="hidden sm:block">
                            <svg class="w-24 h-24 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Limit pinjaman Anda belum tersedia. Silakan hubungi admin.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <x-flash-message />

                <div class="flex justify-end"><a  href="/anggota/pengajuan-pinjaman" class="bg-blue-600 text-white  px-2 py-2 text-sm font-medium hover:bg-blue-700 mb-3 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">+ Ajukan Pinjaman</a></div>

                <!-- Pinjaman Cards -->
                <div class="space-y-4">
                  @forelse($pinjamans as $pinjaman)
                  <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 {{ $pinjaman->status == 'pending' ? 'border-2 border-yellow-400' : '' }}">
                    <!-- Card Header -->
                    <div class="px-6 py-4 {{ $pinjaman->status == 'pending' ? 'bg-yellow-50 border-b border-yellow-200' : 'bg-gray-100 border-b border-gray-200' }}">
                      <div class="flex justify-between items-start">
                        <div>
                          <h3 class="text-lg font-semibold text-gray-900">{{ $pinjaman->no_pinjaman }}</h3>
                          @if($pinjaman->approver)
                          <p class="text-sm text-gray-600 mt-1">Disetujui oleh: {{ $pinjaman->approver->name }}</p>
                          @endif
                          @if($pinjaman->status == 'pending')
                          <p class="text-sm text-yellow-700 mt-1 font-medium">Menunggu Persetujuan Admin</p>
                          @endif
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                          {{ $pinjaman->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                          {{ $pinjaman->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                          {{ $pinjaman->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                          {{ $pinjaman->status == 'disbursed' ? 'bg-green-100 text-green-800' : '' }}
                          {{ $pinjaman->status == 'completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                          {{ $pinjaman->status_label }}
                        </span>
                      </div>
                    </div>

                    <!-- Card Body -->
                    <div class="px-6 py-4">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Jumlah Pinjaman -->
                        <div class="bg-gray-50 rounded-lg p-4">
                          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Jumlah Pinjaman</p>
                          <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                          <p class="text-sm text-gray-600 mt-1">Bunga: {{ number_format($pinjaman->bunga, 1) }}%</p>
                        </div>

                        <!-- Total + Bunga -->
                        <div class="bg-blue-50 rounded-lg p-4">
                          <p class="text-xs text-blue-600 uppercase tracking-wide mb-1 font-medium">Total Pembayaran</p>
                          <p class="text-2xl font-bold text-blue-700">Rp {{ number_format($pinjaman->total_payment, 0, ',', '.') }}</p>
                          <p class="text-sm text-blue-600 mt-1">Cicilan: Rp {{ number_format($pinjaman->monthly_payment, 0, ',', '.') }}/bln</p>
                        </div>

                        <!-- Tenor -->
                        <div class="bg-gray-50 rounded-lg p-4">
                          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tenor</p>
                          <p class="text-2xl font-bold text-gray-900">{{ $pinjaman->tenor }} <span class="text-base font-normal text-gray-600">bulan</span></p>
                          <p class="text-sm text-gray-600 mt-1">
                            @if($pinjaman->disbursed_date)
                              Dicairkan: {{ \Carbon\Carbon::parse($pinjaman->disbursed_date)->format('d M Y') }}
                            @elseif($pinjaman->approved_date)
                              Disetujui: {{ \Carbon\Carbon::parse($pinjaman->approved_date)->format('d M Y') }}
                            @else
                              Diajukan: {{ \Carbon\Carbon::parse($pinjaman->applied_date)->format('d M Y') }}
                            @endif
                          </p>
                        </div>

                        <!-- Progress -->
                        <div class="bg-gray-50 rounded-lg p-4">
                          <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Progress Pembayaran</p>
                          @if($pinjaman->status == 'disbursed' || $pinjaman->status == 'completed')
                          <div class="mt-2">
                            <div class="flex justify-between items-center mb-1">
                              <span class="text-sm font-semibold text-gray-900">{{ number_format($pinjaman->persentase_lunas, 1) }}%</span>
                              <span class="text-xs text-gray-500">Rp {{ number_format($pinjaman->paid_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                              <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: {{ $pinjaman->persentase_lunas }}%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">Sisa: Rp {{ number_format($pinjaman->remaining_amount, 0, ',', '.') }}</p>
                          </div>
                          @else
                          <p class="text-sm text-gray-400 mt-2">Belum ada pembayaran</p>
                          @endif
                        </div>
                      </div>

                      <!-- Action Button -->
                      <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('anggota.cicilans', $pinjaman->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                          </svg>
                          Lihat Cicilan
                        </a>
                      </div>
                    </div>
                  </div>
                  @empty
                  <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada data pinjaman</h3>
                    <p class="mt-2 text-sm text-gray-500">Anda belum memiliki riwayat pinjaman.</p>
                  </div>
                  @endforelse
                </div>
            </div>
        </div>
    </body>
</html>
