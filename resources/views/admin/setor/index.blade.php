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
          <!-- Header -->
          <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Verifikasi Setor Simpanan</h1>
            <p class="text-sm text-gray-600 mt-1">Daftar Setor Simpanan yang menunggu verifikasi</p>
          </div>

          <x-flash-message />

          <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if($setorSimpanans->count() > 0)
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anggota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Simpanan</th>
                
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
              
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @foreach($setorSimpanans as $setorSimpanan)
                  <tr class="hover:bg-gray-50">
                    <!-- Anggota -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ $setorSimpanan->user->name }}</div>
                        <div class="text-xs text-gray-500">ID: {{ $setorSimpanan->user->id }}</div>
                      </div>
                    </td>

                    <!-- jenis simpanan -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ $setorSimpanan->simpanan_type }}</div>
                    </td>


                    <!-- Jumlah -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($setorSimpanan->amount, 0, ',', '.') }}</div>
                     
                    </td>

                    <!-- Bukti -->
                    <td class="px-6 py-4 whitespace-nowrap">
                      <a href="{{ Storage::url($setorSimpanan->bukti_setor) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-200 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat
                      </a>
                    </td>

                    <!-- Aksi -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <div class="flex items-center space-x-2">
                        <!-- Approve Button -->
                        <form action="{{ route('admin.setor.verify', $setorSimpanan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui setoran ini?')">
                          @csrf
                          @method('PUT')
                          <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Setuju
                          </button>
                        </form>

                        <!-- Reject Button -->
                        <button onclick="showRejectModal({{ $setorSimpanan->id }}, '{{ $setorSimpanan->user->name }}')" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                          </svg>
                          Tolak
                        </button>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @else

            <p class="text-sm text-gray-600 mb-4">
    Anda akan menolak setoran dari: <span id="rejectUserName" class="font-semibold text-gray-900"></span>
  </p>
            <!-- Empty State -->
            <div class="p-12 text-center">
              <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <h3 class="mt-4 text-lg font-medium text-gray-900">Tidak ada setoran pending</h3>
              <p class="mt-2 text-sm text-gray-500">Semua setoran cicilan sudah diverifikasi.</p>
            </div>
            @endif
          </div>
        
        </main>

        <!-- Reject Modal -->
        <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Tolak Setoran</h3>
                <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
              
       

              <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                  <label for="rejection_note" class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                  </label>
                  <textarea id="rejection_note" 
                            name="rejection_note" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="Jelaskan alasan penolakan..."
                            required></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                  <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                  </button>
                  <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    Tolak Setoran
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      function showRejectModal(setoranId, userName) {
        // document.getElementById('rejectUserName').innerHTML = userName;
        document.getElementById('rejectForm').action = '/admin/setor/' + setoranId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
      }

      function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejection_note').value = '';
      }

      // Close modal on outside click
      document.getElementById('rejectModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeRejectModal();
        }
      });
    </script>
  </body>
</html>
