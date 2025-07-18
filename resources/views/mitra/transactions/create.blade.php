<x-layout>
  <x-slot:title>Transaksi Baru – {{ $post->title }}</x-slot:title>

  <div class="container mx-auto py-10">
    <div class="max-w-2xl mx-auto bg-orange-200 p-8 rounded-xl shadow-lg">
      <h1 class="text-2xl font-bold text-orange-500 mb-6">
        Form Transaksi “{{ $post->title }}”
      </h1>

      <form method="POST"
            action="{{ route('mitra.informasi.transactions.store', $post) }}"
            class="space-y-6">
        @csrf

        {{-- Nama Produk --}}
        <div>
          <label class="block text-orange-500 font-medium">Nama Produk</label>
          <input name="nama_produk" value="{{ old('nama_produk') }}"
                 class="w-full border rounded p-2" required>
        </div>

        {{-- Jumlah --}}
        <div>
          <label class="block text-orange-500 font-medium">Jumlah</label>
          <input type="number" name="jumlah" value="{{ old('jumlah') }}"
                 min="1" class="w-full border rounded p-2" required>
        </div>

        {{-- Merk --}}
        <div>
          <label class="block text-orange-500 font-medium">Merk</label>
          <input name="merk" value="{{ old('merk') }}"
                 class="w-full border rounded p-2" required>
        </div>

        {{-- Harga per Satuan --}}
        <div>
          <label class="block text-orange-500 font-medium">Harga per Satuan</label>
          <input type="number" name="harga_satuan" value="{{ old('harga_satuan') }}"
                 step="0.01" min="0" class="w-full border rounded p-2" required>
        </div>

        {{-- Tipe Pembayaran --}}
        <div>
          <label class="block text-orange-500 font-medium">Tipe Pembayaran</label>
          <select name="tipe_pembayaran" class="w-full border rounded p-2" required>
            <option value="" disabled {{ old('tipe_pembayaran')?'':'selected' }}>
              Pilih metode
            </option>
            <option value="Transfer" {{ old('tipe_pembayaran')=='Transfer'?'selected':'' }}>Transfer</option>
            <option value="Cash"     {{ old('tipe_pembayaran')=='Cash'    ?'selected':'' }}>Cash</option>
            <option value="Kredit"   {{ old('tipe_pembayaran')=='Kredit'  ?'selected':'' }}>Kredit</option>
          </select>
        </div>

        {{-- PIC Mitra --}}
        <div>
          <label class="block text-orange-500 font-medium">PIC Mitra</label>
          <input name="pic_mitra"
                 value="{{ Auth::user()->name }}"
                 class="w-full border bg-gray-100 rounded p-2" readonly>
        </div>

        {{-- Disabled Fields --}}
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-500">Bukti Pembayaran</label>
            <input type="text" disabled value="No file choosen" class="w-full border bg-gray-100 rounded p-2">
          </div>

          {{-- PIC RS --}}
          <div>
            <label class="block text-orange-500 font-medium mb-1">PIC Marketing</label>
            <select name="pic_rs"
                    class="w-full border rounded p-2 focus:ring-orange-400 focus:border-orange-400"
                    required>
              <option value="" disabled {{ old('pic_rs') ? '' : 'selected' }}>
                — Pilih PIC Marketing —
              </option>
              @foreach($marketingUsers as $user)
                <option value="{{ $user->id }}"
                        {{ old('pic_rs') == $user->id ? 'selected' : '' }}>
                  {{ $user->name }}
                </option>
              @endforeach
            </select>
            @error('pic_rs')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Approval RS (otomatis “Tidak”) --}}
          <input type="hidden" name="approval_rs" value="0">

          {{-- Approval Mitra --}}
          <div>
            <label class="block text-orange-500 font-medium mb-1">Approval Mitra</label>
            <select name="approval_mitra"
                    class="w-full border rounded p-2 focus:ring-orange-400 focus:border-orange-400"
                    required>
              <option value="0" {{ old('approval_mitra') === '0' ? 'selected' : '' }}>Tidak</option>
              <option value="1" {{ old('approval_mitra') === '1' ? 'selected' : '' }}>Ya</option>
            </select>
            @error('approval_mitra')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Status --}}
          <div>
            <label class="block text-gray-500">Status</label>
            <input disabled value="Proses"
                  class="w-full border bg-gray-100 rounded p-2">
          </div>

        </div>

        {{-- Submit --}}
        <div class="flex justify-end space-x-4">
          <button type="button" onclick="history.back()"
                  class="px-4 py-2 bg-gray-300 rounded">
            Batal
          </button>
          <button type="submit"
                  class="px-6 py-2 bg-orange-500 text-white rounded">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</x-layout>
