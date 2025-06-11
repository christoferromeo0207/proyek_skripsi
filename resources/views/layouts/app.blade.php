<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Tailwind (via CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="dns-prefetch" href="//fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
</head>
<body>
  <div id="app">


    @unless(request()->routeIs('register') ||  request()->routeIs('register.mitra'))
      <nav class="bg-white shadow">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
          <a href="{{ url('/') }}"
             class="text-orange-500 font-bold text-lg">
            {{ config('app.name', 'Laravel') }}
          </a>
          <ul class="flex space-x-4">
            @guest
              @if(Route::has('login'))
                <li><a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a></li>
              @endif
              @if(Route::has('register'))
                <li><a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">Register</a></li>
              @endif
            @else
              <li class="relative">
                <button id="userMenuButton" class="text-gray-700 hover:text-gray-900">
                  {{ Auth::user()->name }}
                </button>
                <ul class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg">
                  <li>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                      Logout
                    </a>
                  </li>
                </ul>
              </li>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
              </form>
            @endguest
          </ul>
        </div>
      </nav>
    @endunless

    <main class="py-6">
      @yield('content')
    </main>
  </div>
</body>
</html>
