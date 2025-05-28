<x-layout>
  <x-slot:title>Pengajuan Mitra Baru</x-slot:title>

  <div class="container mx-auto py-10">
    <div class="max-w-2xl mx-auto bg-orange-200 p-8 rounded-xl shadow-lg">
      <h1 class="text-2xl font-bold text-orange-500 mb-6">
        Form Pengajuan Mitra Baru
      </h1>

      <form method="POST" action="{{ route('mitra.store') }}" class="space-y-6">
        @csrf

        {{-- Judul Perusahaan --}}
        <div>
          <label class="block text-orange-600 font-semibold">Nama Perusahaan</label>
          <input name="title"
                 value="{{ old('title') }}"
                 class="w-full border rounded p-2"
                 required>
          @error('title')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Kategori --}}
        <div>
          <label class="block text-orange-600 font-semibold">Kategori</label>
          <select name="category_id" class="w-full border rounded p-2" required>
            <option value="">— Pilih Kategori —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}"
                      {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
          @error('category_id')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div>
          <label class="block text-orange-600 font-semibold">Deskripsi</label>
          <textarea name="body"
                    rows="4"
                    class="w-full border rounded p-2"
                    required>{{ old('body') }}</textarea>
          @error('body')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Kontak --}}
        <div>
          <label class="block text-orange-600 font-semibold">Telepon</label>
          <input name="phone" value="{{ old('phone') }}"
                 class="w-full border rounded p-2">
          @error('phone')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-orange-600 font-semibold">Email</label>
          <input name="email" value="{{ old('email') }}"
                 class="w-full border rounded p-2">
          @error('email')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-orange-600 font-semibold">Alamat</label>
          <input name="alamat" value="{{ old('alamat') }}"
                 class="w-full border rounded p-2">
          @error('alamat')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- BPJS --}}
        <div>
          <label class="block text-orange-600 font-semibold">Keterangan BPJS</label>
          <select name="keterangan_bpjs" class="w-full border rounded p-2" required>
            <option value="yes" {{ old('keterangan_bpjs')=='yes'?'selected':'' }}>Yes</option>
            <option value="no"  {{ old('keterangan_bpjs')=='no' ?'selected':'' }}>No</option>
          </select>
          @error('keterangan_bpjs')<p class="text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Metode Pembayaran --}}
        <div>
          <label class="block text-orange-600 font-semibold mb-1">Metode Pembayaran</label>
          <select name="pembayaran"
                  class="w-full border rounded p-2 focus:ring-orange-400 focus:border-orange-400"
                  required>
            <option value="" disabled {{ old('pembayaran') ? '' : 'selected' }}>
              — Pilih Metode Pembayaran —
            </option>
            <option value="ditagihkan ke perusahaan"
                    {{ old('pembayaran') == 'ditagihkan ke perusahaan' ? 'selected' : '' }}>
              Ditagihkan ke Perusahaan
            </option>
            <option value="tunai"
                    {{ old('pembayaran') == 'tunai' ? 'selected' : '' }}>
              Tunai
            </option>
            <option value="online payment"
            {{ old('pembayaran') == 'online payment' ? 'selected' : '' }}>
            Online Payment
          </option>
        </select>
        @error('pembayaran')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>
      
      {{-- PIC RS --}}
      <div>
        <label for="PIC" class="block text-orange-600 font-semibold mb-1">
          PIC Marketing
        </label>
        <select name="PIC"
                id="PIC"
                class="w-full border rounded p-2 @error('PIC') border-red-500 @enderror"
                required>
          <option value="">— Pilih PIC Marketing —</option>
          @foreach($marketingUsers as $u)
            <option value="{{ $u->id }}"
              {{ old('PIC') == $u->id ? 'selected' : '' }}>
              {{ $u->name }}
            </option>
          @endforeach
        </select>
        @error('PIC')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

        {{-- Tanggal --}}
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-orange-600 font-semibold">Tanggal Mulai</label>
            <input type="date" name="tanggal_awal"
                   value="{{ old('tanggal_awal') }}"
                   class="w-full border rounded p-2" required>
            @error('tanggal_awal')<p class="text-red-600">{{ $message }}</p>@enderror
          </div>
          <div>
            <label class="block text-orange-600 font-semibold">Tanggal Akhir</label>
            <input type="date" name="tanggal_akhir"
                   value="{{ old('tanggal_akhir') }}"
                   class="w-full border rounded p-2" required>
            @error('tanggal_akhir')<p class="text-red-600">{{ $message }}</p>@enderror
          </div>
        </div>


        <div class="text-right">
          <button type="button"
                  onclick="window.history.back()"
                  class="px-6 py-2 bg-gray-400 text-white rounded-lg">
            Batal
          </button>
          <button type="submit"
                  class="px-6 py-2 bg-orange-500 text-white rounded-lg">
            Kirim Pengajuan
          </button>
        </div>
      </form>
    </div>
  </div>
</x-layout>
