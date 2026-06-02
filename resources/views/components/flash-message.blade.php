     <!-- Alerts -->
          @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg mb-4">
              {{ session('success') }}
            </div>
          @endif

          @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
              {{ session('error') }}
            </div>
          @endif

          @if($errors->any() && !$errors->has('password'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
              <div class="font-medium mb-2">Terjadi kesalahan validasi:</div>
              <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                  <li class="text-sm">{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif