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

            <!-- Pilih Jenis Simpanan -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                Pilih Jenis Simpanan <span class="text-red-500">*</span>
              </label>
              <div class="space-y-4">
                @php
                  $types = [
                    'pokok' => 'Simpanan Pokok',
                    'wajib' => 'Simpanan Wajib',
                    'sukarela' => 'Simpanan Sukarela',
                  ];
                  $selectedTypes = old('simpanan_type', []);
                @endphp

                @foreach ($types as $type => $label)
                  <div class="grid gap-4 sm:grid-cols-[auto_1fr] items-start p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center gap-2 mt-1">
                      <input
                        type="checkbox"
                        id="simpanan_{{ $type }}"
                        name="simpanan_type[]"
                        value="{{ $type }}"
                        data-type="{{ $type }}"
                        class="type-checkbox h-4 w-4 text-blue-600 border-gray-900 rounded"
                        {{ in_array($type, $selectedTypes) ? 'checked' : '' }}
                      />
                      <label for="simpanan_{{ $type }}" class="font-semibold text-gray-700">
                        {{ $label }}
                      </label>
                    </div>
                    <div class="space-y-3">
                      <div>
                        <label for="amount_{{ $type }}" class="sr-only">Jumlah setor {{ $label }}</label>
                        <input
                          type="number"
                          id="amount_{{ $type }}"
                          name="amount[{{ $type }}]"
                          placeholder="Jumlah setor untuk {{ $label }}"
                          value="{{ old('amount.' . $type) }}"
                          min="1000"
                          step="1000"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount.' . $type) border-red-500 @enderror"
                        />
                        <p class="mt-1 text-xs text-gray-500">Minimal Rp 1.000 jika dipilih.</p>
                        @error('amount.' . $type)
                          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                      </div>
                      <div>
                        <label for="bukti_setor_{{ $type }}" class="block text-sm font-semibold text-gray-700 mb-2">
                          Bukti Setor {{ $label }} <span class="text-red-500">*</span>
                        </label>
                        <input
                          type="file"
                          id="bukti_setor_{{ $type }}"
                          name="bukti_setor[{{ $type }}]"
                          accept="image/*,.pdf"
                          class="@error('bukti_setor.' . $type) border-red-500 @enderror"
                        />
                        @error('bukti_setor.' . $type)
                          <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
              @error('simpanan_type')
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
