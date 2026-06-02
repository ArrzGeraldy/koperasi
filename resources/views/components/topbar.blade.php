  <nav
          class="h-16 w-full bg-white shadow-sm flex items-center justify-between px-6"
        >
          <button class="p-2 rounded-lg transition" id="toggle-topbar">
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
          <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
          <div class="flex items-center space-x-4">
         
            <form method="POST" action="{{ route('logout') }}" class="inline">
              @csrf
              <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition text-sm font-semibold">
                Logout
              </button>
            </form>
          </div>
        </nav>
