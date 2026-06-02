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

      <!-- main content -->
      <main
        class="flex-1 flex flex-col overflow-hidden ms-0 lg:ms-64"
        id="content"
      >
        <x-topbar />
        <div class="flex-1 overflow-y-auto p-6">
          <!-- Stats Cards -->
          <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
          >
            <!-- Total Member -->
            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Total Member</p>
                  <p class="text-3xl font-bold text-gray-900">248</p>
                  <p class="text-xs text-green-600 mt-1">↑ 12%</p>
                </div>
                <div
                  class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center"
                >
                  <svg
                    class="w-6 h-6 text-blue-600"
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
                </div>
              </div>
            </div>

            <!-- Pengajuan Pending -->
            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Pengajuan Pending</p>
                  <p class="text-3xl font-bold text-gray-900">5</p>
                  <p class="text-xs text-orange-600 mt-1">Perlu Action</p>
                </div>
                <div
                  class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center"
                >
                  <svg
                    class="w-6 h-6 text-orange-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Pinjaman Aktif -->
            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Pinjaman Aktif</p>
                  <p class="text-3xl font-bold text-gray-900">142</p>
                  <p class="text-xs text-blue-600 mt-1">Rp 2.5M</p>
                </div>
                <div
                  class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center"
                >
                  <svg
                    class="w-6 h-6 text-green-600"
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
                </div>
              </div>
            </div>

            <!-- Dana Simpanan -->
            <div class="bg-white rounded-lg shadow p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-600 mb-1">Dana Simpanan</p>
                  <p class="text-3xl font-bold text-gray-900">850Jt</p>
                  <p class="text-xs text-green-600 mt-1">↑ 8%</p>
                </div>
                <div
                  class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center"
                >
                  <svg
                    class="w-6 h-6 text-purple-600"
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
                </div>
              </div>
            </div>
          </div>

          <!-- Pengajuan Pinjaman -->
          <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">
                Pengajuan Pinjaman Pending
              </h2>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Nomor
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Member
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Jumlah
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Tenor
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Tanggal
                    </th>
                    <th
                      class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                    >
                      Aksi
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr class="hover:bg-gray-50">
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                    >
                      LN-2026-0015
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div
                          class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-xs mr-3"
                        >
                          BS
                        </div>
                        <div>
                          <div class="text-sm font-medium text-gray-900">
                            Budi Santoso
                          </div>
                          <div class="text-xs text-gray-500">ID: 12345</div>
                        </div>
                      </div>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"
                    >
                      Rp 5.000.000
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      12 Bulan
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      27 Feb 2026
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <button
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded mr-2 text-xs font-medium"
                      >
                        Approve
                      </button>
                      <button
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium"
                      >
                        Reject
                      </button>
                    </td>
                  </tr>
                  <tr class="hover:bg-gray-50">
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                    >
                      LN-2026-0014
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div
                          class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-semibold text-xs mr-3"
                        >
                          SA
                        </div>
                        <div>
                          <div class="text-sm font-medium text-gray-900">
                            Siti Aminah
                          </div>
                          <div class="text-xs text-gray-500">ID: 12346</div>
                        </div>
                      </div>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"
                    >
                      Rp 3.000.000
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      6 Bulan
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      26 Feb 2026
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <button
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded mr-2 text-xs font-medium"
                      >
                        Approve
                      </button>
                      <button
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium"
                      >
                        Reject
                      </button>
                    </td>
                  </tr>
                  <tr class="hover:bg-gray-50">
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                    >
                      LN-2026-0013
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div
                          class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-semibold text-xs mr-3"
                        >
                          AP
                        </div>
                        <div>
                          <div class="text-sm font-medium text-gray-900">
                            Ahmad Prasetyo
                          </div>
                          <div class="text-xs text-gray-500">ID: 12347</div>
                        </div>
                      </div>
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold"
                    >
                      Rp 10.000.000
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      18 Bulan
                    </td>
                    <td
                      class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      25 Feb 2026
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <button
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded mr-2 text-xs font-medium"
                      >
                        Approve
                      </button>
                      <button
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium"
                      >
                        Reject
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Bottom Section -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Aktivitas -->
            <div class="bg-white rounded-lg shadow">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                  Aktivitas Terbaru
                </h2>
              </div>
              <div class="p-6 space-y-4">
                <div class="flex items-start">
                  <div
                    class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3"
                  ></div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-900">
                      Pembayaran cicilan diterima
                    </p>
                    <p class="text-xs text-gray-500">
                      Budi Santoso - Rp 500.000
                    </p>
                    <p class="text-xs text-gray-400">5 menit yang lalu</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 mr-3"></div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-900">Pengajuan pinjaman baru</p>
                    <p class="text-xs text-gray-500">
                      Siti Aminah - Rp 3.000.000
                    </p>
                    <p class="text-xs text-gray-400">2 jam yang lalu</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <div
                    class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3"
                  ></div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-900">Member baru terdaftar</p>
                    <p class="text-xs text-gray-500">Dewi Lestari</p>
                    <p class="text-xs text-gray-400">3 jam yang lalu</p>
                  </div>
                </div>
                <div class="flex items-start">
                  <div
                    class="w-2 h-2 bg-orange-500 rounded-full mt-2 mr-3"
                  ></div>
                  <div class="flex-1">
                    <p class="text-sm text-gray-900">
                      Dana simpanan ditambahkan
                    </p>
                    <p class="text-xs text-gray-500">
                      Ahmad Prasetyo - Rp 2.000.000
                    </p>
                    <p class="text-xs text-gray-400">5 jam yang lalu</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Status Cicilan -->
            <div class="bg-white rounded-lg shadow">
              <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                  Status Cicilan Bulan Ini
                </h2>
              </div>
              <div class="p-6 space-y-4">
                <div>
                  <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-600">Lunas</span>
                    <span class="text-sm font-semibold text-green-600"
                      >85 (68%)</span
                    >
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-green-500 h-2 rounded-full"
                      style="width: 68%"
                    ></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-600">Pending</span>
                    <span class="text-sm font-semibold text-blue-600"
                      >32 (26%)</span
                    >
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-blue-500 h-2 rounded-full"
                      style="width: 26%"
                    ></div>
                  </div>
                </div>
                <div>
                  <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-600">Terlambat</span>
                    <span class="text-sm font-semibold text-red-600"
                      >8 (6%)</span
                    >
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div
                      class="bg-red-500 h-2 rounded-full"
                      style="width: 6%"
                    ></div>
                  </div>
                </div>
                <div class="pt-4 mt-4 border-t border-gray-200">
                  <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-900"
                      >Total Cicilan</span
                    >
                    <span class="text-sm font-bold text-gray-900">125</span>
                  </div>
                  <div class="flex justify-between mt-2">
                    <span class="text-sm font-medium text-gray-900"
                      >Total Terkumpul</span
                    >
                    <span class="text-sm font-bold text-blue-600"
                      >Rp 45.500.000</span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
