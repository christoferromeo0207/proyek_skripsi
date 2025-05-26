<!DOCTYPE html>
<html lang="en" class="w-full h-full bg-orange-300 m-0 p-0">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  @vite(['resources/css/app.css','resources/js/app.js'])

  <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>

  <title>Sistem Kelola Agensi Mitra | {{ $title }}</title>

  <style>
    html, body {
      margin: 0; padding: 0;
      width: 100%; height: 100%;
      overflow-x: hidden;
    }
  </style>
</head>

<body class="h-full w-full m-0 p-0 overflow-x-hidden"
      x-data="{ sidebarOpen: false, scrollY: window.scrollY, showHeader: true,
                onScroll() {
                  this.showHeader = window.scrollY < this.scrollY;
                  this.scrollY  = window.scrollY;
                }
              }"
      @scroll.window="onScroll"
>
  {{-- Header --}}
  @if (! request()->routeIs(['dashboard','marketing.dashboard','mitra.dashboard']))
    @php
      // choose home route by role
      $homeRoute = auth()->user()->role === 'mitra'
                 ? route('mitra.dashboard')
                 : route('dashboard');
    @endphp

    <div x-show="showHeader"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-3"
         class="sticky top-0 z-50 bg-orange-200 px-6 pt-3 pb-1 shadow-md"
    >
      <div class="flex justify-between items-start">

        {{-- Logo / Title --}}
        <a href="{{ $homeRoute }}"
           class="flex items-center space-x-3 hover:opacity-90 transition duration-200 no-underline">
          <img src="{{ asset('img/logo_rs.png') }}"
               alt="Logo"
               class="w-12 h-12 rounded-full object-cover">
          <div class="leading-5">
            <span class="font-bold text-xl text-[rgb(255,138,43)]">
              Sistem Kelola Agensi Mitra
            </span>
          </div>
        </a>

        {{-- User dropdown --}}
        <div class="relative" x-data="{ open: false }"
             @mouseenter="open = true" @mouseleave="open = false"
        >
          <button @click="open = !open"
                  class="flex items-center space-x-2 mt-1 pt-1 hover:opacity-80 transition-opacity duration-300 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-7 h-7"
                 fill="rgb(255, 138, 43)"
                 viewBox="0 0 24 24">
              <path d="…"/>
            </svg>
            <span class="font-bold text-lg" style="color: rgb(255, 138, 43);">
              {{ Auth::user()->name }}
            </span>
          </button>

          <div x-show="open"
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="opacity-0 scale-95 translate-y-1"
               x-transition:enter-end="opacity-100 scale-100 translate-y-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="opacity-100 scale-100 translate-y-0"
               x-transition:leave-end="opacity-0 scale-95 translate-y-1"
               class="absolute right-0 mt-2 w-48 bg-orange-100 border border-orange-300 rounded-lg shadow-xl z-50 overflow-hidden"
          >
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                      class="flex items-center gap-2 w-full px-4 py-2 font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition duration-200">
                <i class="fas fa-sign-out-alt"></i>
                Keluar
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
  @endif

  {{-- Main content --}}
  <div class="flex-1 p-0 m-0">
    {{ $slot }}
  </div>

  {{-- Footer --}}
  @if (! request()->routeIs(['dashboard','marketing.dashboard','mitra.dashboard']))
    <footer class="w-full bg-orange-300 bg-opacity-70 text-white text-sm py-3 text-center">
      <div class="mx-auto max-w-screen-xl">
        <div class="sm:flex sm:items-center sm:justify-between">
          <span class="text-sm text-orange-500 sm:text-center">
            © 2025 | RSU Prima Medika
          </span>
          <div class="flex mt-0 space-x-6 sm:justify-center sm:mt-0">
            {{-- social links… --}}
          </div>
        </div>
      </div>
    </footer>
  @endif
</body>
</html>
