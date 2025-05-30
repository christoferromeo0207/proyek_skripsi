<x-layout>
    <x-slot:title>Dashboard Marketing</x-slot>

    <div class="min-h-screen w-full md:min-h-[100dvh] bg-gradient-to-br from-orange-300 to-orange-400 text-white p-0 m-0 flex flex-col">


        <!-- Top Container -->
        <div class="relative top-10 md:top-16"> 

            <!-- Menu Bar -->
            <div class="mx-auto mb-6 w-[90%] md:w-[60%] rounded-2xl shadow-lg"
                style="background: linear-gradient(to bottom, rgb(255, 234, 214), rgb(251, 146, 50));">
                <div class="flex flex-col md:flex-row justify-between items-center px-4 py-3 space-y-4 md:space-y-0">

                    <!-- Logo -->
                    <div class="flex justify-center md:justify-start">
                        <img src="{{ asset('img/logo_rs.png') }}" alt="Logo" class="w-16 md:w-20 h-16 md:h-20 rounded-full object-cover">
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

                    <!-- User Button Dropdown -->
                  <!-- User Button Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="bg-orange-200 text-orange-600 font-bold px-4 py-2 rounded-full shadow-md hover:bg-orange-50 transition-all duration-200 flex items-center space-x-2 min-w-[160px]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-base truncate max-w-[120px]">
                                {{ Auth::user()->name }}
                            </span>
                        </button>

                        <!-- Dropdown Container -->
                        <div x-show="open" x-cloak
                            x-transition
                            class="absolute right-0 mt-2 w-48 bg-orange-200 border border-orange-200 rounded-lg shadow-lg z-50 overflow-hidden">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-3 text-sm font-bold
                                        text-orange-600 hover:bg-orange-50 transition-colors rounded-b-lg">
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

            <!-- Heading -->
            <div class="text-center py-8 md:py-12">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-wide">SISTEM KELOLA AGENSI MITRA</h1>
                <p class="text-xl md:text-2xl font-bold mt-2">MARKETING</p>
            </div>

            <!-- Statistik Cards -->
           <div class="px-6 md:px-12 grid gap-6 grid-cols-1 md:grid-cols-3 mb-10">
                @foreach($stats as [$title, $count, $desc, $icon, $url])
                <a 
                    href="{{ $url }}" class="block no-underline text-current hover:no-underline">
                    <div class="bg-orange-500 bg-opacity-30 hover:bg-orange-500 hover:scale-105 
                                hover:shadow-2xl transform transition duration-300 ease-in-out 
                                rounded-xl p-6 text-center shadow-lg backdrop-blur-md">
                    
                    <div class="flex justify-center mb-4">
                        <i class="{{ $icon }} text-white text-4xl"></i>
                    </div>
                    
                    <div class="text-xl md:text-2xl font-bold mb-1 text-white">{{ $title }}</div>
                    <div class="text-4xl md:text-5xl font-extrabold text-white leading-tight">
                        {{ $count }}
                    </div>
                    <p class="text-sm mt-2 text-white">{{ $desc }}</p>
                    </div>
                </a>
                @endforeach
            </div>


        </div>

        <!-- Footer -->
        <footer class="bg-orange-300 bg-opacity-10 text-white text-center text-sm py-4 mt-auto">
            <div class="mx-auto max-w-screen-xl">
                <span class="text-sm text-orange-500">© 2025 | RSU Prima Medika</span>
            </div>
        </footer>

    </div>


</x-layout>
