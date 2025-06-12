<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <!-- Make sure Tailwind CSS is loading properly -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <!-- Add a small style section to ensure our colors work -->
  <style>
    .bg-orange-gradient {
      background: linear-gradient(135deg, #ffedd5 0%, #fb923c 100%);
    }
    .btn-orange {
      background-color: #f97316;
      color: white;
    }
    .btn-orange:hover {
      background-color: #ea580c;
    }
    .text-orange {
      color: #f97316;
    }
    .ring-orange {
      --tw-ring-color: rgba(249, 115, 22, 0.5);
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-orange-gradient">
  <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold text-center text-orange">Masuk ke Akun Anda</h2>
    {{-- Session error --}}
    @if(session('error'))
      <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
        {{ session('error') }}
      </div>
    @endif
    <form action="{{ route('login') }}" method="POST" class="space-y-5">
      @csrf
      {{-- username --}}
      <div>
        <label for="username" class="block text-sm font-semibold text-orange">Username</label>
        <input
          id="username"
          name="username"
          type="username"
          value="{{ old('username') }}"
          required
          autofocus
          class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-2 ring-orange @error('username') border-red-500 @enderror"
          placeholder="username"
        >
        @error('username')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>
      {{-- Password --}}
      <div>
        <label for="password" class="block text-sm font-semibold text-orange">Password</label>
        <input
          id="password"
          name="password"
          type="password"
          required
          class="w-full px-4 py-2 mt-1 border rounded-md focus:outline-none focus:ring-2 ring-orange @error('password') border-red-500 @enderror"
          placeholder="••••••••"
        >
        @error('password')
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>
      
      <button type="submit"
              class="w-full px-4 py-2 font-semibold btn-orange rounded-md focus:outline-none focus:ring-2 ring-orange transition-colors duration-200">
        Login
      </button>
    </form>
    <p class="text-center text-sm text-gray-600 space-y-2">
      Belum punya akun?
      <div class="flex justify-center gap-4">
        <a href="{{ route('register') }}"
          class="text-orange hover:underline">
          Daftar Marketing
        </a>
        <span class="text-gray-400">|</span>
        <a href="{{ route('register.mitra') }}"
          class="text-orange hover:underline">
          Daftar PIC Mitra
        </a>
      </div>
    </p>

  </div>
</body>
</html>