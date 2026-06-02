<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link
      href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
      rel="stylesheet"
    />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans antialiased bg-gray-50">
    <x-nav-anggota :user="Auth::user()" />

    <x-flash-message />

    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
      <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Setor Simpanan</h1>
          <p class="text-gray-600 mt-2">Tambahkan dana simpanan Anda</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
          <form action="{{ route('anggota.setor.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Jenis Simpanan -->
            <div>
              <label for="simpanan_type" class="block text-sm font-semibold text-gray-700 mb-2">
                Jenis Simpanan <span class="text-red-500">*</span>
              </label>
              <select 
                id="simpanan_type" 
                name="simpanan_type" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('simpanan_type') border-red-500 @enderror"
                required
              >
                <option value="" disabled selected>Pilih Jenis Simpanan</option>
                <option value="pokok" {{ old('simpanan_type') == 'pokok' ? 'selected' : '' }}>Simpanan Pokok</option>
                <option value="wajib" {{ old('simpanan_type') == 'wajib' ? 'selected' : '' }}>Simpanan Wajib</option>
                <option value="sukarela" {{ old('simpanan_type') == 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela</option>
              </select>
              @error('simpanan_type')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Jumlah Setor -->
            <div>
              <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                Jumlah Setor (Rp) <span class="text-red-500">*</span>
              </label>
              <input 
                type="number" 
                id="amount" 
                name="amount" 
                placeholder="Contoh: 100000"
                value="{{ old('amount') }}"
                min="1000"
                step="1000"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                required
              />
              <p class="mt-1 text-xs text-gray-500">Minimal Rp 1.000</p>
              @error('amount')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Bukti Setor -->
            <div>
              <label for="bukti_setor" class="block text-sm font-semibold text-gray-700 mb-2">
                Bukti Setor (Foto/Scan) <span class="text-red-500">*</span>
              </label>
              <div class="relative">
             
                <input 
                  type="file" 
                  id="bukti_setor" 
                  name="bukti_setor" 
                  accept="image/*,.pdf"
                />
             
              </div>
              <div id="file-name" class="mt-2 text-sm text-gray-600 hidden">
                <p>File: <span id="file-display" class="font-semibold text-blue-600"></span></p>
              </div>
              @error('bukti_setor')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 pt-6">
              <button 
                type="submit" 
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition"
              >
                Setor Simpanan
              </button>
              <a 
                href="{{ route('anggota.simpanan.index') }}" 
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition text-center"
              >
                Batal
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      // File upload preview
      const fileInput = document.getElementById('bukti_setor');
      const fileName = document.getElementById('file-name');
      const fileDisplay = document.getElementById('file-display');

      fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          fileDisplay.textContent = this.files[0].name;
          fileName.classList.remove('hidden');
        } else {
          fileName.classList.add('hidden');
        }
      });
    </script>
  </body>
</html>
