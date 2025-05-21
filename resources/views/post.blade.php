<x-layout>
    <x-slot:title>{{ $post->title }}</x-slot:title>
  
    <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex flex-col items-center py-10 space-y-8">
  
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
          <div x-show="openAction"
               x-transition
               class="absolute right-0 mt-2 w-48 bg-orange-100 border border-orange-300 rounded-lg shadow-xl z-50 overflow-hidden">
           
              <!-- Edit Perusahaan -->
              <a href="{{ route('posts.edit', $post->slug) }}"
                class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition no-underline">
                <i class="fas fa-edit"></i>
                Edit
              </a>

              <!-- Hapus Perusahaan -->
              <form method="POST"
                    action="{{ route('posts.destroy', $post) }}"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra ini?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition">
                  <i class="fas fa-trash-alt"></i>
                  Hapus
                </button>
              </form>


             <!--Lihat Pesan -->
              <a href="{{ route('posts.messages.index', $post) }}"
                class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition no-underline">
                <i class="fas fa-envelope"></i>
                Lihat Pesan
              </a>

              <!--Kirim Pesan -->
              <a href="{{ route('posts.messages.create', $post) }}"
                class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition no-underline">
                <i class="fas fa-paper-plane"></i>
                Kirim Pesan
              </a>

      

          </div>
        </div>
  
        <!-- Grid Boxes -->
        <div class="relative grid grid-cols-1 md:grid-cols-2 md:grid-rows-2">
          <div class="hidden md:block absolute top-1/2 left-0 w-full h-[1px] bg-white"></div>
          <div class="hidden md:block absolute top-0 left-1/2 w-[1px] h-full bg-white"></div>
  
          <!-- Box 1 -->
          <div class="flex flex-col items-center justify-center space-y-3 p-6">
            <h2 class="text-2xl md:text-3xl font-bold text-white text-center bg-gradient-to-br from-orange-400 to-orange-500 px-6 py-2 rounded-md shadow-md">
              {{ $post->title }}
            </h2>
            <div class="flex flex-wrap items-center justify-center gap-3">
              <span class="px-3 py-2 rounded-full text-white text-xs font-semibold"
                    style="background-color: {{ $post->category->color }};">
                {{ $post->category->name }}
              </span>
              <span class="flex items-center gap-1 bg-orange-100 text-orange-600 font-bold text-xs px-3 py-2 rounded-full">
                <i class="fas fa-map-marker-alt"></i>{{ $post->alamat }}
              </span>
            </div>
          </div>
  
         <!-- Box 2 -->
          <div class="flex flex-col items-center justify-center space-y-3 p-6">
            <h3 class="text-xl font-bold text-orange-500">Perjanjian Kerjasama</h3>
            {{-- hitung berapa transaksi (kerjasama) aktif untuk post ini --}}
            <p class="text-3xl font-extrabold text-white">
              {{ $post->transactions->count() }} Kerjasama Aktif
            </p>

            {{-- dropdown See Details --}}
            <div x-data="{ open: false }" class="relative">
              <button @click="open = !open"
                      class="bg-orange-400 text-white font-bold px-6 py-2 rounded-md hover:bg-orange-500 transition">
                See Details
              </button>

              <div x-show="open"
                  @click.away="open = false"
                  x-transition
                  class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 overflow-hidden">
                @forelse($post->transactions as $transaction)
                  <a href="{{ route('posts.transactions.show', [$post, $transaction]) }}"
                    class="block px-4 py-2 text-gray-800 hover:bg-gray-100 no-underline">
                    {{ $transaction->nama_produk }}
                  </a>
                @empty
                  <div class="px-4 py-2 text-sm text-gray-500">Tidak ada produk kerjasama.</div>
                @endforelse
              </div>
            </div>
          </div>

  
          <!-- Box 3 -->
          @php
              $today = \Carbon\Carbon::today();
              $start = \Carbon\Carbon::parse($post->tanggal_awal);
              $end   = \Carbon\Carbon::parse($post->tanggal_akhir);

              if ($today->lt($start)) {
                  $status = 'Proses';
                  $color  = 'bg-yellow-500';
              } elseif ($today->between($start, $end)) {
                  $status = 'Aktif';
                  $color  = 'bg-green-500';
              } else {
                  $status = 'Berakhir';
                  $color  = 'bg-red-500';
              }
          @endphp

          <div class="flex flex-col items-center justify-center space-y-3 p-6">
            <div class="flex items-center space-x-2">
              <h3 class="text-2xl md:text-3xl font-extrabold text-orange-500">Periode:</h3>
              <span class="{{ $color }} text-white font-bold px-3 py-1 rounded-lg text-lg">
                {{ $status }}
              </span>
            </div>
            <p class="text-white font-extrabold text-lg mt-2 font-mono">
              {{ \Carbon\Carbon::parse($post->tanggal_awal)->format('d M Y') }}
              – 
              {{ \Carbon\Carbon::parse($post->tanggal_akhir)->format('d M Y') }}
            </p>
          </div>

            
          <!-- Box 4 -->
          <div class="flex flex-col items-center justify-center space-y-3 p-6">
            <h3 class="text-xl font-bold text-orange-500">Maps</h3>
            <div class="w-60 h-40 bg-gray-300 rounded-lg overflow-hidden shadow-lg">
              <iframe class="w-full h-full"
                      src="https://maps.google.com/maps?q={{ urlencode($post->alamat) }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
                      frameborder="0" allowfullscreen>
              </iframe>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Deskripsi Perusahaan -->
      <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6">
        <h2 class="text-orange-500 font-bold text-lg mb-2">Deskripsi Perusahaan</h2>
        <p class="text-gray-800 text-justify leading-relaxed">{{ $post->body }}</p>
      </div>
  
      <!-- Contact Info + PIC + PIC Mitra + File Pendukung -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-11/12 md:w-4/5 lg:w-3/4 mt-8">
  
        <!-- Kolom 1: Contact Info -->
        <div class="bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 space-y-5 border border-blue-400">
          <!-- Nomor Telepon -->
          <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.707 12.293a.999.999 0 0 0-1.414 0l-1.594 1.594c-.739-.22-2.118-.72-2.992-1.594s-1.374-2.253-1.594-2.992l1.594-1.594a.999.999 0 0 0 0-1.414l-4-4a.999.999 0 0 0-1.414 0L3.581 5.005c-.38.38-.594.902-.586 1.435.023 1.424.4 6.37 4.298 10.268s8.844 4.274 10.269 4.298h.028c.528 0 1.027-.208 1.405-.586l2.712-2.712a.999.999 0 0 0 0-1.414l-4-4.001zm-.127 6.712c-1.248-.021-5.518-.356-8.873-3.712-3.366-3.366-3.692-7.651-3.712-8.874L7 4.414 9.586 7 8.293 8.293a1 1 0 0 0-.272.912c.024.115.611 2.842 2.271 4.502s4.387 2.247 4.502 2.271a.991.991 0 0 0 .912-.271L17 14.414 19.586 17l-2.006 2.005z"/>
            </svg>
            <div>
              <div class="text-orange-500 font-bold">Nomor Telepon</div>
              <div class="text-gray-800">{{ $post->phone }}</div>
            </div>
          </div>
          <!-- Email -->
          <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M20 4H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V6c0-1.103-.897-2-2-2zm0 2v.511l-8 6.223-8-6.222V6h16zM4 18V9.044l7.386 5.745a.994.994 0 0 0 1.228 0L20 9.044 20.002 18H4z"/>
            </svg>
            <div>
              <div class="text-orange-500 font-bold">Email</div>
              <div class="text-gray-800">{{ $post->email }}</div>
            </div>
          </div>
          <!-- Lokasi -->
          <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 14c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.103 0 2 .897 2 2s-.897 2-2 2-2-.897-2-2 .897-2 2-2z"/>
              <path d="M11.42 21.814a.998.998 0 0 0 1.16 0C12.884 21.599 20.029 16.44 20 10c0-4.411-3.589-8-8-8S4 5.589 4 9.995c-.029 6.445 7.116 11.604 7.42 11.819zM12 4c3.309 0 6 2.691 6 6.005.021 4.438-4.388 8.423-6 9.73-1.611-1.308-6.021-5.294-6-9.735 0-3.309 2.691-6 6-6z"/>
            </svg>
            <div>
              <div class="text-orange-500 font-bold">Lokasi</div>
              <div class="text-gray-800">{{ $post->alamat }}</div>
            </div>
          </div>
          <!-- Pembayaran -->
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
  
        <!-- Dokumentasi: Read-only list dari $post->file_path -->
        <div class="self-start flex justify-end">
          <div class="bg-white/30 rounded-xl shadow-lg backdrop-blur-md p-4 w-full max-w-xs flex flex-col space-y-3">
            <div class="flex items-center justify-between">
              <h3 class="text-orange-500 font-bold text-lg">Dokumentasi</h3>
            </div>

            <div id="file-list" name="file_path" class="space-y-2 text-sm text-gray-800">
              @php
                // Ambil string file_path, lalu decode JSON menjadi array.
                $raw = $post->file_path;
                $files = [];
                if ($raw) {
                    // jika JSON valid, decode; kalau tidak, coba explode koma
                    $decoded = json_decode($raw, true);
                    $files   = is_array($decoded) ? $decoded : explode(',', $raw);
                }
              @endphp

              @forelse($files as $path)
                @php
                  $name = basename($path);
                  $url  = Storage::url($path);
                @endphp

                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-200">
                  <div class="truncate">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="inline-block h-5 w-5 text-gray-500 mr-2"
                        fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 2H5a2 2 0 00-2 2v16a2 2 0 002 2h14a2 2 0 002-2V4a2 2 0 00-2-2zm-7 16h-4v-2h4v2zm0-4h-4v-2h4v2zm6 4h-4v-2h4v2zm0-4h-4v-2h4v2z"/>
                    </svg>
                    <span class="align-middle">{{ $name }}</span>
                  </div>

                  <div class="flex space-x-2">
                    <a href="{{ $url }}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Buka">
                      <!-- eye icon -->
                      <svg xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd"
                              d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 
                                9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 
                                14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                              clip-rule="evenodd"/>
                      </svg>
                    </a>
                    <a href="{{ $url }}" download class="text-green-500 hover:text-green-700" title="Download">
                      <!-- download icon -->
                      <svg xmlns="http://www.w3.org/2000/svg"
                          class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v2a2 2 0 002 2h12a2 2 
                                0 002-2v-2m-4-4l-4 4m0 
                                0l-4-4m4 4V4"/>
                      </svg>
                    </a>
                  </div>
                </div>
              @empty
                <p class="text-gray-500">Belum ada file dokumentasi.</p>
              @endforelse
            </div>
          </div>
        </div>



      </div>
  
      <!-- Section: Komisi Table -->
      <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/60 rounded-xl shadow-lg p-6 mt-8">
        <h2 class="text-orange-500 font-bold text-lg mb-4">Komisi</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-orange-300">
              <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Item</th>
                <th class="px-4 py-2">Nominal</th>
                <th class="px-4 py-2">Anak Perusahaan</th>
                <th class="px-4 py-2">#</th>
              </tr>
            </thead>
            <tbody>
              <tr class="bg-white/50">
                <td class="px-4 py-2" colspan="5">Tidak ada data komisi</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

  
      <!-- Produk Kerjasama Hasil Transaksi -->
      <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/60 rounded-xl shadow-lg p-6 mt-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-orange-500 font-bold text-lg">Produk Kerjasama</h2>
          <a href="{{ route('posts.transactions.create', $post) }}"
            class="bg-orange-400 hover:bg-orange-500 text-white font-bold px-4 py-2 rounded-lg no-underline">
            Tambah
          </a>

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
              <a href="{{ route('posts.transactions.show', [$post, $transaction]) }}"
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
                    class="bg-red-400 hover:bg-red-500 text-white px-3 py-1 rounded-lg text-xs font-bold"
                  >
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
  