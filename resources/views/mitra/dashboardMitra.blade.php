
<x-layout>
  <x-slot:title>Dashboard Mitra</x-slot:title>

  <div class="min-h-screen bg-gradient-to-br from-orange-300 to-orange-400 text-white flex flex-col">

    {{-- Menu Bar --}}
    <div class="mx-auto mt-6 w-[90%] md:w-[60%] rounded-2xl shadow-lg"
         style="background: linear-gradient(to bottom, #FFEAD6, #FB9232);">
      <div class="flex flex-col md:flex-row justify-between items-center px-4 py-3 space-y-4 md:space-y-0">
        {{-- Logo --}}
        <div class="flex justify-center md:justify-start">
          <img src="{{ asset('img/logo_rs.png') }}" alt="Logo"
               class="w-16 md:w-20 h-16 md:h-20 rounded-full object-cover">
        </div>

        {{-- Menu Items --}}
        <div class="flex flex-wrap justify-center space-x-4 md:space-x-6 font-semibold text-white text-[14px] md:text-[16px] tracking-wide">
            <a href="{{ route('mitra.dashboard') }}"
                 class="text-white font-bold hover:text-orange-100 transition no-underline">Home</a>
            <a href="{{ route('mitra.informasi.show', $post) }}"
                class="text-white font-bold hover:text-orange-100 transition no-underline text-center leading-tight">
                Informasi<br>Mitra
            </a>
           
            <a
              href="{{ route('mitra.informasi.notifications', $post) }}"
              class="text-white font-bold hover:text-orange-100 transition no-underline">
              Notifikasi
            </a>

        {{-- Pengajuan Kerjasama dropdown --}}
          <div class="relative" x-data="{ open: false }">
            <a href="#"
              @click.prevent="open = !open"
              class="text-white font-bold hover:text-orange-100 transition no-underline text-center leading-tight flex items-center space-x-1">
              <span>Pengajuan<br>Kerjasama</span>
              <i class="fas fa-caret-down text-sm"></i>
            </a>

            <div x-show="open"
                @click.outside="open = false"
                x-cloak
                class="absolute right-0 mt-2 w-48 bg-orange-200 rounded-lg shadow-lg z-50">
              {{-- Pengajuan Transaksi baru --}}
                <a href="{{ route('mitra.informasi.transactions.create', $post) }}"
                class="block px-4 py-2 hover:bg-gray-100 text-orange-500 no-underline">
                Pengajuan Transaksi Baru
              </a>
              {{-- Pengajuan Perusahaan baru --}}
              <a href="{{ route('mitra.create') }}"
                class="block px-4 py-2 hover:bg-gray-100 text-orange-500 no-underline">
                Pengajuan Mitra Baru
              </a>
            </div>
          </div>
          
        </div>

        {{-- User Dropdown --}}
        <div class="relative" x-data="{ open: false }">
          <button @click="open = !open"
                  class="bg-orange-200 text-orange-600 font-bold px-4 py-2 rounded-full shadow-md hover:bg-orange-50 transition flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5 text-orange-600"
                 fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0z M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="truncate max-w-[120px]">{{ Auth::user()->name }}</span>
          </button>

          <div x-show="open" x-cloak x-transition
               class="absolute right-0 mt-2 w-48 bg-orange-200 border border-orange-200 rounded-lg shadow-lg z-50">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                      class="w-full flex items-center gap-2 px-4 py-3 text-sm font-bold text-orange-600 hover:bg-orange-50 transition rounded-b-lg">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="h-5 w-5" fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0
                        01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013
                        3v1" />
                </svg>
                Keluar
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>

    {{-- Heading --}}
    <div class="text-center py-8 md:py-12">
      <h1 class="text-3xl md:text-4xl font-extrabold tracking-wide">SISTEM KELOLA AGENSI MITRA</h1>
      <p class="text-xl md:text-2xl font-bold mt-2">{{ $companyTitle }}</p>
    </div>

    {{-- Statistik Informasi (Cards) --}}
    <div class="px-6 md:px-12 grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

      {{-- Card: Jumlah Kerjasama --}}
      <a href="{{ url('/schedule') }}"
         class="block bg-orange-200 bg-opacity-50 rounded-2xl p-6 shadow-lg
                transform transition hover:shadow-xl hover:scale-105 hover:bg-orange-400
                no-underline">
        <div class="flex justify-center mb-4">
          <i class="fas fa-handshake text-white text-4xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-white text-center mb-2">
          Jumlah Kerjasama
        </h3>
        <div class="text-3xl font-extrabold text-white text-center mb-1">
          {{ $total }}
        </div>
        <p class="text-sm text-white/90 text-center">Transaksi</p>
      </a>

      {{-- Card: Jumlah Pesan --}}
      <a href="{{ route('mitra.informasi.notifications', $post) }}"
        class="block bg-orange-200 bg-opacity-50 rounded-2xl p-6 shadow-lg
                transform transition hover:shadow-xl hover:scale-105 hover:bg-orange-400
                no-underline">
        <div class="flex justify-center mb-4">
          <i class="fas fa-comment-alt text-white text-4xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-white text-center mb-2">
          Jumlah Pesan
        </h3>
        <div class="text-3xl font-extrabold text-white text-center mb-1">
          {{ $messageCount }}
        </div>
        <p class="text-sm text-white/90 text-center">
          Pesan total untuk perusahaan ini
        </p>
      </a>


      {{-- Card: Status Kerjasama --}}
      <a href="{{ url('/schedule') }}"
         class="block bg-orange-200 bg-opacity-50 rounded-2xl p-6 shadow-lg
                transform transition hover:shadow-xl hover:scale-105 hover:bg-orange-400
                no-underline">
        <div class="flex justify-center mb-4">
          <i class="fas fa-calendar-alt text-white text-4xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-white text-center mb-2">
          Status Kerjasama
        </h3>
        <div class="text-3xl font-extrabold text-white text-center mb-1">
          {{ $activeCount }}
        </div>
        <p class="text-sm text-white/90 text-center">Aktif</p>
      </a>

    </div>

    
    {{-- Footer --}}
    <footer class="bg-orange-300 bg-opacity-10 text-white text-center text-sm py-4 mt-auto">
      <span class="text-orange-500">© 2025 | RSU Prima Medika</span>
    </footer>

  </div>
</x-layout>
