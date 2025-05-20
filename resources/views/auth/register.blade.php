@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-center py-12">
    <div class="w-full max-w-md p-8 bg-orange-100 rounded-lg ">
      <h2 class="text-2xl font-bold text-center text-orange-500 mb-6">
        Create Your Account
      </h2>

      @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
          {{ session('error') }}
        </div>
      @endif

      <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf

        <div>
          <label class="block text-sm font-medium text-orange-700">Full Name</label>
          <input name="name" type="text" value="{{ old('name') }}" required
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Enter your full name">
          @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Username</label>
          <input name="username" type="text" value="{{ old('username') }}" required
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Enter your username">
          @error('username') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Email</label>
          <input name="email" type="email" value="{{ old('email') }}" required
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Enter your email">
          @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Password</label>
          <input name="password" type="password" required
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Enter your password">
          @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Confirm Password</label>
          <input name="password_confirmation" type="password" required
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Confirm your password">
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Jabatan</label>
          <input name="jabatan" type="text" value="{{ old('jabatan') }}"
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Enter your position">
          @error('jabatan') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Tanggal Lahir</label>
          <input name="tgl_lahir" type="date" value="{{ old('tgl_lahir') }}"
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200">
          @error('tgl_lahir') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Tanggal Masuk</label>
          <input name="tgl_masuk" type="date" value="{{ old('tgl_masuk') }}"
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200">
          @error('tgl_masuk') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">Tempat Lahir</label>
          <input name="tempat_lahir" type="text" value="{{ old('tempat_lahir') }}"
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Enter your birthplace">
          @error('tempat_lahir') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-orange-700">No. Telepon</label>
          <input name="no_telp" type="tel" value="{{ old('no_telp') }}"
            class="mt-1 block w-full px-3 py-2 border border-orange-300 rounded focus:outline-none focus:ring focus:ring-orange-200"
            placeholder="Enter your phone number">
          @error('no_telp') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="w-full py-2 px-4 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition">
          Register
        </button>
      </form>

      <p class="mt-6 text-center text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="text-orange-500 hover:underline">Log in</a>
      </p>
    </div>
  </div>
@endsection
