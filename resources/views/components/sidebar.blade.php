  <aside
        class="bg-blue-600 text-white w-64 transition-all duration-300 flex-shrink-0 fixed top-0 h-screen z-10 -translate-x-full lg:-translate-x-0"
      >
      <div class="flex items-center justify-between  bg-blue-900">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4">
          <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 20 20">
            <path
              d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
            />
            <path
              fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
              clip-rule="evenodd"
            />
          </svg>
          <span class="text-xl font-bold">Koperasi</span>


        </div>

        <button
        class="p-2 rounded-lg lg:hidden hover:bg-blue-600 transition"
        id="toggle-sidebar"
      >
        <svg
          class="w-5 h-5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16"
          />
        </svg>
      </button>
    </div>

   
        <!-- Navigation -->
        <nav class="px-3 py-4">
          {{-- Dashboard --}}
          <a
            href="/admin/dashboard"
     class="flex items-center px-4 py-3 mb-2 text-blue-100 rounded-lg  transition {{ request()->is('admin/dashboard*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}"
          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
              />
            </svg>
            Dashboard
          </a>

          {{-- anggota --}}
          <a
            href="{{ route('admin.users.index') }}"
            class="flex items-center px-4 py-3 mb-2 text-blue-100 rounded-lg  transition {{ request()->is('admin/users*') ? 'bg-blue-700' : 'hover:bg-blue-700' }}"
          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
            Anggota
          </a>

          {{-- pengajuan pending --}}
          <a
            href="/admin/pinjaman/pending"
                class="flex items-center px-4 py-3 mb-2 text-blue-100 rounded-lg  transition {{ request()->is('admin/pinjaman/pending') ? 'bg-blue-700' : 'hover:bg-blue-700' }}"

          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
              />
            </svg>
            Pengajuan
         
          </a>

          {{-- pinjaman aktif --}}
          <a
            href="/admin/pinjaman"
         class="flex items-center px-4 py-3 mb-2 text-blue-100 rounded-lg  transition {{ request()->is('admin/pinjaman') ? 'bg-blue-700' : 'hover:bg-blue-700' }}"

          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            Pinjaman Aktif
          </a>
          <a
            href="{{ route('admin.pembayaran-cicilan.notif.index') }}"
          class="flex items-center px-4 py-3 mb-2 text-blue-100 rounded-lg  transition {{ request()->is('admin/pembayaran-cicilan/notif') ? 'bg-blue-700' : 'hover:bg-blue-700' }}"
          >
            <svg
              class="w-5 h-5 mr-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
              />
            </svg>
            Notifikasi Pembayaran
          </a>
       
        </nav>

        <!-- User Info -->
        <div
          class="absolute bottom-0 left-0 right-0 flex items-center px-4 py-4 bg-blue-900"
        >
          <div
            class="w-10 h-10 bg-white text-blue-600 rounded-full flex items-center justify-center font-bold"
          >
            AD
          </div>
          <div class="ml-3">
            <p class="text-sm font-semibold">Admin</p>
            <p class="text-xs text-blue-200">{{ auth()->user()->email }}</p>
          </div>
        </div>
      </aside>