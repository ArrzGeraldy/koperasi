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
      <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Simpanan Anda</h1>
            <p class="text-gray-600 mt-1">Kelola dan pantau simpanan</p>
          </div>
          <a href="{{ route('anggota.setor.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Setor Simpanan
          </a>
        </div>

        <!-- Simpanan Cards -->
        @if($simpanan)
        <div class="grid md:grid-cols-3 gap-4">
          <!-- Simpanan Pokok -->
          <div
            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-4 text-white"
          >
            <div class="flex items-center justify-between mb-1">
              <h3 class="text-xs font-medium opacity-90">Simpanan Pokok</h3>
            </div>
            <p class="text-2xl font-bold">
              Rp {{ number_format($simpanan->simpanan_pokok, 0, ',', '.') }}
            </p>
          </div>
          <!-- Simpanan Wajib -->
          <div
            class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-4 text-white"
          >
            <div class="flex items-center justify-between mb-1">
              <h3 class="text-xs font-medium opacity-90">Simpanan Wajib</h3>
            </div>
            <p class="text-2xl font-bold">
              Rp {{ number_format($simpanan->simpanan_wajib, 0, ',', '.') }}
            </p>
          </div>
          <!-- Simpanan Sukarela -->
          <div
            class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-4 text-white"
          >
            <div class="flex items-center justify-between mb-1">
              <h3 class="text-xs font-medium opacity-90">
                Simpanan Sukarela
              </h3>
            </div>
            <p class="text-2xl font-bold">
              Rp {{ number_format($simpanan->simpanan_sukarela, 0, ',', '.')
              }}
            </p>
          </div>
        </div>
        @else
        <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500">
          <p class="text-sm">Belum ada data simpanan</p>
        </div>
        @endif
      </div>
    </div>
  </body>
</html>
