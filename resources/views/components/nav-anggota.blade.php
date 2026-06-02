    @props(['user'])

<!-- Header/Navbar -->
        <nav class="bg-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo/Brand -->
                    <div class="flex items-center">
                        <a href="{{ route('anggota.dashboard') }}" class="flex items-center space-x-2">
                            <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-xl font-bold text-gray-900">Koperasi</span>
                        </a>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('anggota.dashboard') }}" class="px-4 py-2 rounded-lg {{ request()->is('anggota/dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} font-medium transition duration-200">
                            Dashboard
                        </a>
                        <a href="{{ route('anggota.riwayat') }}" class="px-4 py-2 rounded-lg {{ request()->is('anggota/riwayat') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} font-medium transition duration-200">
                            Riwayat Pinjaman
                        </a>
                        <a href="{{ route('anggota.simpanan.index') }}" class="px-4 py-2 rounded-lg {{ request()->is('anggota/simpanan') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} font-medium transition duration-200">
                            Simpanan
                        </a>
                    
                    </div>

                    <!-- User Profile (Desktop) -->
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 font-medium transition duration-200">
                                Logout
                            </button>
                        </form>
                    </div>

                    <!-- Hamburger Menu Button (Mobile) -->
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden md:hidden pb-4">
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('anggota.dashboard') }}" class="px-4 py-2 rounded-lg {{ request()->is('anggota/dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} font-medium transition duration-200">
                            Dashboard
                        </a>
                        <a href="{{ route('anggota.riwayat') }}" class="px-4 py-2 rounded-lg {{ request()->is('anggota/riwayat') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} font-medium transition duration-200">
                            Riwayat Pinjaman
                        </a>
                        <a href="{{ route('anggota.simpanan.index') }}" class="px-4 py-2 rounded-lg {{ request()->is('anggota/riwayat') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-600' }} font-medium transition duration-200">
                            Simpanan
                        </a>
                        <div class="border-t border-gray-200 pt-4 mt-2">
                            <div class="flex items-center space-x-3 px-4">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="mt-3 m-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 font-medium transition duration-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <script>
            // Toggle mobile menu
            document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                mobileMenu.classList.toggle('hidden');
            });
        </script>