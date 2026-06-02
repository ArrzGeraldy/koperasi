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
            <div class="max-w-3xl mx-auto">
                
                <!-- Back Button -->
                <div class="mb-6">
                    <a href="{{ route('anggota.cicilans', $cicilan->pinjaman_id) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar Cicilan
                    </a>
                </div>

                <!-- Page Title -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Upload Bukti Pembayaran Cicilan</h1>
                    <p class="text-sm text-gray-600">No Pinjaman: {{ $cicilan->pinjaman->no_pinjaman }}</p>
                </div>

                <x-flash-message />

                <!-- Cicilan Info -->
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">Detail Cicilan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-blue-700">Cicilan ke-</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $cicilan->no_cicilan }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-700">Jatuh Tempo</p>
                            <p class="text-lg font-semibold text-blue-900">{{ \Carbon\Carbon::parse($cicilan->due_date)->format('d M Y') }}</p>
                            @if($cicilan->isOverdue())
                            <p class="text-xs text-red-600 mt-1 font-medium">⚠️ Terlambat {{ $cicilan->hari_terlambat }} hari</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-blue-700">Jumlah yang Harus Dibayar</p>
                            <p class="text-2xl font-bold text-blue-900">Rp {{ number_format($cicilan->amount, 0, ',', '.') }}</p>
                            @if($cicilan->late_fee > 0)
                            <p class="text-xs text-red-600 mt-1">+Denda: Rp {{ number_format($cicilan->late_fee, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form Upload -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form action="{{ route('anggota.pembayaran-cicilan.store', $cicilan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Tanggal Transfer -->
                        <div class="mb-6">
                            <label for="tanggal_transfer" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Transfer <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal_transfer" 
                                   name="tanggal_transfer" 
                                   value="{{ old('tanggal_transfer', date('Y-m-d')) }}"
                                   max="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tanggal_transfer') border-red-500 @enderror"
                                   required>
                            @error('tanggal_transfer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bukti Transfer -->
                        <div class="mb-6">
                            <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-2">
                                Bukti Transfer <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                   id="bukti_transfer" 
                                   name="bukti_transfer" 
                                   accept="image/jpeg,image/jpg,image/png,application/pdf"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bukti_transfer') border-red-500 @enderror"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">Format: PNG, JPG, PDF (maksimal 2MB)</p>
                            @error('bukti_transfer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info -->
                        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div class="text-sm text-yellow-700">
                                    <p class="font-medium">Perhatian:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1">
                                        <li>Pastikan bukti transfer jelas dan terbaca</li>
                                        <li>Jumlah transfer harus sesuai dengan jumlah cicilan</li>
                                        <li>Bukti pembayaran akan diverifikasi oleh admin</li>
                                        <li>Anda akan mendapat notifikasi setelah verifikasi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('anggota.cicilans', $cicilan->pinjaman_id) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition duration-200">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-200">
                                Upload Bukti Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
