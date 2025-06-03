
<x-layout>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
    integrity="sha512-…"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />

  <x-slot:title>{{ $title }}</x-slot:title>

  <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex justify-center py-10">
    <div class="w-full max-w-4xl bg-white/30 backdrop-blur-md shadow-lg rounded-2xl p-8">
      <h1 class="text-2xl font-bold text-orange-500 mb-6">
        Form Transaksi Produk untuk “{{ $post->title }}”
      </h1>

      <form
        method="POST"
        action="{{ route('posts.transactions.store', $post) }}"
        enctype="multipart/form-data"
        class="space-y-6"
      >
        @csrf

        {{-- Nama Produk --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Nama Produk:</label>
          <input type="text" name="nama_produk"
                 class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400"
                 placeholder="Masukkan teks" required>
        </div>

        {{-- Jumlah Produk --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Jumlah Produk:</label>
          <input type="number" name="jumlah" min="1"
                 class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400"
                 placeholder="Masukkan Jumlah (Angka)"
                 oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                 required>
        </div>

        {{-- Merk Produk --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Merk Produk:</label>
          <input type="text" name="merk"
                 class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400"
                 placeholder="Masukkan teks" required>
        </div>

        {{-- Harga per Satuan --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Harga per Satuan:</label>
          <input type="number" name="harga_satuan" min="1" step="0.01"
                 class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400"
                 placeholder="Masukkan Harga (Angka)"
                 oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                 required>
        </div>

        {{-- Tipe Pembayaran --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Tipe Pembayaran:</label>
          <select name="tipe_pembayaran"
                  class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400"
                  required>
            <option value="" disabled selected>Pilih metode pembayaran</option>
            <option value="Transfer">Transfer</option>
            <option value="Cash">Cash</option>
            <option value="Kredit">Kredit</option>
          </select>
        </div>

    

        {{-- PIC Rumah Sakit --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">PIC Rumah Sakit:</label>
          <select name="pic_rs"
                  class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400 @error('pic_rs') border-red-500 @enderror"
                  required>
            <option value="">— Pilih PIC RS —</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
          </select>
          @error('pic_rs')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Approval RS --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Approval RS:</label>
          <select name="approval_rs"
                  class="w-full rounded-lg border-gray-300 focus:ring-orange-400 focus:border-orange-400"
                  required>
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
          </select>
        </div>

        {{-- PIC Mitra --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">PIC Mitra:</label>
          <input 
            type="text" 
            name="pic_mitra"
            value="{{ $post->pic_mitra }}" 
            readonly
            class="w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed @error('pic_mitra') border-red-500 @enderror"
          >
          @error('pic_mitra')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>


        {{-- Approval Mitra --}}
        <input type="hidden" name="approval_mitra" value="0">
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Approval Mitra:</label>
          <input 
            type="text"
            value="Tidak"
            readonly
            class="w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
          >
          @error('approval_mitra')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Status --}}
        <div>
          <label class="block text-orange-500 font-semibold mb-1">Status:</label>
          <input
            type="text"
            name="status"
            value="{{ old('status','Proses') }}"
            readonly
            class="w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed"
          >
          @error('status')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Submit --}}
        <div class="flex justify-end space-x-4">
          <button type="button" onclick="history.back()"
                  class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
            Batal
          </button>
          <button type="submit"
                  class="bg-orange-400 hover:bg-orange-500 text-white font-bold py-2 px-6 rounded-lg shadow transition">
            Simpan Transaksi
          </button>
        </div>
      </form>


    </div>
  </div>
</x-layout>
