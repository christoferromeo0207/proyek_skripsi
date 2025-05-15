<x-layout>
  <x-slot:title>Edit Pegawai</x-slot:title>

  <div class="min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex items-center justify-center p-6">
    <div class="w-full max-w-3xl bg-white/80 backdrop-blur-md rounded-2xl shadow-lg p-8 space-y-6">
      
      {{-- Header --}}
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-orange-600">Edit Pegawai Marketing</h1>
      </div>

      {{-- Validation Errors --}}
      @if($errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded">
        <ul class="list-disc list-inside space-y-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Edit Form --}}
      <form action="{{ route('user.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Name --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $user->name) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('name') border-red-500 @enderror">
          </div>

          {{-- Username --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text"
                   name="username"
                   value="{{ old('username', $user->username) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('username') border-red-500 @enderror">
          </div>

          {{-- Email --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email"
                   name="email"
                   value="{{ old('email', $user->email) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('email') border-red-500 @enderror">
          </div>

          {{-- Password (kosongkan jika tidak ingin ganti) --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Password <small>(Opsional)</small></label>
            <input type="password"
                   name="password"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('password') border-red-500 @enderror">
          </div>

          {{-- Confirm Password --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password<small> (Opsional)</small></label>
            <input type="password"
                   name="password_confirmation"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
          </div>

          {{-- Jabatan --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Jabatan</label>
            <input type="text"
                   name="jabatan"
                   value="{{ old('jabatan', $user->jabatan) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('jabatan') border-red-500 @enderror">
          </div>

          {{-- Tanggal Lahir --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
            <input type="date"
                   name="tgl_lahir"
                   value="{{ old('tgl_lahir', $user->tgl_lahir) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('tgl_lahir') border-red-500 @enderror">
          </div>

          {{-- Tanggal Masuk --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
            <input type="date"
                   name="tgl_masuk"
                   value="{{ old('tgl_masuk', $user->tgl_masuk) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('tgl_masuk') border-red-500 @enderror">
          </div>

          {{-- Tempat Lahir --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
            <input type="text"
                   name="tempat_lahir"
                   value="{{ old('tempat_lahir', $user->tempat_lahir) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('tempat_lahir') border-red-500 @enderror">
          </div>

          {{-- No Telp --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">No Telp</label>
            <input type="text"
                   name="no_telp"
                   value="{{ old('no_telp', $user->no_telp) }}"
                   class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400 @error('no_telp') border-red-500 @enderror">
          </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end pt-4 border-t">
          <a href="{{ route('user.index') }}"
            class="px-4 py-2 text-gray-600 bg-gray-300 rounded hover:bg-gray-400 no-underline mr-4">
            Batal
          </a>
          <button type="submit"
                  class="px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
            Simpan Perubahan
          </button>
        </div>
      </form>
    </div>
  </div>
</x-layout>
