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

                <!-- Menu Items -->
                    <div class="flex flex-wrap justify-center space-x-4 md:space-x-6 font-semibold text-white text-[14px] md:text-[16px] tracking-wide">
                        <a href="{{ '/dashboardMarketing' }}" class="text-white font-bold hover:text-orange-100 transition no-underline">Home</a>
                        <a href="{{ '/posts' }}" class="text-white font-bold hover:text-orange-100 transition no-underline text-center leading-tight">
                            Perusahaan<br>Mitra
                        </a>
                        <a href="{{ route('notifications') }}" class="text-white font-bold hover:text-orange-100 transition no-underline">
                            Notifikasi
                        </a>                       
                         <a href="{{ url('/schedule') }}" class="text-white font-bold hover:text-orange-100 transition no-underline text-center leading-tight">
                            Jadwal<br>Mitra
                        </a>
                    </div>

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="bg-orange-200 text-orange-600 font-bold px-4 py-2 rounded-full shadow-md hover:bg-orange-50 transition flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
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
            <p class="text-xl md:text-2xl font-bold mt-2">MARKETING</p>
        </div>





        {{-- Statistik Utama --}}
        <div class="mx-auto w-[90%] md:w-[60%] bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 flex flex-col md:flex-row
                    items-center divide-y md:divide-y-0 md:divide-x divide-white/50 space-y-6 md:space-y-0">

            {{-- Jumlah Kerjasama --}}
            <div class="flex-1 flex flex-col items-center">
                <i class="fas fa-users text-4xl mb-2"></i>
                <div class="font-semibold text-lg">Jumlah Kerjasama</div>
                <div class="text-5xl font-extrabold mt-1">{{ $total }}</div>
                <div class="uppercase text-sm tracking-wide mt-1">kerjasama</div>
            </div>

            {{-- Update Informasi --}}
            <div class="flex-1 flex flex-col items-center">
                <i class="fas fa-comment-alt text-4xl mb-2"></i>
                <div class="font-semibold text-lg">Update Informasi</div>
                @if($newMessagesCount > 0)
                    <div class="text-3xl font-extrabold mt-1">{{ $newMessagesCount }}</div>
                    <div class="uppercase text-sm tracking-wide mt-1">pesan baru</div>
                @else
                    <div class="mt-2 text-sm">Tidak ada pesan baru</div>
                @endif
            </div>

            {{-- Kerjasama Proses --}}
            <div class="flex-1 flex flex-col items-center">
                <i class="fas fa-calendar-alt text-4xl mb-2"></i>
                <div class="font-semibold text-lg">Kerjasama Dalam Proses</div>
                <span class="bg-green-500 text-white font-bold px-4 py-2 rounded-lg text-lg mt-2">
                    {{ $activeCount }} Proses
                </span>
            </div>

        </div>

        {{-- Footer --}}
        <footer class="bg-orange-300 bg-opacity-10 text-white text-center text-sm py-4 mt-auto">
            <span class="text-orange-500">© 2025 | RSU Prima Medika</span>
        </footer>
    </div>
</x-layout>
