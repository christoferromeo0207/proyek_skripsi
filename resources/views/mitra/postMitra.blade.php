<x-layout>
  <x-slot:title>{{ $companyTitle }}</x-slot:title>

  <div x-data="{ fileModal: false, fileUrl: '', isImage: false }"
       class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex flex-col items-center py-10 space-y-8">

    <!-- Main Card -->
    <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-2xl backdrop-blur-md p-8 relative">

      <!-- Action Button -->
      <div class="absolute top-4 right-4" x-data="{ openAction: false }" @mouseenter="openAction = true" @mouseleave="openAction = false">
        <button @click="openAction = !openAction"
                class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-orange-200 transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="rgb(255, 138, 43)" viewBox="0 0 24 24">
            <path d="M16.939 10.939 12 15.879l-4.939-4.94-2.122 2.122L12 20.121l7.061-7.06z"/>
            <path d="M16.939 3.939 12 8.879l-4.939-4.94-2.122 2.122L12 13.121l7.061-7.06z"/>
          </svg>
        </button>
        <div x-show="openAction" x-transition
             class="absolute right-0 mt-2 w-48 bg-orange-100 border border-orange-300 rounded-lg shadow-xl z-50 overflow-hidden">
          <!-- Edit Link -->
          <a href="{{ route('mitra.editMitra', $post) }}"
             class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition no-underline">
            <i class="fas fa-edit"></i> Edit
          </a>
          <!--Lihat Pesan -->
          <a href="{{ route('mitra.informasi.messages.index', $post) }}"
            class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition no-underline">
            <i class="fas fa-envelope"></i>
            Lihat Pesan
          </a>
          <!--Kirim Pesan -->
          <a href="{{ route('mitra.informasi.messages.create', $post) }}"
            class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition no-underline">
            <i class="fas fa-paper-plane"></i>
            Kirim Pesan
          </a>

        </div>
      </div>

      <!-- Grid Boxes -->
      <div class="relative grid grid-cols-1 md:grid-cols-2 md:grid-rows-2 gap-4">
        <div class="hidden md:block absolute top-1/2 left-0 w-full h-[1px] bg-white"></div>
        <div class="hidden md:block absolute top-0 left-1/2 w-[1px] h-full bg-white"></div>

        <!-- Box 1: Title & Location -->
        <div class="flex flex-col items-center justify-center space-y-3 p-6">
        <h2 class="text-2xl md:text-3xl font-bold text-white text-center bg-gradient-to-br from-orange-400 to-orange-500 px-6 py-2 rounded-md shadow-md">
            {{ $post->title }}</h2>

        @if($post->category)
            <span
            class="px-3 py-2 rounded-full text-white text-xs font-semibold"
            style="background-color: {{ $post->category->color }};">
            {{ $post->category->name }}
            </span>
        @endif

        <span class="flex items-center gap-1 bg-orange-100 text-orange-600 font-bold text-xs px-3 py-2 rounded-full">
            <i class="fas fa-map-marker-alt"></i>
            {{ $post->alamat }}
        </span>
        </div>


        <!-- Box 2: Active Collaborations -->
        <div class="flex flex-col items-center justify-center space-y-3 p-6">
          <h3 class="text-xl font-bold text-orange-500">Perjanjian Kerjasama</h3>
          <p class="text-3xl font-extrabold text-white">{{ $post->transactions->count() }} Aktif</p>
          <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="bg-orange-400 text-white font-bold px-6 py-2 rounded-md hover:bg-orange-500 transition">
              Lihat Detail
            </button>
            <div x-show="open" @click.away="open = false" x-transition
                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 overflow-hidden">
              @forelse($post->transactions as $trx)
                <a href="{{ route('posts.transactions.show', [$post, $trx]) }}" 
                class="block px-4 py-2 text-gray-800 hover:bg-gray-100 no-underline">
                  {{ $trx->nama_produk }}
                </a>
              @empty
                <div class="px-4 py-2 text-sm text-gray-500">Tidak ada transaksi</div>
              @endforelse
            </div>
          </div>
        </div>

        <!-- Box 3: Status & Period -->
        @php
          $today = \Carbon\Carbon::today();
          $start = \Carbon\Carbon::parse($post->tanggal_awal);
          $end   = \Carbon\Carbon::parse($post->tanggal_akhir);
          if ($today->lt($start)) { $status='Proses'; $color='bg-yellow-500'; }
          elseif ($today->between($start,$end)) { $status='Aktif'; $color='bg-green-500'; }
          else { $status='Selesai'; $color='bg-red-500'; }
        @endphp
        <div class="flex flex-col items-center justify-center space-y-3 p-6">
          <div class="flex items-center space-x-2">
            <h3 class="text-2xl font-extrabold text-orange-500">Status</h3>
            <span class="{{ $color }} text-white font-bold px-3 py-1 rounded-lg text-lg">{{ $status }}</span>
          </div>
          <p class="text-white font-extrabold text-lg font-mono">
            {{ \Carbon\Carbon::parse($post->tanggal_awal)->format('d M Y') }} – {{ \Carbon\Carbon::parse($post->tanggal_akhir)->format('d M Y') }}
          </p>
        </div>

        <!-- Box 4: Map -->
        <div class="flex flex-col items-center justify-center space-y-3 p-6">
          <h3 class="text-xl font-bold text-orange-500">Lokasi</h3>
          <div class="w-60 h-40 bg-gray-300 rounded-lg overflow-hidden shadow-lg">
            <iframe class="w-full h-full"
                    src="https://maps.google.com/maps?q={{ urlencode($post->alamat) }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
                    frameborder="0" allowfullscreen>
            </iframe>
          </div>
        </div>
      </div>
    </div>

    <!-- Description -->
    <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6">
      <h2 class="text-orange-500 font-bold text-lg mb-2">Deskripsi</h2>
      <p class="text-gray-800 leading-relaxed">{{ $post->body }}</p>
    </div>

    <!-- Contact & PIC -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-11/12 md:w-4/5 lg:w-3/4">
      <div class="bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 space-y-4">
        <div class="flex items-center gap-2">
          <i class="fas fa-phone text-orange-500"></i>
          <div>
            <div class="font-bold text-orange-500">Telepon</div>
            <div class="text-gray-800">{{ $post->phone }}</div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <i class="fas fa-envelope text-orange-500"></i>
          <div>
            <div class="font-bold text-orange-500">Email</div>
            <div class="text-gray-800">{{ $post->email }}</div>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <i class="fas fa-map-marker-alt text-orange-500"></i>
          <div>
            <div class="font-bold text-orange-500">Alamat</div>
            <div class="text-gray-800">{{ $post->alamat }}</div>
          </div>
        </div>
         <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/>
              <path d="M12 11c-2 0-2-.63-2-1s.7-1 2-1 1.39.64 1.4 1h2A3 3 0 0 0 13 7.12V6h-2v1.09C9 7.42 8 8.71 8 10c0 1.12.52 3 4 3 2 0 2 .68 2 1s-.62 1-2 1c-1.84 0-2-.86-2-1H8c0 .92.66 2.55 3 2.92V18h2v-1.08c2-.34 3-1.63 3-2.92 0-1.12-.52-3-4-3z"/>
            </svg>
            <div>
              <div class="text-orange-500 font-bold">Pembayaran</div>
              <div class="text-gray-800">{{ $post->pembayaran }}</div>
            </div>
          </div>
          <!-- Keterangan BPJS -->
          <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M11.63 21.91A.9.9 0 0 0 12 22a1 1 0 0 0 .41-.09C22 17.67 21 7 21 6.9a1 1 0 0 0-.55-.79l-8-4a1 1 0 0 0-.9 0l-8 4A1 1 0 0 0 3 6.9c0 .1-.92 10.77 8.63 15.01zM5 7.63l7-3.51 7 3.51c.05 2-.27 9-7 12.27C5.26 16.63 4.94 9.64 5 7.63z"/>
              <path d="M11.06 16h2v-3h3.01v-2h-3.01V8h-2v3h-3v2h3v3z"/>
            </svg>
            <div>
              <div class="text-orange-500 font-bold">Keterangan BPJS</div>
              <div>
                <span class="inline-block bg-green-400 text-white text-sm font-bold px-3 py-1 rounded-full">
                  {{ $post->keterangan_bpjs === 'yes' ? 'Ya' : 'Tidak' }}
                </span>
              </div>
            </div>
          </div>
      </div>
        <!-- Kolom 2: PIC & PIC Mitra -->
        <div class="space-y-6">
          <!-- CARD PIC -->
          <div class="bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 flex flex-col items-center space-y-4">
            <h3 class="text-orange-500 font-bold text-xl">PIC Rumah Sakit</h3>
            <p class="text-gray-800 font-semibold text-center">{{ $post->picUser?->name ?? 'Tidak ada PIC' }}</p>
          </div>
          <!-- CARD PIC MITRA -->
          <div class="bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 flex flex-col items-center space-y-4">
            <h3 class="text-orange-500 font-bold text-xl">PIC Mitra</h3>
            <p class="text-gray-800 font-semibold text-center">{{ $post->pic_mitra ?? 'Tidak ada PIC Mitra' }}</p>
          </div>
        </div>
    </div>

       <!-- Dokumentasi -->
        <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-lg p-6">
          <h3 class="text-orange-500 font-bold text-lg mb-4">Dokumentasi</h3>
          <div class="space-y-3">
            @php
              $raw   = $post->file_path;
              $files = $raw ? (json_decode($raw, true) ?: explode(',', $raw)) : [];
            @endphp

            @forelse($files as $idx => $path)
              @php
                $name        = basename($path);
                // Public URL (storage/app/public/…)
                $publicUrl   = asset('storage/' . ltrim($path, '/'));
                // Your download route
                $downloadUrl = route('posts.files.download', [$post, $idx]);
                $fileId      = "file-{$idx}";
              @endphp

              <div
                class="flex items-center justify-between p-3 bg-white rounded-lg border"
                data-file-id="{{ $fileId }}"
                data-file-url="{{ $publicUrl }}"
              >
                <div class="truncate max-w-xs">{{ $name }}</div>
                <div class="flex space-x-2">
                  <!-- Preview -->
                  <button
                    type="button"
                    onclick="openFile('{{ $fileId }}')"
                    class="text-blue-500 hover:text-blue-700"
                    title="Preview"
                  >👁️</button>

                  <!-- Download -->
                  <a
                    href="{{ $downloadUrl }}"
                    class="text-green-500 hover:text-green-700"
                    title="Download"
                  >⬇️</a>

                  <!-- Delete -->
                  <form
                    action="{{ route('posts.files.destroy', [$post, $idx]) }}"
                    method="POST"
                    onsubmit="return confirm('Hapus file {{ $name }}?');"
                  >
                    @csrf @method('DELETE')
                    <button
                      type="submit"
                      class="text-red-500 hover:text-red-700"
                      title="Delete"
                    >🗑️</button>
                  </form>
                </div>
              </div>
            @empty
              <p class="text-gray-500">Belum ada dokumentasi.</p>
            @endforelse
          </div>
        </div>


    <!-- Produk Kerjasama Hasil Transaksi -->
      <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/60 rounded-xl shadow-lg p-6 mt-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-orange-500 font-bold text-lg">Produk Kerjasama</h2>


        </div>
        <table class="w-full text-sm text-left text-gray-700">
          <thead class="text-xs text-gray-700 uppercase bg-orange-300">
            <tr>
              <th class="px-4 py-2">Produk-Jumlah</th>
              <th class="px-4 py-2">Merk-Harga</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Approval RS</th>
              <th class="px-4 py-2">Approval Mitra</th>
              <th class="px-4 py-2">Action</th>
            </tr>
          </thead>
          <tbody>
        @forelse($post->transactions as $transaction)
          <tr class="bg-white/50 hover:bg-white/70 transition">
            <!-- Produk – Jumlah -->
            <td class="px-4 py-2">
              {{ $transaction->nama_produk }} - {{ $transaction->jumlah }}
            </td>

            <!-- Merk – Total Harga -->
            <td class="px-4 py-2">
              {{ $transaction->merk }} - {{ number_format($transaction->total_harga, 2) }}
            </td>

            <!-- Status -->
            <td class="px-4 py-2">
              {{ ucfirst($transaction->status) }}
            </td>

            <!-- Approval RS -->
            <td class="px-4 py-2">
              {{ $transaction->approval_rs ? 'Ya' : 'Tidak' }}
            </td>

            <!-- Approval Mitra -->
            <td class="px-4 py-2">
              {{ $transaction->approval_mitra ? 'Ya' : 'Tidak' }}
            </td>

            <!-- Action -->
            <td class="px-4 py-2 flex gap-2">
              <!-- Detail: popup atau ke halaman edit -->
              <a href="{{ route('mitra.transactions.show', [$post, $transaction]) }}"
                class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg text-xs font-bold no-underline">
                Detail
              </a>

              <!-- Hapus -->
             <form
                action="{{ route('posts.transactions.destroy', [$post, $transaction]) }}"
                method="POST"
                onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');"
                class="inline">
                  @csrf
                  @method('DELETE')
                  <button
                    type="submit"
                    class="bg-red-400 hover:bg-red-500 text-white px-3 py-1 rounded-lg text-xs font-bold">
                    Hapus
                  </button>
              </form>

            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-2 text-center text-gray-600">
              Belum ada transaksi.
            </td>
          </tr>
        @endforelse
      </tbody>

        </table>
      </div>
  </div>


   <script>
    function openFile(fileId) {
      const fileEl = document.querySelector(`[data-file-id="${fileId}"]`);
      if (!fileEl) return;
      const url = fileEl.dataset.fileUrl;
      const isImage = /\.(jpe?g|png|gif|webp)$/i.test(url);

      const modal = document.createElement('div');
      modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
      modal.innerHTML = `
        <div role="dialog" class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-auto">
          <div class="flex justify-between items-center p-4 border-b">
            <h3 class="text-lg font-medium">Preview File</h3>
            <button onclick="this.closest('[role=dialog]').parentElement.remove()" 
                    class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
          </div>
          <div class="p-4">
            ${ isImage
                ? `<img src="${url}" class="max-w-full h-auto mx-auto" alt="Preview">`
                : `<iframe src="${url}" class="w-full h-96 border-0"></iframe>` }
          </div>
          <div class="p-4 border-t flex justify-end space-x-2">
            <a href="${url}" download
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
              Download
            </a>
            <button onclick="this.closest('[role=dialog]').parentElement.remove()"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
              Tutup
            </button>
          </div>
        </div>
      `;
      document.body.appendChild(modal);
    }
  </script>

  <script>
    
      const uploadBtn = document.getElementById('upload-btn');
      const fileInput = document.getElementById('file-input');
      const fileList  = document.getElementById('file-list');
    
      // DataTransfer persist untuk menyimpan semua file
      const dt = new DataTransfer();
    
      // Buka dialog file
      uploadBtn.addEventListener('click', () => fileInput.click());
    
      // Saat user memilih file
      fileInput.addEventListener('change', e => {
        // Tambahkan setiap file baru ke DataTransfer
        for (const file of e.target.files) {
          dt.items.add(file);
        }
        // Set kembali fileInput.files
        fileInput.files = dt.files;
        // Render ulang daftar
        renderFileList();
        // Clear supaya bisa pilih file yang sama lagi
        fileInput.value = '';
      });
    
      function renderFileList() {
        fileList.innerHTML = '';  // kosongkan dulu
    
        Array.from(dt.files).forEach((file, idx) => {
          // baris container
          const row = document.createElement('div');
          row.className = 'flex justify-between items-center bg-gray-100 rounded px-3 py-2';
          row.dataset.index = idx;
    
          // nama file
          const name = document.createElement('span');
          name.textContent = file.name;
          name.className = 'truncate';
    
          // tombol preview
          const previewBtn = document.createElement('button');
          previewBtn.type = 'button';
          previewBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg"
                  class="w-5 h-5 text-blue-500 hover:text-blue-700"
                  viewBox="0 0 20 20"
                  fill="currentColor">
              <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
              <path fill-rule="evenodd"
                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943
                        9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732
                        14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                    clip-rule="evenodd"/>
            </svg>`;
          previewBtn.title = 'Preview';
          previewBtn.addEventListener('click', () => openPreview(idx));
    
          // tombol delete
          const deleteBtn = document.createElement('button');
          deleteBtn.type = 'button';
          deleteBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg"
                  class="w-5 h-5 text-red-500 hover:text-red-700"
                  viewBox="0 0 20 20"
                  fill="currentColor">
              <path fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000
                        2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1
                        0 100-2h-3.382l-.724-1.447A1 1 0
                        0011 2H9zM7 8a1 1 0 012 0v6a1 1 0
                        11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1
                        0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd"/>
            </svg>`;
          deleteBtn.title = 'Hapus';
          deleteBtn.addEventListener('click', () => {
            dt.items.remove(idx);
            fileInput.files = dt.files;
            renderFileList();
          });
    
          row.append(name, previewBtn, deleteBtn);
          fileList.append(row);
        });
      }
    
      function openPreview(index) {
        const file = dt.files[index];
        if (!file) return;
    
        const url = URL.createObjectURL(file);
        const isImage = file.type.startsWith('image/');
    
        const modal = document.createElement('div');
        modal.className = 
          'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
          <div class="bg-white rounded-lg overflow-auto max-h-full">
            <div class="flex justify-end p-2">
              <button id="close-modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-4">
              ${ isImage 
                  ? `<img src="${url}" class="max-w-full h-auto mx-auto" alt="Preview">`
                  : `<iframe src="${url}" class="w-full h-96 border-0"></iframe>`
              }
            </div>
          </div>
        `;
        document.body.append(modal);
        modal.querySelector('#close-modal')
              .addEventListener('click', () => modal.remove());
      }
    </script>
</x-layout>
