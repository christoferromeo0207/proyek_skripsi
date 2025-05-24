{{-- resources/views/mitra/informationMitra.blade.php --}}
<x-layout>
  <x-slot:title>Detail Kerjasama — {{ $post->title }}</x-slot:title>

  <div class="min-h-screen bg-gradient-to-br from-orange-300 to-orange-400 text-white flex flex-col">

    {{-- Back --}}
    <div class="px-6 py-4">
      <a href="{{ route('mitra.dashboard') }}"
         class="text-sm text-white/90 hover:text-white no-underline">
        ← Kembali ke Dashboard
      </a>
    </div>

    {{-- Heading --}}
    <div class="text-center py-6">
      <h1 class="text-3xl font-extrabold">{{ $post->title }}</h1>
      <p class="text-lg">Detail Kerjasama</p>
    </div>

    <div class="mx-auto w-[90%] md:w-[60%] space-y-8">

      {{-- Form Edit Terbatas --}}
      <form action="{{ route('mitra.kerjasama.update', $post) }}" method="POST"
            class="bg-white/30 backdrop-blur-md rounded-2xl p-6 shadow-lg">
        @csrf @method('PUT')

        <h2 class="text-xl font-bold text-orange-500 mb-4">Ubah Informasi Dasar</h2>
        <div class="grid grid-cols-1 gap-4">
          {{-- Deskripsi --}}
          <div>
            <label class="block text-white font-semibold mb-1">Deskripsi</label>
            <textarea name="body"
                      class="w-full rounded-md p-2 text-gray-800"
                      rows="4">{{ old('body', $post->body) }}</textarea>
          </div>
          {{-- Telepon --}}
          <div>
            <label class="block text-white font-semibold mb-1">Nomor Telepon</label>
            <input type="text" name="phone"
                   value="{{ old('phone', $post->phone) }}"
                   class="w-full rounded-md p-2 text-gray-800" />
          </div>
          {{-- Email --}}
          <div>
            <label class="block text-white font-semibold mb-1">Email</label>
            <input type="email" name="email"
                   value="{{ old('email', $post->email) }}"
                   class="w-full rounded-md p-2 text-gray-800" />
          </div>
          {{-- Lokasi --}}
          <div>
            <label class="block text-white font-semibold mb-1">Lokasi</label>
            <input type="text" name="alamat"
                   value="{{ old('alamat', $post->alamat) }}"
                   class="w-full rounded-md p-2 text-gray-800" />
          </div>
          {{-- PIC Mitra --}}
          <div>
            <label class="block text-white font-semibold mb-1">PIC Mitra</label>
            <select name="pic_mitra"
                    class="w-full rounded-md p-2 text-gray-800">
              @foreach($allMitra ?? [] as $mitraName)
                <option value="{{ $mitraName }}"
                  {{ old('pic_mitra', $post->pic_mitra) === $mitraName ? 'selected' : '' }}>
                  {{ $mitraName }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mt-6 text-right">
          <button type="submit"
                  class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-bold rounded-md">
            Simpan Perubahan
          </button>
        </div>
      </form>

      {{-- Komisi (Read-only) --}}
      <div class="bg-white/30 backdrop-blur-md rounded-2xl p-6 shadow-lg">
        <h2 class="text-xl font-bold text-orange-500 mb-4">Komisi</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-gray-800">
            <thead class="bg-orange-300 text-white">
              <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Label</th>
                <th class="px-4 py-2">Persentase</th>
                <th class="px-4 py-2">Nominal</th>
                <th class="px-4 py-2">Anak Perusahaan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($post->commissions ?? [] as $i => $com)
                <tr class="{{ $i % 2 ? 'bg-white/50' : '' }}">
                  <td class="px-4 py-2">{{ $i+1 }}</td>
                  <td class="px-4 py-2">{{ $com->label }}</td>
                  <td class="px-4 py-2">{{ $com->percentage }}%</td>
                  <td class="px-4 py-2">Rp {{ number_format($com->amount,2,',','.') }}</td>
                  <td class="px-4 py-2">{{ $com->child_company_name }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-4 py-2 text-center">Belum ada data komisi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Produk Kerjasama --}}
      <div class="bg-white/30 backdrop-blur-md rounded-2xl p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-orange-500">Produk Kerjasama</h2>
            <a href="{{ route('mitra.kerjasama.transactions.create', $post) }}"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md no-underline">
            Tambah Produk
            </a>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-gray-800">
            <thead class="bg-orange-300 text-white">
              <tr>
                <th class="px-3 py-2">Produk – Jumlah</th>
                <th class="px-3 py-2">Merk – Harga</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Approval RS</th>
                <th class="px-3 py-2">Approval Mitra</th>
                <th class="px-3 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($post->transactions as $tx)
                <tr class="hover:bg-white/50 transition">
                  <td class="px-3 py-2">{{ $tx->nama_produk }} – {{ $tx->jumlah }}</td>
                  <td class="px-3 py-2">{{ $tx->merk }} – Rp {{ number_format($tx->total_harga,2,',','.') }}</td>
                  <td class="px-3 py-2">{{ ucfirst($tx->status) }}</td>
                  <td class="px-3 py-2">{{ $tx->approval_rs ? 'Ya' : 'Tidak' }}</td>
                  <td class="px-3 py-2">{{ $tx->approval_mitra ? 'Ya' : 'Tidak' }}</td>
                  <td class="px-3 py-2 flex gap-2">
                    <a href="{{ route('mitra.kerjasama.transactions.edit', [$post, $tx]) }}"
                       class="px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs no-underline">
                      Edit
                    </a>
                    @unless($tx->approval_mitra)
                      <form action="{{ route('mitra.kerjasama.transactions.approval', [$post, $tx]) }}"
                            method="POST" onsubmit="return confirm('Approve kerjasama ini?')">
                        @csrf
                        <button type="submit"
                                class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs">
                          Approve
                        </button>
                      </form>
                    @endunless
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="px-3 py-2 text-center">Belum ada produk kerjasama.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>

    {{-- Footer --}}
    <footer class="mt-auto py-4 text-center text-sm text-white/80">
      © 2025 | RSU Prima Medika
    </footer>

  </div>
</x-layout>
