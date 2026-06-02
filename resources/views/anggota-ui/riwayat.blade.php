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
        <x-nav-anggota :user="$user" />
  
        <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Riwayat Pinjaman</h1>
                    <p class="text-sm text-gray-600 mt-1">Daftar semua pinjaman yang pernah Anda ajukan</p>
                </div>

                <x-flash-message />

                <!-- Filters & Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <p class="text-xs text-gray-600">Total Pinjaman</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pinjamans->total() }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg shadow-sm p-4">
                        <p class="text-xs text-blue-700">Aktif</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $pinjamans->where('status', 'disbursed')->count() }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg shadow-sm p-4">
                        <p class="text-xs text-green-700">Lunas</p>
                        <p class="text-2xl font-bold text-green-900">{{ $pinjamans->where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg shadow-sm p-4">
                        <p class="text-xs text-yellow-700">Pending</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $pinjamans->where('status', 'pending')->count() }}</p>
                    </div>
                </div>

                <!-- List Pinjaman -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    @if($pinjamans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Pinjaman</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($pinjamans as $pinjaman)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ $pinjaman->no_pinjaman }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pinjaman->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $pinjaman->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $pinjaman->tenor }} bulan</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($pinjaman->status == 'pending')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                        @elseif($pinjaman->status == 'approved')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Disetujui
                                        </span>
                                        @elseif($pinjaman->status == 'disbursed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aktif
                                        </span>
                                        @elseif($pinjaman->status == 'completed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Lunas
                                        </span>
                                        @elseif($pinjaman->status == 'rejected')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Ditolak
                                        </span>
                                        @endif
                                    </td>
                              
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($pinjaman->status == 'disbursed')
                                        <a href="{{ route('anggota.cicilans', $pinjaman->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                            Lihat Cicilan
                                        </a>
                                        @elseif($pinjaman->status == 'completed')
                                        <a href="{{ route('anggota.cicilans', $pinjaman->id) }}" class="text-gray-600 hover:text-gray-900 font-medium">
                                            Lihat Detail
                                        </a>
                                        @else
                                        <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $pinjamans->links() }}
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Belum ada riwayat pinjaman</h3>
                        <p class="mt-2 text-sm text-gray-500">Anda belum pernah mengajukan pinjaman.</p>
                        <div class="mt-6">
                            <a href="{{ route('anggota.pengajuan.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                                Ajukan Pinjaman
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
   
    </body>
</html>
