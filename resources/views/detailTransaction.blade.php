<x-layout>
  <x-slot:title>Detail &amp; Edit Transaksi – {{ $transaction->nama_produk }}</x-slot:title>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
    @endif

  @php
    $raw = $transaction->getRawOriginal('bukti_pembayaran');
    if (is_string($raw)) {
        $files = json_decode($raw, true) ?: [];
    } elseif (is_array($raw)) {
        $files = $raw;
    } else {
        $files = [];
    }
  @endphp


  <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400
              flex justify-center items-start py-10">
    <form
      action="{{ route('posts.transactions.update', [$post, $transaction]) }}"
      method="POST"
      enctype="multipart/form-data"
      class="w-11/12 md:w-3/4 bg-white p-8 rounded-2xl shadow-lg"
    >
      @csrf
      @method('PUT')

      {{-- Header --}}
      <div class="flex justify-between items-center pb-4">
        <h1 class="text-2xl font-bold text-orange-600">Detail &amp; Edit Transaksi</h1>
        <div class="flex space-x-2">
          <button type="button"
                  onclick="history.back()"
                  class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
            Batal
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
            Simpan
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kolom Kiri --}}
        <div class="space-y-4">
          {{-- Nama Produk --}}
          <div>
            <label class="block text-orange-500 font-semibold">Nama Produk</label>
            <input type="text"
                   name="nama_produk"
                   value="{{ old('nama_produk', $transaction->nama_produk) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>
          {{-- Jumlah --}}
          <div>
            <label class="block text-orange-500 font-semibold">Jumlah</label>
            <input type="number"
                   name="jumlah"
                   min="1"
                   value="{{ old('jumlah', $transaction->jumlah) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>
          {{-- Merk --}}
          <div>
            <label class="block text-orange-500 font-semibold">Merk Produk</label>
            <input type="text"
                   name="merk"
                   value="{{ old('merk', $transaction->merk) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>
          {{-- Harga per Satuan --}}
          <div>
            <label class="block text-orange-500 font-semibold">Harga per Satuan</label>
            <input type="number"
                   name="harga_satuan"
                   min="1" step="0.01"
                   value="{{ old('harga_satuan', $transaction->harga_satuan) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>
          {{-- Tipe Pembayaran --}}
          <div>
            <label class="block text-orange-500 font-semibold">Tipe Pembayaran</label>
            <select name="tipe_pembayaran"
                    class="w-full border rounded px-3 py-2"
                    required>
              <option disabled value="">Pilih metode</option>
              @foreach(['Transfer','Cash','Kredit'] as $m)
                <option value="{{ $m }}"
                        {{ old('tipe_pembayaran', $transaction->tipe_pembayaran) == $m ? 'selected' : '' }}>
                  {{ $m }}
                </option>
              @endforeach
            </select>
          </div>
          {{-- Status --}}
          <div>
            <label class="block text-orange-500 font-semibold">Status</label>
            <select name="status"
                    class="w-full border rounded px-3 py-2"
                    required>
              @foreach(['Proses','Selesai','Dibatalkan'] as $st)
                <option value="{{ $st }}"
                        {{ old('status', $transaction->status) == $st ? 'selected' : '' }}>
                  {{ $st }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Kolom Kanan --}}
        <div class="space-y-4">
          {{-- PIC RS --}}
          <div>
            <label class="block text-orange-500 font-semibold">PIC Rumah Sakit</label>
            <select name="pic_rs"
                    class="w-full border rounded px-3 py-2"
                    required>
              <option value="">— Pilih PIC RS —</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}"
                        {{ old('pic_rs', $transaction->pic_rs) == $user->id ? 'selected' : '' }}>
                  {{ $user->name }}
                </option>
              @endforeach
            </select>
          </div>
          {{-- Approval RS --}}
          <div>
            <label class="block text-orange-500 font-semibold">Approval RS</label>
            <select name="approval_rs"
                    class="w-full border rounded px-3 py-2"
                    required>
              <option value="1" {{ old('approval_rs', $transaction->approval_rs) == 1 ? 'selected' : '' }}>Ya</option>
              <option value="0" {{ old('approval_rs', $transaction->approval_rs) == 0 ? 'selected' : '' }}>Tidak</option>
            </select>
          </div>
          {{-- PIC Mitra --}}
          <div>
            <label class="block text-orange-500 font-semibold">PIC Mitra</label>
            <input type="text"
                   name="pic_mitra"
                   value="{{ old('pic_mitra', $transaction->pic_mitra) }}"
                   class="w-full border rounded px-3 py-2"
                   required>
          </div>
          {{-- Approval Mitra --}}
          <div>
            <label class="block text-orange-500 font-semibold">Approval Mitra</label>
            <select name="approval_mitra"
                    class="w-full border rounded px-3 py-2"
                    required>
              <option value="1" {{ old('approval_mitra', $transaction->approval_mitra) == 1 ? 'selected' : '' }}>Ya</option>
              <option value="0" {{ old('approval_mitra', $transaction->approval_mitra) == 0 ? 'selected' : '' }}>Tidak</option>
            </select>
          </div>
        </div>

        {{-- Full-width File Section --}}
        <div class="col-span-full space-y-4">
          <label class="block text-orange-500 font-semibold mb-1">Bukti Transaksi Saat Ini</label>

          @php
            // Decode JSON or use array
            $raw = $transaction->bukti_pembayaran;
            $files = is_array($raw) ? $raw : [];
          @endphp



        {{-- di bagian atas view, sebelum mulai pengecekan --}}
        @php
          // Ambil raw JSON (tanpa cast) atau value hasil cast
          $raw = $transaction->getRawOriginal('bukti_pembayaran') 
              ?? $transaction->bukti_pembayaran;

          // Pastikan jadi array string
          if (is_string($raw)) {
              $files = json_decode($raw, true) ?: [];
          } elseif (is_array($raw)) {
              $files = $raw;
          } else {
              $files = [];
          }
        @endphp

        {{-- … lalu di tempat list file --}}
        @if(count($files))
          <ul class="space-y-2">
            @foreach($files as $filePath)
              @if(is_string($filePath))
                @php
                  // generate URL publik
                  $url = Storage::url($filePath);
                  // ambil nama file saja
                  $filename = basename($filePath);
                @endphp

                <li class="flex justify-between items-center bg-gray-100 p-2 rounded">
                  <a href="{{ $url }}" target="_blank" class="underline">
                    {{ $filename }}
                  </a>

                  <form action="{{ route('posts.transactions.file.destroy', [$post, $transaction, $filename]) }}"
                        method="POST"
                        onsubmit="return confirm('Hapus {{ $filename }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600">×</button>
                  </form>

                </li>
              @endif
            @endforeach
          </ul>
        @else
          <p class="text-gray-500">Belum ada upload.</p>
        @endif



          {{-- @if(!empty($files))
            <ul class="space-y-2">
              @foreach($files as $filePath)

            @php($url = Storage::url($filePath))
                <li class="flex justify-between items-center bg-gray-100 p-2 rounded">
                  <a href="{{ $url }}" target="_blank" class="underline">
                    {{ basename($filePath) }}
                  </a>
                  <form action="{{ route('transactions.file.destroy', [$post, $transaction, basename($filePath)]) }}"
                        method="POST"
                        onsubmit="return confirm('Hapus {{ basename($filePath) }}?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600">×</button>
                  </form>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-gray-500">Belum ada upload.</p>
          @endif --}}




          <label class="block text-orange-500 font-semibold mt-4 mb-1">Tambah Bukti Transaksi</label>
          <div id="file-upload-area"
               class="border-2 border-dashed border-orange-300 rounded-lg p-4 text-center cursor-pointer hover:bg-orange-50">
            Klik untuk pilih file…
            <input type="file"
                   name="bukti_pembayaran[]"
                   id="file-upload-input"
                   class="hidden"
                   accept=".png,.jpg,.jpeg,.pdf"
                   multiple>
          </div>
          <div id="selected-files" class="mt-2 space-y-2"></div>
        </div>


      </div>
    </form>
  </div>

  {{-- JS Preview --}}
  <script>
    const area  = document.getElementById('file-upload-area'),
          input = document.getElementById('file-upload-input'),
          list  = document.getElementById('selected-files');

    area.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
      list.innerHTML = '';
      Array.from(input.files).forEach(f => {
        const row = document.createElement('div');
        row.textContent = f.name;
        row.className = 'px-2 py-1 bg-white rounded border';
        list.append(row);
      });
    });
  </script>
</x-layout>
