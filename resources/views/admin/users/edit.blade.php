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
              <span class="text-gray-900 font-medium">Edit User</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
          </div>

          <x-flash-message />

          <!-- Form -->
          <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="grid lg:grid-cols-2 gap-4">
              <!-- Name -->
              <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Email -->
              <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" placeholder="contoh@email.com" required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Phone -->
              <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
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
                  <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                  <option value="member" {{ old('role', $user->role) == 'member' ? 'selected' : '' }}>Member</option>
                </select>
                @error('role')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Status -->
              <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                  <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                  <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                  <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

                           <!-- Address -->
              <div class="mb-4 lg:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                <textarea name="address" id="address" rows="3" placeholder="Masukkan alamat lengkap"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror">{{ old('address', $user->address) }}</textarea>
                @error('address')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Password (Optional) -->
              <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Password Confirmation -->
              <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              </div>

              <!-- Dana Simpanan -->
              <div class="mb-6">
                <label for="dana_simpanan" class="block text-sm font-medium text-gray-700 mb-2">Dana Simpanan <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-4 top-2.5 text-gray-500 font-medium">Rp</span>
                  <input type="number" name="dana_simpanan" id="dana_simpanan" value="{{ old('dana_simpanan', $user->dana_simpanan) }}" placeholder="0" required min="0" step="1000"
                    class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dana_simpanan') border-red-500 @enderror">
                </div>
                @error('dana_simpanan')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Jika diubah, limit pinjaman akan otomatis ter-update (3x dana simpanan)</p>
              </div>

              <!-- Rekening Section -->
              <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                  <label class="block text-sm font-medium text-gray-700">Rekening Pencairan</label>
                  <button type="button" onclick="tambahRekening()" class="text-sm bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded transition">
                    + Tambah Rekening
                  </button>
                </div>
                
                <div id="rekening-container" class="space-y-4">
                  @forelse($user->rekenings as $index => $rekening)
                    <div class="rekening-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                      <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-700">Rekening #{{ $index + 1 }}</span>
                        @if($index > 0)
                          <button type="button" onclick="hapusRekening(this)" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">
                            Hapus
                          </button>
                        @endif
                      </div>
                      <input type="hidden" name="rekenings[{{ $index }}][id]" value="{{ $rekening->id }}">
                      <div class="grid lg:grid-cols-3 gap-3">
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-1">Nama Rekening <span class="text-red-500">*</span></label>
                          <input type="text" name="rekenings[{{ $index }}][name]" value="{{ old('rekenings.'.$index.'.name', $rekening->name) }}" placeholder="BCA - John Doe" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rekenings.'.$index.'.name') border-red-500 @enderror">
                          @error('rekenings.'.$index.'.name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                          @enderror
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                          <select name="rekenings[{{ $index }}][type]" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rekenings.'.$index.'.type') border-red-500 @enderror">
                            <option value="">Pilih Tipe</option>
                            <option value="bank" {{ old('rekenings.'.$index.'.type', $rekening->type) == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="ewallet" {{ old('rekenings.'.$index.'.type', $rekening->type) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                          </select>
                          @error('rekenings.'.$index.'.type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                          @enderror
                        </div>
                        <div>
                          <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                          <input type="text" name="rekenings[{{ $index }}][number_rek]" value="{{ old('rekenings.'.$index.'.number_rek', $rekening->number_rek) }}" placeholder="1234567890" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('rekenings.'.$index.'.number_rek') border-red-500 @enderror">
                          @error('rekenings.'.$index.'.number_rek')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                          @enderror
                        </div>
                      </div>
                    </div>
                  @empty
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
                  @endforelse
                </div>
                
                @error('rekenings')
                  <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
              </div>

              <!-- Buttons -->
              <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                  Update
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
      // Initialize rekening index based on existing rekenings count
      let rekeningIndex = {{ $user->rekenings->count() > 0 ? $user->rekenings->count() : 1 }};

      function tambahRekening() {
        const container = document.getElementById('rekening-container');
        const newIndex = rekeningIndex;
        const rekeningNumber = newIndex + 1;

        const rekeningHtml = `
          <div class="rekening-item border border-gray-300 rounded-lg p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm font-medium text-gray-700">Rekening #${rekeningNumber}</span>
              <button type="button" onclick="hapusRekening(this)" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded transition">
                Hapus
              </button>
            </div>
            <div class="grid lg:grid-cols-3 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Rekening <span class="text-red-500">*</span></label>
                <input type="text" name="rekenings[${newIndex}][name]" placeholder="BCA - John Doe" required
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tipe <span class="text-red-500">*</span></label>
                <select name="rekenings[${newIndex}][type]" required
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  <option value="">Pilih Tipe</option>
                  <option value="bank">Bank</option>
                  <option value="ewallet">E-Wallet</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening <span class="text-red-500">*</span></label>
                <input type="text" name="rekenings[${newIndex}][number_rek]" placeholder="1234567890" required
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              </div>
            </div>
          </div>
        `;

        container.insertAdjacentHTML('beforeend', rekeningHtml);
        rekeningIndex++;
      }

      function hapusRekening(button) {
        const container = document.getElementById('rekening-container');
        const items = container.querySelectorAll('.rekening-item');
        
        if (items.length <= 1) {
          alert('Minimal harus ada 1 rekening!');
          return;
        }

        button.closest('.rekening-item').remove();
      }
    </script>
  </body>
</html>
