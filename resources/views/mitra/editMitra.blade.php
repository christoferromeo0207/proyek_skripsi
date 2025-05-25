<x-layout>
  <x-slot:title>Edit Perusahaan</x-slot:title>

  @php
    $role = auth()->user()->role;
    $isMitra = $role === 'mitra';
  @endphp

  <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex justify-center items-start py-10">
    <form action="{{ route('mitra.updateMitra', $post) }}"
          method="POST"
          enctype="multipart/form-data"
          class="w-11/12 md:w-3/4 bg-white p-8 rounded-2xl shadow-lg space-y-6"
          x-data="{ isChild: @json(old('is_child', $post->is_child) === 1) }">
      @csrf
      @method('PUT')

      {{-- Header --}}
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-orange-600">Edit Perusahaan</h1>
        <div class="flex space-x-2">
          <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
          <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">Selesai</button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Kolom Kiri --}}
        <div class="space-y-4">

          {{-- Nama Perusahaan --}}
          <div>
            <label class="block text-orange-500 font-semibold">Nama Perusahaan</label>
            @if($isMitra)
              <input type="hidden" name="title" value="{{ $post->title }}">
              <div class="p-2 bg-gray-100 rounded">{{ $post->title }}</div>
            @else
              <input type="text" name="title" value="{{ old('title', $post->title) }}" class="w-full border rounded px-3 py-2 @error('title') border-red-500 @enderror" required>
            @endif
            @error('title')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Kategori --}}
          <div>
            <label class="block text-orange-500 font-semibold">Kategori Perusahaan</label>
            @if($isMitra)
              <input type="hidden" name="category_id" value="{{ $post->category_id }}">
              <div class="p-2 bg-gray-100 rounded">{{ $post->category->name }}</div>
            @else
              <select name="category_id" class="w-full border rounded px-3 py-2 @error('category_id') border-red-500 @enderror" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id)==$cat->id)>{{ $cat->name }}</option>
                @endforeach
              </select>
            @endif
            @error('category_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Deskripsi (editable by mitra) --}}
          <div>
            <label class="block text-orange-500 font-semibold">Deskripsi Perusahaan</label>
            <textarea name="body" rows="4" class="w-full border rounded px-3 py-2 @error('body') border-red-500 @enderror" required>{{ old('body', $post->body) }}</textarea>
            @error('body')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Kontak, Email, Telepon, Alamat (all editable) --}}
          <div class="bg-gray-100 p-4 rounded space-y-3">
            <div>
              <label class="block text-orange-500 font-semibold">Email</label>
              <input type="email" name="email" value="{{ old('email', $post->email) }}" class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror">
              @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-orange-500 font-semibold">No Telepon</label>
              <input type="text" name="phone" value="{{ old('phone', $post->phone) }}" class="w-full border rounded px-3 py-2 @error('phone') border-red-500 @enderror">
              @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-orange-500 font-semibold">Alamat</label>
              <input type="text" name="alamat" value="{{ old('alamat', $post->alamat) }}" class="w-full border rounded px-3 py-2 @error('alamat') border-red-500 @enderror">
              @error('alamat')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

          {{-- BPJS, Pembayaran, Periode (read-only for mitra) --}}
          <div class="bg-gray-50 p-4 rounded space-y-3">
            <div>
              <label class="block text-orange-500 font-semibold">Keterangan BPJS</label>
              @if($isMitra)
                <input type="hidden" name="keterangan_bpjs" value="{{ $post->keterangan_bpjs }}">
                <div class="p-2 bg-gray-100 rounded capitalize">{{ $post->keterangan_bpjs }}</div>
              @else
                <select name="keterangan_bpjs" class="w-full border rounded px-3 py-2 @error('keterangan_bpjs') border-red-500 @enderror">
                  <option value="yes" @selected(old('keterangan_bpjs', $post->keterangan_bpjs)=='yes')>Yes</option>
                  <option value="no"  @selected(old('keterangan_bpjs', $post->keterangan_bpjs)=='no')>No</option>
                </select>
              @endif
              @error('keterangan_bpjs')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-orange-500 font-semibold">Pembayaran</label>
              @if($isMitra)
                <input type="hidden" name="pembayaran" value="{{ $post->pembayaran }}">
                <div class="p-2 bg-gray-100 rounded">{{ $post->pembayaran }}</div>
              @else
                <select name="pembayaran" class="w-full border rounded px-3 py-2 @error('pembayaran') border-red-500 @enderror">
                  <option value="Tunai" @selected(old('pembayaran', $post->pembayaran)=='Tunai')>Tunai</option>
                  <option value="Online Payment" @selected(old('pembayaran', $post->pembayaran)=='Online Payment')>Online Payment</option>
                </select>
              @endif
              @error('pembayaran')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
              <label class="block text-orange-500 font-semibold">Periode Kerjasama</label>
              @if($isMitra)
                <input type="hidden" name="tanggal_awal" value="{{ $post->tanggal_awal }}">
                <input type="hidden" name="tanggal_akhir" value="{{ $post->tanggal_akhir }}">
                <div class="p-2 bg-gray-100 rounded">{{ $post->tanggal_awal }} – {{ $post->tanggal_akhir }}</div>
              @else
                <div class="flex gap-2">
                  <input type="date" name="tanggal_awal" value="{{ old('tanggal_awal', $post->tanggal_awal) }}" class="flex-1 border rounded px-3 py-2 @error('tanggal_awal') border-red-500 @enderror">
                  <input type="date" name="tanggal_akhir" value="{{ old('tanggal_akhir', $post->tanggal_akhir) }}" class="flex-1 border rounded px-3 py-2 @error('tanggal_akhir') border-red-500 @enderror">
                </div>
              @endif
              @error('tanggal_awal')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
              @error('tanggal_akhir')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

        </div>

        {{-- Kolom Kanan --}}
        <div class="space-y-6">

          {{-- PIC RS (read-only for mitra) & PIC Mitra (editable) --}}
          <div class="bg-gray-100 p-4 rounded space-y-3">
            <h2 class="font-semibold text-orange-500">Informasi PIC Marketing RS & Mitra</h2>

            <div>
              <label class="block mb-1">PIC RS</label>
              @if($isMitra)
                <input type="hidden" name="picUser_id" value="{{ $post->picUser_id }}">
                <div class="p-2 bg-gray-100 rounded">{{ $post->picUser?->name ?? '-' }}</div>
              @else
                <select name="picUser_id" class="w-full border rounded px-3 py-2 @error('picUser_id') border-red-500 @enderror">
                  <option value="">— Pilih User —</option>
                  @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(old('picUser_id', $post->picUser_id)==$u->id)>{{ $u->name }}</option>
                  @endforeach
                </select>
              @endif
              @error('picUser_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="block mb-1">PIC Mitra</label>
              <input type="text" name="pic_mitra" value="{{ old('pic_mitra', $post->pic_mitra) }}" class="w-full border rounded px-3 py-2 @error('pic_mitra') border-red-500 @enderror" required>
              @error('pic_mitra')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
          </div>

          {{-- Dokumentasi (editable) --}}
          <div class="bg-gray-100 p-4 rounded">
            <label class="block text-orange-500 font-semibold mb-2">Dokumentasi</label>
            <div id="file-upload-area" class="border-2 border-dashed border-orange-300 rounded-lg p-4 text-center cursor-pointer hover:bg-orange-50 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
              <p class="mt-2 text-orange-600 font-medium">Klik untuk unggah file</p>
              <input type="file" name="file_path[]" id="file-upload-input" class="hidden" accept=".png,.jpg,.jpeg,.pdf" multiple>
            </div>
            <div id="selected-files" class="mt-4 space-y-3"></div>
          </div>

          {{-- Anak Perusahaan (read-only for mitra) --}}
          <div class="bg-gray-100 p-4 rounded">
            <label class="block text-orange-500 font-semibold mb-2">Anak Perusahaan?</label>
            <div class="flex items-center gap-6">
              @if($isMitra)
                <input type="hidden" name="is_child" value="{{ $post->is_child }}">
                <div class="p-2 bg-gray-100 rounded">{{ $post->is_child? 'Yes':'No' }}</div>
              @else
                <label class="inline-flex items-center">
                  <input type="radio" name="is_child" value="1" x-model="isChild" class="form-radio text-orange-500">
                  <span class="ml-2">Yes</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="radio" name="is_child" value="0" x-model="isChild" class="form-radio text-orange-500">
                  <span class="ml-2">No</span>
                </label>
              @endif
            </div>
            <div x-show="isChild" x-transition x-cloak class="mt-4">
              <label class="block text-gray-700 mb-1">Pilih Induk Perusahaan</label>
              @if($isMitra)
                <input type="hidden" name="parent_id" value="{{ $post->parent_id }}">
                <div class="p-2 bg-gray-100 rounded">{{ optional($post->parent)->title }}</div>
              @else
                <select name="parent_id" class="w-full border rounded px-3 py-2">
                  <option value="">— Pilih Induk —</option>
                  @foreach($parents as $p)
                    <option value="{{ $p->id }}" @selected(old('parent_id', $post->parent_id)==$p->id)>{{ $p->title }}</option>
                  @endforeach
                </select>
              @endif
            </div>
          </div>

        </div>

    </form>
  </div>

    <script>
        const fileUploadArea = document.getElementById('file-upload-area');
        const fileInput = document.getElementById('file-upload-input');
        const selectedFilesContainer = document.getElementById('selected-files');
        
        // Handle click on upload area
        fileUploadArea.addEventListener('click', () => fileInput.click());
        
        // Handle file selection
        fileInput.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                Array.from(this.files).forEach(file => {
                    addFilePreview(file);
                });
            }
        });
        
        // Add file preview with open functionality
        function addFilePreview(file) {
            const fileId = Date.now();
            const fileElement = document.createElement('div');
            fileElement.className = 'flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200';
            fileElement.dataset.fileId = fileId;
            
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const fileUrl = e.target.result;
                    fileElement.innerHTML = `
                        <div class="flex items-center">
                            <img src="${fileUrl}" class="h-10 w-10 object-cover rounded mr-3 cursor-pointer" 
                                 onclick="openFile('${fileId}')" title="Klik untuk melihat">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-800 truncate max-w-xs cursor-pointer" 
                                     onclick="openFile('${fileId}')">${file.name}</div>
                                <div class="text-xs text-gray-500">${formatFileSize(file.size)}</div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" class="text-blue-500 hover:text-blue-700" onclick="openFile('${fileId}')" title="Buka">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" class="text-red-500 hover:text-red-700" onclick="removeFilePreview('${fileId}')" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    `;
                    // Simpan file URL untuk dibuka nanti
                    fileElement.dataset.fileUrl = fileUrl;
                };
                reader.readAsDataURL(file);
            } else {
                const fileUrl = URL.createObjectURL(file);
                fileElement.innerHTML = `
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-red-100 flex items-center justify-center rounded mr-3 cursor-pointer" 
                             onclick="openFile('${fileId}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-medium text-gray-800 truncate max-w-xs cursor-pointer" 
                                 onclick="openFile('${fileId}')">${file.name}</div>
                            <div class="text-xs text-gray-500">${formatFileSize(file.size)}</div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" class="text-blue-500 hover:text-blue-700" onclick="openFile('${fileId}')" title="Buka">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button type="button" class="text-red-500 hover:text-red-700" onclick="removeFilePreview('${fileId}')" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                `;
                // Simpan file URL untuk dibuka nanti
                fileElement.dataset.fileUrl = fileUrl;
            }
            
            selectedFilesContainer.appendChild(fileElement);
        }
        
        // Fungsi untuk membuka file
        function openFile(fileId) {
            const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
            if (fileElement && fileElement.dataset.fileUrl) {
                const fileUrl = fileElement.dataset.fileUrl;
                
                // Buat modal untuk menampilkan file
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
                modal.innerHTML = `
                    <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-auto">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-lg font-medium">Preview File</h3>
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            ${fileElement.querySelector('img') ? 
                              `<img src="${fileUrl}" class="max-w-full h-auto mx-auto" alt="Preview">` : 
                              `<iframe src="${fileUrl}" class="w-full h-96 border-0"></iframe>`}
                        </div>
                        <div class="p-4 border-t flex justify-end">
                            <a href="${fileUrl}" download class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md mr-2 no-underline">
                                Download
                            </a>
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                                Tutup
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
            }
        }
        
        // Remove file preview
        function removeFilePreview(fileId) {
            const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
            if (fileElement) {
                // Hapus object URL untuk PDF
                if (fileElement.dataset.fileUrl && !fileElement.querySelector('img')) {
                    URL.revokeObjectURL(fileElement.dataset.fileUrl);
                }
                fileElement.remove();
            }
        }
        
        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

    </script>
</x-layout>
