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
          <!-- Page Header -->
          <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Pengajuan Pinjaman Pending</h1>
                <p class="text-gray-600 mt-1">Daftar pengajuan pinjaman yang menunggu persetujuan</p>
              </div>
              <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.pinjaman.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  Lihat Semua Pinjaman
                </a>
              </div>
            </div>
          </div>

          <x-flash-message />

          <!-- Filter & Search -->
          <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('admin.pinjaman.pending') }}" class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <input 
                  type="text" 
                  name="search" 
                  value="{{ request('search') }}"
                  placeholder="Cari nomor pinjaman atau nama member..." 
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
              </div>
              <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                Cari
              </button>
              @if(request('search'))
              <a href="{{ route('admin.pinjaman.pending') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                Reset
              </a>
              @endif
            </form>
          </div>

          <!-- Table -->
          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Pinjaman</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @forelse($pinjamans as $pinjaman)
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ $pinjaman->no_pinjaman }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ $pinjaman->user->name }}</div>
                      <div class="text-xs text-gray-500">ID: {{ $pinjaman->user->id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</div>
                      <div class="text-xs text-gray-500">Total: Rp {{ number_format($pinjaman->total_payment, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ $pinjaman->tenor }} Bulan</div>
                      <div class="text-xs text-gray-500">Bunga: {{ $pinjaman->bunga }}%</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ \Carbon\Carbon::parse($pinjaman->applied_date)->format('d M Y') }}
                      <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($pinjaman->applied_date)->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <div class="flex space-x-2">
                        <a href="{{ route('admin.pinjaman.show', $pinjaman->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                          Detail
                        </a>
                        <a href="{{ route('admin.pinjaman.approve-form', $pinjaman->id) }}" class="text-green-600 hover:text-green-800 font-medium">
                          Proses
                        </a>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                      <p class="mt-4 text-gray-500">Tidak ada pengajuan pinjaman pending</p>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($pinjamans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
              {{ $pinjamans->links() }}
            </div>
            @endif
          </div>
        </main>
      </div>
    </div>
  </body>
</html>
