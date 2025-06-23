<x-layout>
  <x-slot:title>{{ $title }}</x-slot:title>

  <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex justify-center py-10">
    <div class="w-full max-w-4xl bg-white/30 backdrop-blur-md shadow-lg rounded-2xl p-8">
      <h1 class="text-2xl font-bold text-orange-500 mb-6">
        Form Transaksi Produk untuk “{{ $post->title }}”
      </h1>

      {{-- ERROR DISPLAY --}}
      @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('posts.transactions.store', $post) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Jenis Transaksi --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Jenis Transaksi:</label>
          <select name="jenis_transaksi" id="jenis_transaksi"
                  class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400"
                  onchange="toggleJenis()" required>
            <option value="" disabled {{ old('jenis_transaksi') ? '' : 'selected' }}>Pilih jenis transaksi</option>
            <option value="barang" {{ old('jenis_transaksi') === 'barang' ? 'selected' : '' }}>Barang</option>
            <option value="jasa" {{ old('jenis_transaksi') === 'jasa' ? 'selected' : '' }}>Jasa</option>
          </select>
        </div>

        {{-- Barang Only --}}
        <div id="field_barang_only" class="hidden">
          <div>
            {{-- Pilih Barang --}}
            <label class="block text-orange-500 font-semibold mb-1">Pilih Barang:</label>
            <select name="master_barang_id" id="master_barang_id"
                    class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400">
              <option value="" disabled selected>-- Pilih Barang --</option>
              @foreach($barangs as $b)
                <option value="{{ $b->id }}" {{ old('master_barang_id') == $b->id ? 'selected' : '' }}>
                  {{ $b->nama_barang }}
                </option>
              @endforeach
            </select>

            {{-- Merk Produk --}}
            <div class="mt-4">
              <label class="block text-orange-500 font-semibold mb-1">Merk Produk:</label>
              <input type="text" name="merk" value="{{ old('merk') }}"
                    placeholder="Masukkan merk produk"
                    class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400">
            </div>

            {{-- Jumlah Produk --}}
            <div class="mt-4">
              <label class="block text-orange-500 font-semibold mb-1">Jumlah Produk:</label>
              <input type="number" name="jumlah" min="1" value="{{ old('jumlah') }}"
                    placeholder="Contoh: 10"
                    class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400">
            </div>

            {{-- Harga Satuan --}}
            <div class="mt-4">
              <label class="block text-orange-500 font-semibold mb-1">Harga per Satuan (Rp):</label>
              <input type="number" name="harga_satuan" step="0.01" min="0" value="{{ old('harga_satuan') }}"
                    placeholder="Contoh: 250000"
                    class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400">
            </div>
          </div>
        </div>

        {{-- Jasa Only --}}
        <div id="field_jasa_only" class="hidden">
          <div>
            <label class="block text-orange-500 font-semibold mb-1">Pilih Jasa:</label>
            <select name="master_jasa_id" id="master_jasa_id"
                    class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400">
              <option value="" disabled selected>-- Pilih Jasa --</option>
              @foreach($jasas as $j)
                <option value="{{ $j->id }}" {{ old('master_jasa_id') == $j->id ? 'selected' : '' }}>
                  {{ $j->nama_jasa }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mt-4">
            <label class="block text-orange-500 font-semibold mb-1">Durasi Jasa:</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                     value="{{ old('tanggal_mulai') }}"
                     class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400">
              <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                     value="{{ old('tanggal_selesai') }}"
                     class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400">
            </div>
          </div>
        </div>

        {{-- Tipe Pembayaran --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Tipe Pembayaran:</label>
          <select name="tipe_pembayaran" class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400" required>
            <option value="" disabled {{ old('tipe_pembayaran') ? '' : 'selected' }}>Pilih metode pembayaran</option>
            <option value="Transfer" {{ old('tipe_pembayaran') === 'Transfer' ? 'selected' : '' }}>Transfer</option>
            <option value="Cash" {{ old('tipe_pembayaran') === 'Cash' ? 'selected' : '' }}>Cash</option>
            <option value="Kredit" {{ old('tipe_pembayaran') === 'Kredit' ? 'selected' : '' }}>Kredit</option>
          </select>
        </div>

        {{-- PIC RS --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">PIC Rumah Sakit:</label>
          <select name="pic_rs" class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400" required>
            <option value="">-- Pilih PIC RS --</option>
            @foreach($users->where('role', 'marketing') as $user)
              <option value="{{ $user->id }}" {{ old('pic_rs') == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Approval RS --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Approval RS:</label>
          <select name="approval_rs" class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400" required>
            <option value="1" {{ old('approval_rs') == 1 ? 'selected' : '' }}>Ya</option>
            <option value="0" {{ old('approval_rs') == 0 ? 'selected' : '' }}>Tidak</option>
          </select>
        </div>

        {{-- PIC Mitra --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">PIC Mitra:</label>
          <input type="text" name="pic_mitra_display"
                 value="{{ $post->pic_mitra }}"
                 disabled
                 class="w-full rounded-lg border-gray-300 bg-gray-100">

          <input type="hidden" name="pic_mitra" value="{{ $post->pic_mitra }}">
        </div>

        <input type="hidden" name="approval_mitra" value="0">
        <input type="hidden" name="status" value="proses">

        {{-- Submit --}}
        <div class="flex justify-end space-x-4">
          <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
          <button type="submit" class="bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-6 rounded-lg shadow transition">Simpan Transaksi</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function toggleJenis() {
      const jenis = document.getElementById('jenis_transaksi').value;

      const barangFields = document.getElementById('field_barang_only');
      const jasaFields = document.getElementById('field_jasa_only');

      if (jenis === 'barang') {
        barangFields.classList.remove('hidden');
        jasaFields.classList.add('hidden');
      } else if (jenis === 'jasa') {
        barangFields.classList.add('hidden');
        jasaFields.classList.remove('hidden');
      } else {
        barangFields.classList.add('hidden');
        jasaFields.classList.add('hidden');
      }
    }

    window.addEventListener('DOMContentLoaded', toggleJenis);
  </script>
</x-layout>
