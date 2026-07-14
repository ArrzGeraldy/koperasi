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
                
                <!-- Back Button -->
                <div class="mb-6">
                    <a href="{{ route('anggota.dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>

                <!-- Pinjaman Info Header -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 mb-6 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $pinjaman->no_pinjaman }}</h1>
                            <div class="mt-3 space-y-1">
                                <p class="text-sm opacity-90">Jumlah Pinjaman: <span class="font-semibold">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</span></p>
                                <p class="text-sm opacity-90">Total Pembayaran: <span class="font-semibold">Rp {{ number_format($pinjaman->total_payment, 0, ',', '.') }}</span></p>
                                <p class="text-sm opacity-90">Tenor: <span class="font-semibold">{{ $pinjaman->tenor }} bulan</span></p>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 md:text-right">
                            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-3">
                                <p class="text-xs opacity-90">Cicilan per bulan</p>
                                <p class="text-3xl font-bold mt-1">Rp {{ number_format($pinjaman->monthly_payment, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    @if($pinjaman->status == 'disbursed' || $pinjaman->status == 'completed')
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium">Progress Pembayaran</span>
                            <span class="text-sm font-semibold">{{ number_format($pinjaman->persentase_lunas, 1) }}%</span>
                        </div>
                        <div class="w-full bg-white bg-opacity-30 rounded-full h-3">
                            <div class="bg-white h-3 rounded-full transition-all duration-300" style="width: {{ $pinjaman->persentase_lunas }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-2 text-sm">
                            <span>Terbayar: Rp {{ number_format($pinjaman->paid_amount, 0, ',', '.') }}</span>
                            <span>Sisa: Rp {{ number_format($pinjaman->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endif
                </div>

                @php
                    $firstUnpaid = $pinjaman->cicilans->firstWhere('status', 'unpaid');
                @endphp

                @if($firstUnpaid)
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('anggota.pembayaran-cicilan.create', $firstUnpaid->id) }}?full=1" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium">
                        Sekali Bayar - Lunasi Seluruh Sisa
                    </a>
                </div>
                @endif

                <x-flash-message />

                <!-- Cicilan Cards -->
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Daftar Cicilan</h2>
                    <p class="text-sm text-gray-600 mt-1">Total {{ $pinjaman->cicilans->count() }} cicilan</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($pinjaman->cicilans as $cicilan)
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-200 
                        {{ $cicilan->isOverdue() ? 'border-l-4 border-red-500' : '' }}
                        {{ $cicilan->isPaid() ? 'border-l-4 border-green-500' : '' }}
                        {{ !$cicilan->isPaid() && !$cicilan->isOverdue() ? 'border-l-4 border-yellow-500' : '' }}">
                        
                        <div class="space-y-3">
                            <!-- Cicilan -->
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">Cicilan ke-{{ $cicilan->no_cicilan }}</h3>
                                    @if($cicilan->isPaid() && $cicilan->paid_date)
                                    <p class="text-xs text-green-600 mt-1">✓ Dibayar: {{ \Carbon\Carbon::parse($cicilan->paid_date)->format('d M Y') }}</p>
                                    @endif
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    {{ $cicilan->isPaid() ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $cicilan->isOverdue() ? 'bg-red-100 text-red-800' : '' }}
                                    {{ !$cicilan->isPaid() && !$cicilan->isOverdue() ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ $cicilan->status_label }}
                                </span>
                            </div>

                            <!-- Jatuh Tempo -->
                            <div>
                                <p class="text-xs text-gray-500">Jatuh Tempo</p>
                                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($cicilan->due_date)->format('d M Y') }}</p>
                                @if($cicilan->isOverdue())
                                <p class="text-xs text-red-600 mt-1 font-medium">⚠️ Terlambat {{ $cicilan->hari_terlambat }} hari</p>
                                @endif
                            </div>

                            <!-- Total & Terbayar -->
                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-200">
                                <div>
                                    <p class="text-xs text-gray-500">Total Cicilan</p>
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($cicilan->amount, 0, ',', '.') }}</p>
                                    @if($cicilan->late_fee > 0)
                                    <p class="text-xs text-red-600 mt-1">+Denda: Rp {{ number_format($cicilan->late_fee, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Terbayar</p>
                                    <p class="text-lg font-bold text-blue-600">Rp {{ number_format($cicilan->paid_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <!-- Aksi -->
                            @if($cicilan->status === 'paid')
                                <!-- Sudah Lunas -->
                                <div class="pt-2">
                                    <div class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Sudah Lunas
                                    </div>
                                </div>
                            @elseif($cicilan->status === 'pending_verification')
                                <!-- Menunggu Verifikasi -->
                                <div class="pt-2">
                                    <div class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Menunggu Verifikasi
                                    </div>
                                </div>
                            @elseif($cicilan->status === 'rejected')
                                <!-- Ditolak - bisa upload ulang -->
                                <div class="pt-2 space-y-2">
                                    <div class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 text-xs font-medium rounded-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Pembayaran Ditolak
                                    </div>
                                    <a href="{{ route('anggota.pembayaran-cicilan.create', $cicilan->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Upload Ulang Bukti
                                    </a>
                                </div>
                            @else
                                <!-- Belum Bayar -->
                                <div class="pt-2">
                                    <a href="{{ route('anggota.pembayaran-cicilan.create', $cicilan->id) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Bayar Cicilan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 bg-white rounded-lg shadow-sm p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada data cicilan</h3>
                        <p class="mt-2 text-sm text-gray-500">Belum ada cicilan untuk pinjaman ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </body>
</html>
