@extends('layouts.app')

@php
  // ambil domain dari email user
  $email   = auth()->user()->email;
  $domain  = strtolower(substr(strrchr($email, '@'), 1));

  // mapping ke webmail URL
  if (in_array($domain, ['gmail.com','googlemail.com'])) {
      $webmail = 'https://mail.google.com';
  } elseif (in_array($domain, ['yahoo.com','yahoo.co.id'])) {
      $webmail = 'https://mail.yahoo.com';
  } elseif (in_array($domain, ['outlook.com','hotmail.com','live.com','msn.com'])) {
      $webmail = 'https://outlook.live.com';
  } else {
      // fallback ke webmail.generic (ini bisa disesuaikan)
      $webmail = 'https://mail.' . $domain;
  }
@endphp



@section('content')
  <div class="max-w-md mx-auto mt-16 p-8 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Verifikasi Email</h1>

    @if (session('status') == 'verification-link-sent')
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        Link verifikasi baru telah dikirim ke {{ auth()->user()->email }}.
      </div>
    @endif

    <p>
      Silakan cek inbox email <strong>{{ auth()->user()->email }}</strong>
      dan klik tombol <em>Verify Email Address</em> di dalamnya.
    </p>

    <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
      @csrf
      <button type="submit"
              class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
        Kirim Ulang Link Verifikasi
      </button>
    </form>

    {{-- Tombol untuk membuka Gmail --}}
    <div class="mt-4 text-center">
    <a href="{{ $webmail }}"
        target="_blank"
        class="inline-block px-4 py-2 mt-2 border border-gray-300 rounded hover:bg-gray-100">
        Buka {{ ucfirst(explode('.', $domain)[0]) }} 
    </a>
    </div>
@endsection
