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
        <!-- Header/Navbar -->
        <x-nav-anggota :user="Auth::user()" />
     

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Pengajuan Pinjaman</h1>
                <p class="text-gray-600 mt-2">Ajukan pinjaman dengan mudah dan cepat</p>
            </div>

            <x-flash-message />

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- Form Section (Left) -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                        <form action="{{ route('anggota.pengajuan.store') }}" method="POST" id="pengajuan-form">
                            @csrf

                            <!-- Limit Info Card -->
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 mb-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm opacity-90 mb-1">Limit Pinjaman Tersedia</p>
                                        <h3 class="text-3xl font-bold">Rp {{ number_format($limitPinjaman->available_limit, 0, ',', '.') }}</h3>
                                        <p class="text-xs opacity-75 mt-2">Maksimal dari Rp {{ number_format($limitPinjaman->max_limit, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="hidden sm:block">
                                        <svg class="w-16 h-16 opacity-30" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Jumlah Pinjaman -->
                            <div class="mb-6">
                                <label for="jumlah_pinjaman" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Jumlah Pinjaman <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 text-gray-500 font-medium">Rp</span>
                                    <input 
                                        type="number" 
                                        name="jumlah_pinjaman" 
                                        id="jumlah_pinjaman" 
                                        value="{{ old('jumlah_pinjaman') }}"
                                        min="100000" 
                                        max="{{ $limitPinjaman->available_limit }}"
                                        step="100000"
                                        placeholder="0"
                                        required
                                        class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('jumlah_pinjaman') border-red-500 @enderror transition"
                                    >
                                </div>
                                @error('jumlah_pinjaman')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-2">Minimal Rp 100.000 - Maksimal Rp {{ number_format($limitPinjaman->available_limit, 0, ',', '.') }}</p>
                            </div>

                            <!-- Info Tenor & Bunga (Read-only) -->
                            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                                <div class="p-4 bg-gray-50 rounded-lg border-2 border-gray-100">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Tenor</label>
                                    <p class="text-2xl font-bold text-gray-900">10 Bulan</p>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-lg border-2 border-gray-100">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Bunga Total</label>
                                    <p class="text-2xl font-bold text-gray-900">20%</p>
                                </div>
                            </div>

                            <!-- Keperluan -->
                            <div class="mb-6">
                                <label for="keperluan" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tujuan Pinjaman (Opsional)
                                </label>
                                <textarea 
                                    name="keperluan" 
                                    id="keperluan" 
                                    rows="4" 
                                    maxlength="500"
                                    placeholder="Contoh: Modal usaha, biaya pendidikan, renovasi rumah, dll."
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none @error('keperluan') border-red-500 @enderror transition"
                                >{{ old('keperluan') }}</textarea>
                                @error('keperluan')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1" id="char-count">0/500 karakter</p>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                                <button 
                                    type="submit" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    Ajukan Pinjaman
                                </button>
                                <a 
                                    href="{{ route('anggota.dashboard') }}" 
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg text-center transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                >
                                    Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Simulasi Section (Right) -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Simulasi Pembayaran
                        </h3>

                        <div class="space-y-4">
                            <!-- Jumlah Pinjaman -->
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Jumlah Pinjaman</span>
                                <span class="text-sm font-bold text-gray-900" id="sim-pokok">Rp 0</span>
                            </div>

                            <!-- Bunga -->
                            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                                <span class="text-sm text-gray-600">Bunga (20%)</span>
                                <span class="text-sm font-bold text-orange-600" id="sim-bunga">Rp 0</span>
                            </div>

                            <!-- Total Pembayaran -->
                            <div class="flex justify-between items-center pb-3 border-b-2 border-gray-300">
                                <span class="text-sm font-semibold text-gray-700">Total Pembayaran</span>
                                <span class="text-lg font-bold text-blue-600" id="sim-total">Rp 0</span>
                            </div>

                            <!-- Cicilan per Bulan -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border-2 border-green-200">
                                <p class="text-xs text-gray-600 mb-1">Cicilan per Bulan (10x)</p>
                                <p class="text-2xl font-bold text-green-700" id="sim-cicilan">Rp 0</p>
                            </div>

                            <!-- Info Box -->
                            <div class="bg-blue-50 rounded-lg p-4 mt-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <p class="text-xs text-blue-800">
                                        Pembayaran akan dilakukan selama <strong>10 bulan</strong> dengan total bunga <strong>30%</strong> dari jumlah pinjaman.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script>
            // Character counter
            const keperluanInput = document.getElementById('keperluan');
            const charCount = document.getElementById('char-count');
            
            keperluanInput.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = `${length}/500 karakter`;
            });

            // Real-time calculation
            const jumlahInput = document.getElementById('jumlah_pinjaman');
            const simPokok = document.getElementById('sim-pokok');
            const simBunga = document.getElementById('sim-bunga');
            const simTotal = document.getElementById('sim-total');
            const simCicilan = document.getElementById('sim-cicilan');

            function formatRupiah(angka) {
                return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function calculateSimulasi() {
                const pokok = parseFloat(jumlahInput.value) || 0;
                const bunga = pokok * 0.20; // 30%
                const total = pokok + bunga;
                const cicilan = total / 10; // 10 bulan

                simPokok.textContent = formatRupiah(pokok);
                simBunga.textContent = formatRupiah(bunga);
                simTotal.textContent = formatRupiah(total);
                simCicilan.textContent = formatRupiah(Math.round(cicilan));
            }

            jumlahInput.addEventListener('input', calculateSimulasi);

            // Initial calculation if old value exists
            if (jumlahInput.value) {
                calculateSimulasi();
            }

            // Mobile menu toggle
            document.getElementById('mobile-menu-button').addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                mobileMenu.classList.toggle('hidden');
            });
        </script>
    </body>
</html>
