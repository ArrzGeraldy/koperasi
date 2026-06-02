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
            <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
              <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">User</a>
              <span>/</span>
              <span class="text-gray-900 font-medium">Tambah User</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah User Baru</h1>
          </div>

          <x-flash-message />


          <!-- Form -->
          <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
            <form action="{{ route('admin.users.store') }}" method="POST">
              @csrf

              <input type="hidden" name="status" value="active">
              
              <div class="grid lg:grid-cols-2 gap-4">
              <!-- Name -->
              <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Email -->
              <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="contoh@email.com" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Phone -->
              <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                @error('phone')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

               <!-- Role -->
              <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                <select name="role" id="role" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror">
                  <option value="">Pilih Role</option>
                  <option value="member" {{ old('role', 'member') == 'member' ? 'selected' : '' }}>Member</option>
                  <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

               <!-- Password -->
              <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Password Confirmation -->
              <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              </div>

              <!-- Address -->
              <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea name="address" id="address" rows="3" placeholder="Masukkan alamat lengkap"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                @error('address')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Simpanan Section -->
              <div class="mb-6 border border-gray-300 rounded-lg p-4 bg-gray-50">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Data Simpanan</h3>
                <div class="grid lg:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Simpanan Pokok <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <span class="absolute left-4 top-2.5 text-gray-500 font-medium">Rp</span>
                      <input type="number" name="simpanan_pokok" value="{{ old('simpanan_pokok', 0) }}" placeholder="0" required min="0" step="1000" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('simpanan_pokok') border-red-500 @enderror">
                    </div>
                    @error('simpanan_pokok')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Simpanan Wajib <span class="text-red-500">*</span></label>
                    <div class="relative">
                      <span class="absolute left-4 top-2.5 text-gray-500 font-medium">Rp</span>
                      <input type="number" name="simpanan_wajib" value="{{ old('simpanan_wajib', 0) }}" placeholder="0" required min="0" step="1000" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('simpanan_wajib') border-red-500 @enderror">
                    </div>
                    @error('simpanan_wajib')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                  <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Simpanan Sukarela</label>
                    <div class="relative">
                      <span class="absolute left-4 top-2.5 text-gray-500 font-medium">Rp</span>
                      <input type="number" name="simpanan_sukarela" value="{{ old('simpanan_sukarela', 0) }}" placeholder="0" min="0" step="1000" class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('simpanan_sukarela') border-red-500 @enderror">
                    </div>
                    @error('simpanan_sukarela')
                      <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <p class="text-xs text-gray-500 mt-3">Limit pinjaman: Simpanan Wajib + Simpanan Sukarela</p>
              </div>

              <!-- Rekening Section -->
              <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-gray-700">Rekening Pencairan <span class="text-red-500">*</span></label>
                  <button type="button" onclick="tambahRekening()" class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded transition">
                    + Tambah Rekening
                  </button>
                </div>
                
                <div id="rekening-container" class="space-y-4">
                  <!-- Rekening pertama -->
                  <div class="rekening-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center justify-between mb-3">
                      <span class="text-sm font-medium text-gray-700">Rekening #1</span>
                    </div>
                    <div class="grid lg:grid-cols-3 gap-3">
                      <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nama Rekening <span class="text-red-500">*</span></label>
                        <input type="text" name="rekenings[0][name]" value="{{ old('rekenings.0.name') }}" placeholder="BCA - John Doe" required
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rekenings.0.name') border-red-500 @enderror">
                        @error('rekenings.0.name')
                          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                        <select name="rekenings[0][type]" required
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rekenings.0.type') border-red-500 @enderror">
                          <option value="">Pilih Tipe</option>
                          <option value="bank" {{ old('rekenings.0.type') == 'bank' ? 'selected' : '' }}>Bank</option>
                          <option value="ewallet" {{ old('rekenings.0.type') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                        @error('rekenings.0.type')
                          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                      </div>
                      <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                        <input type="text" name="rekenings[0][number_rek]" value="{{ old('rekenings.0.number_rek') }}" placeholder="1234567890" required
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rekenings.0.number_rek') border-red-500 @enderror">
                        @error('rekenings.0.number_rek')
                          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                      </div>
                    </div>
                  </div>
                </div>
                
                @error('rekenings')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-2">Minimal 1 rekening untuk pencairan dana pinjaman</p>
              </div>
              <!-- Buttons -->
              <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                  Simpan
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium transition">
                  Batal
                </a>
              </div>
            </form>
          </div>
        </main>
      </div>
    </div>

    <script>
      let rekeningIndex = 1;

      function tambahRekening() {
        const container = document.getElementById('rekening-container');
        const newRekening = `
          <div class="rekening-item border border-gray-300 rounded-lg p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm font-medium text-gray-700">Rekening #${rekeningIndex + 1}</span>
              <button type="button" onclick="hapusRekening(this)" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">
                Hapus
              </button>
            </div>
            <div class="grid lg:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Rekening <span class="text-red-500">*</span></label>
                <input type="text" name="rekenings[${rekeningIndex}][name]" placeholder="BCA - John Doe" required
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                <select name="rekenings[${rekeningIndex}][type]" required
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Pilih Tipe</option>
                  <option value="bank">Bank</option>
                  <option value="ewallet">E-Wallet</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                <input type="text" name="rekenings[${rekeningIndex}][number_rek]" placeholder="1234567890" required
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
            </div>
          </div>
        `;
        container.insertAdjacentHTML('beforeend', newRekening);
        rekeningIndex++;
      }

      function hapusRekening(button) {
        const rekeningItems = document.querySelectorAll('.rekening-item');
        if (rekeningItems.length > 1) {
          button.closest('.rekening-item').remove();
        } else {
          alert('Minimal harus ada 1 rekening!');
        }
      }
    </script>
  </body>
</html>
