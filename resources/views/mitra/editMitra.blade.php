<x-layout>
    <x-slot:title>Edit Perusahaan</x-slot:title>

    <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex justify-center items-start py-10">
            <form action="{{ route('mitra.updateMitra', $post) }}"
                method="POST"
                enctype="multipart/form-data"
                class="…">
            @csrf
            @method('PUT')

            {{-- Header --}}
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-orange-600 ml-5">Edit Perusahaan</h1>
               <div class="ml-auto flex space-x-2">
                <button type="button"
                        onclick="window.history.back()"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                  Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                  Selesai
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Informasi Perusahaan --}}
                <div class="space-y-4">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-orange-500 font-semibold">Nama Perusahaan</label>
                        <input type="text"
                               name="title"
                               value="{{ old('title', $post->title) }}"
                               class="w-full border rounded px-3 py-2 @error('title') border-red-500 @enderror"
                               required>
                        @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-orange-500 font-semibold">Kategori Perusahaan</label>
                        <select name="category_id" class="w-full border rounded px-3 py-2 @error('category_id') border-red-500 @enderror">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-orange-500 font-semibold">Deskripsi Perusahaan</label>
                        <textarea name="body" rows="4" class="w-full border rounded px-3 py-2 @error('body') border-red-500 @enderror">{{ old('body', $post->body) }}</textarea>
                        @error('body')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kontak, BPJS, Pembayaran, Periode --}}
                    <div class="bg-gray-100 p-4 rounded space-y-3">
                        <label class="block text-orange-500 font-semibold">Email</label>
                        <input type="email" name="email" value="{{ old('email', $post->email) }}" class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror">
                        @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

                        <label class="block text-orange-500 font-semibold">No Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $post->phone) }}" class="w-full border rounded px-3 py-2 @error('phone') border-red-500 @enderror">
                        @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

                        <label class="block text-orange-500 font-semibold">Alamat Perusahaan</label>
                        <input type="text" name="alamat" value="{{ old('alamat', $post->alamat) }}" class="w-full border rounded px-3 py-2 @error('alamat') border-red-500 @enderror">
                        @error('alamat')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

                        <label class="block text-orange-500 font-semibold">Keterangan BPJS</label>
                        <select name="keterangan_bpjs" class="w-full border rounded px-3 py-2 @error('keterangan_bpjs') border-red-500 @enderror">
                            <option value="yes" @selected(old('keterangan_bpjs', $post->keterangan_bpjs)=='yes')>Ya</option>
                            <option value="no"  @selected(old('keterangan_bpjs', $post->keterangan_bpjs)=='no')>Tidak</option>
                        </select>
                        @error('keterangan_bpjs')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

                        <label class="block text-orange-500 font-semibold">Pembayaran</label>
                        <select name="pembayaran" class="w-full border rounded px-3 py-2 @error('pembayaran') border-red-500 @enderror">
                            <option value="Tunai" @selected(old('pembayaran',$post->pembayaran)=='Tunai')>Tunai</option>
                            <option value="Online Payment" @selected(old('pembayaran',$post->pembayaran)=='Online Payment')>Online Payment</option>
                        </select>
                        @error('pembayaran')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

                        <label class="block text-orange-500 font-semibold">Periode Kerjasama</label>
                        <div class="flex gap-2">
                            <input type="date" name="tanggal_awal" value="{{ old('tanggal_awal',$post->tanggal_awal) }}" class="w-1/2 border rounded px-3 py-2 @error('tanggal_awal') border-red-500 @enderror">
                            <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir',$post->tanggal_akhir) }}" class="w-1/2 border rounded px-3 py-2 @error('tanggal_akhir') border-red-500 @enderror">
                        </div>
                        @error('tanggal_awal')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('tanggal_akhir')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Informasi PIC, File, Anak Perusahaan --}}
                <div class="space-y-6">

                    {{-- PIC RS & Mitra --}}
                    <div class="bg-gray-100 p-4 rounded space-y-3">
                        <h2 class="font-semibold text-orange-500">Informasi PIC Marketing RS</h2>

                        <label class="block">Pilih PIC Rumah Sakit</label>
                        <select name="picUser_id" class="w-full border rounded px-3 py-2 @error('picUser_id') border-red-500 @enderror">
                            <option value="">— Pilih User —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('picUser_id',$post->picUser_id)==$u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        @error('picUser_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

                        <label class="mt-4 block">PIC Mitra</label>
                        <input type="text" name="pic_mitra" value="{{ old('pic_mitra',$post->pic_mitra) }}" class="w-full border rounded px-3 py-2 @error('pic_mitra') border-red-500 @enderror">
                        @error('pic_mitra')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Dokumentasi Upload --}}
                    <div>
                        <label class="block text-orange-500 font-semibold mb-1">Dokumentasi</label>
                        <div class="border-2 border-dashed border-orange-300 rounded-lg p-4 text-center cursor-pointer hover:bg-orange-50 transition" onclick="document.getElementById('file-upload-input').click()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="mt-2 text-orange-600 font-medium">Klik untuk mengunggah</p>
                            <input type="file" name="file_path[]" id="file-upload-input" class="hidden" accept=".png,.jpg,.jpeg,.pdf" multiple>
                        </div>
                        <div id="selected-files" class="mt-4 space-y-3"></div>
                    </div>

                    {{-- Anak Perusahaan UI --}}
                    <div x-data="{ isChild }">
                        <label class="block text-orange-500 font-semibold">Anak Perusahaan?</label>
                        <div class="flex items-center gap-6 mt-2">
                          <label class="inline-flex items-center">
                            <input type="radio" name="is_child" value="1" @click="isChild=true" class="form-radio text-orange-500">
                            <span class="ml-2">Yes</span>
                          </label>
                          <label class="inline-flex items-center">
                            <input type="radio" name="is_child" value="0" @click="isChild=false" class="form-radio text-orange-500">
                            <span class="ml-2">No</span>
                          </label>
                        </div>
                        <div x-show="isChild" x-transition x-cloak class="mt-4 bg-gray-100 p-4 rounded space-y-3">
                          <h3 class="font-semibold text-gray-700">Induk Perusahaan</h3>
                          <label class="block text-gray-700">Pilih Induk</label>
                          <select class="w-full border rounded px-3 py-2">
                            <option>Pilih ↓</option>
                          </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // File upload JS here (omitted for brevity)
    </script>
</x-layout>
