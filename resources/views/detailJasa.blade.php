<x-layout>
  <x-slot:title>Detail & Transaksi Barang – {{ $transaction->nama_produk }}</x-slot:title>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      {{ session('error') }}
    </div>
  @endif

  <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 py-10 px-4">
    <div class="max-w-screen-xl mx-auto">
      <form
        action="{{ route('posts.transactions.update', [$post, $transaction]) }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white p-8 rounded-2xl shadow-lg"
      >
        @csrf
        @method('PUT')

        <h1 class="text-2xl font-bold text-orange-600 mb-6">Detail & Transaksi Jasa</h1>

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

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
          {{-- KIRI --}}
          <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Nama Produk --}}
            <div>
              <label class="block text-orange-500 font-semibold">Nama Produk</label>
              <input type="text" name="nama_produk" value="{{ old('nama_produk', $transaction->nama_produk) }}" class="w-full border rounded px-3 py-2" required>
            </div>

            {{-- Harga Satuan (Hidden) --}}
            <input type="hidden" name="harga_satuan" value="{{ old('harga_satuan', $transaction->harga_satuan) }}">

            {{-- Jumlah (Hidden) --}}
            <input type="hidden" name="jumlah" value="{{ old('jumlah', $transaction->jumlah) }}">

            {{-- Tanggal Mulai --}}
            <div>
              <label class="block text-orange-500 font-semibold">Tanggal Mulai</label>
              <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $transaction->tanggal_mulai) }}" class="w-full border rounded px-3 py-2">
            </div>

            {{-- Tanggal Selesai --}}
            <div>
              <label class="block text-orange-500 font-semibold">Tanggal Selesai</label>
              <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $transaction->tanggal_selesai) }}" class="w-full border rounded px-3 py-2">
            </div>

            {{-- Total Harga (readonly) --}}
            <div>
              <label class="block text-orange-500 font-semibold">Total Harga</label>
              <input type="number" name="total_harga" step="0.01" value="{{ old('total_harga', $transaction->total_harga) }}" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
            </div>

            {{-- Tipe Pembayaran --}}
            <div>
              <label class="block text-orange-500 font-semibold">Tipe Pembayaran</label>
              <select name="tipe_pembayaran" class="w-full border rounded px-3 py-2">
                <option value="Transfer" {{ old('tipe_pembayaran', $transaction->tipe_pembayaran) == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="Cash" {{ old('tipe_pembayaran', $transaction->tipe_pembayaran) == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="Kredit" {{ old('tipe_pembayaran', $transaction->tipe_pembayaran) == 'Kredit' ? 'selected' : '' }}>Kredit</option>
              </select>
            </div>

            {{-- Status --}}
            <div>
              <label class="block text-orange-500 font-semibold">Status (otomatis)</label>
              <input type="text" value="{{ ucfirst($transaction->status) }}" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-600" disabled>
            </div>

            {{-- PIC RS --}}
            <div>
              <label class="block text-orange-500 font-semibold">PIC Rumah Sakit</label>
              <select name="pic_rs" class="w-full border rounded px-3 py-2">
                @foreach($users->where('role', 'marketing') as $user)
                  <option value="{{ $user->id }}" {{ old('pic_rs', $transaction->pic_rs) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
              </select>
            </div>

            {{-- Approval RS --}}
            <div>
              <label class="block text-orange-500 font-semibold">Approval RS</label>
              <select name="approval_rs" class="w-full border rounded px-3 py-2">
                <option value="1" {{ old('approval_rs', $transaction->approval_rs) == 1 ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ old('approval_rs', $transaction->approval_rs) == 0 ? 'selected' : '' }}>Tidak</option>
              </select>
            </div>

            {{-- PIC Mitra --}}
            <div>
              <label class="block text-orange-500 font-semibold">PIC Mitra</label>
              <input type="text" value="{{ $transaction->pic_mitra }}" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-600" disabled>
            </div>

            {{-- Approval Mitra --}}
            <div>
              <label class="block text-orange-500 font-semibold">Approval Mitra</label>
              <input type="text" value="{{ $transaction->approval_mitra ? 'Ya' : 'Tidak' }}" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-600" disabled>
            </div>
          </div> {{-- END kiri --}}

          {{-- KANAN --}}
          <div class="md:col-span-4">
            <label class="block text-orange-500 font-semibold mb-1">Bukti Pembayaran</label>
            <div class="border border-gray-300 bg-gray-50 rounded-lg p-4 min-h-[350px] flex flex-col items-center justify-center text-center">
              @if ($transaction->bukti_pembayaran)
                @foreach ((array)$transaction->bukti_pembayaran as $file)
                  <a href="{{ asset('storage/' . $file) }}" target="_blank" class="block mb-3">
                    <img src="{{ asset('storage/' . $file) }}"
                         alt="Bukti Pembayaran"
                         class="max-h-48 object-contain shadow-md mx-auto">
                  </a>
                @endforeach
              @else
                <p class="text-gray-500 italic">Belum ada bukti pembayaran.</p>
              @endif
            </div>
          </div>
        </div> {{-- END wrapper grid --}}

        {{-- Buttons --}}
        <div class="flex justify-end mt-6 gap-4">
          <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Kembali</button>
          <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- JS Hitung Otomatis Total Harga --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const harga = document.querySelector('input[name="harga_satuan"]');
      const jumlah = document.querySelector('input[name="jumlah"]');
      const total = document.querySelector('input[name="total_harga"]');

      function updateTotal() {
        const h = parseFloat(harga.value) || 0;
        const j = parseInt(jumlah.value) || 1;
        total.value = (h * j).toFixed(2);
      }

      if (harga && jumlah && total) {
        harga.addEventListener('input', updateTotal);
        jumlah.addEventListener('input', updateTotal);
        updateTotal();
      }
    });
  </script>
</x-layout>
