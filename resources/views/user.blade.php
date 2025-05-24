<x-layout>
  <x-slot:title>Pegawai Marketing</x-slot:title>

  <div class="min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 text-gray-900 flex flex-col items-center py-12 px-4">

    <div class="w-full max-w-5xl bg-white/30 backdrop-blur-md rounded-2xl shadow-2xl p-6 space-y-6">

      {{-- Notifikasi --}}
      @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
          {{ session('success') }}
        </div>
      @elseif(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
          {{ session('error') }}
        </div>
      @endif


      {{-- Header + Add Button --}}
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h2 class="text-2xl font-bold text-orange-600">Pegawai Marketing</h2>
        <button
          onclick="openAddEmployeeForm()"
          class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2 rounded-full transition">
          Tambah Pegawai
        </button>
      </div>

      {{-- Search Bar --}}
      <form action="{{ route('user.index') }}" method="GET" class="relative">
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari Nama Pegawai..."
          class="w-full pl-12 pr-4 py-3 rounded-full bg-white bg-opacity-80 placeholder-gray-500 
                 focus:bg-opacity-100 focus:outline-none focus:ring-2 focus:ring-orange-400 transition"
        />
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <svg xmlns="http://www.w3.org/2000/svg"
               class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
          </svg>
        </div>
      </form>

      {{-- Table --}}
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white bg-opacity-60 rounded-lg overflow-hidden border border-gray-200 border-collapse">
          <thead class="bg-orange-300">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-orange-900">Nama Pegawai</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-orange-900">Posisi / Jabatan</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-orange-900">PIC</th>
              <th class="px-6 py-3 text-center text-sm font-semibold text-orange-900">Action</th>
            </tr>
          </thead>



          <tbody class="divide-y divide-gray-200 bg-white bg-opacity-60">
            @forelse($users as $user)
              <tr class="hover:bg-white/50 transition">
                <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                <td class="px-6 py-4">{{ $user->jabatan ?? '-' }}</td>
                <td class="px-6 py-4">{{ $user->posts_count ?? 0 }} Perusahaan</td>
                <td class="px-6 py-4 text-center space-x-2">
                  <a href="{{ route('user.edit', $user) }}"
                     class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white text-sm font-semibold px-3 py-1 rounded transition no-underline">
                    Info
                  </a>
                  <form action="{{ route('user.destroy', $user) }}"
                        method="POST"
                        class="inline-block"
                        onsubmit="return confirm('Yakin ingin menghapus pegawai ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-400 hover:bg-red-500 text-white text-sm font-semibold px-3 py-1 rounded transition no-underline">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-700">
                  Tidak ada data pegawai
                </td>
              </tr>
            @endforelse
          </tbody>
        
        
        
        </table>
        <div class="mt-4">
          {{ $users->withQueryString()->links() }}
        </div>
      </div>




    </div>

    {{-- Modal: Add Pegawai --}}
    <div id="add-employee-modal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 items-start justify-center pt-12 z-50 p-4 overflow-y-auto">
      <div class="bg-white rounded-lg w-full max-w-2xl mx-auto p-6 relative
                  max-h-[90vh] overflow-y-auto">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-2xl font-bold text-gray-800">Tambah Pegawai Marketing</h3>
        </div>

        {{-- Errors --}}
        @if($errors->any())
          <div class="mb-4 text-red-600">
            <ul class="list-disc list-inside space-y-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('user.store') }}" method="POST" class="space-y-6">
          @csrf

          {{-- Grid 1 kolom di mobile, 2 kolom di md --}}
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Nama</label>
              <input type="text" name="name" value="{{ old('name') }}"
                    class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Username</label>
              <input type="text" name="username" value="{{ old('username') }}"
                    class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Email</label>
              <input type="email" name="email" value="{{ old('email') }}"
                    class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password"
                      class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                      class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Jabatan</label>
              <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                    class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}"
                      class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                <input type="date" name="tgl_masuk" value="{{ old('tgl_masuk') }}"
                      class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                    class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">No Telp</label>
              <input type="text" name="no_telp" value="{{ old('no_telp') }}"
                    class="mt-1 block w-full border rounded px-3 py-2 focus:ring-orange-400 focus:border-orange-400">
            </div>
          </div>

          {{-- Buttons --}}
          <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="button"
                    onclick="closeAddEmployeeForm()"
                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
              Batal
            </button>
            <button type="submit"
                    class="px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <script>
      function openAddEmployeeForm() {
        document.getElementById('add-employee-modal').classList.remove('hidden');
      }
      function closeAddEmployeeForm() {
        document.getElementById('add-employee-modal').classList.add('hidden');
      }

      @if(session('error') || $errors->any())
        document.addEventListener('DOMContentLoaded', openAddEmployeeForm);
      @endif
    </script>

</x-layout>
