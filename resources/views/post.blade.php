<x-layout>
    <x-slot:title>{{ $post->title }}</x-slot:title>

    {{-- Root Alpine untuk preview existing files --}}
    <div id="root" x-data="{ fileModal: false, fileUrl: '', isImage: false }"
         class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex flex-col items-center py-10 space-y-8">

      <!-- Main Card -->
      <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-2xl backdrop-blur-md p-8 relative">
        <!-- Action Button -->
        <div class="absolute top-4 right-4" x-data="{ openAction: false }"
             @mouseenter="openAction = true" @mouseleave="openAction = false">
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

            <!-- Lihat Pesan -->
            <a href="{{ route('posts.messages.index', $post) }}"
               class="flex items-center gap-2 w-full px-4 py-2 text-md font-bold text-orange-400 hover:bg-orange-300 hover:text-orange-700 transition no-underline">
              <i class="fas fa-envelope"></i>
              Lihat Pesan
            </a>

            <!-- Kirim Pesan -->
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

          <!-- Box 1: Judul & Kategori -->
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

          <!-- Box 2: Perjanjian Kerjasama -->
          <div class="flex flex-col items-center justify-center space-y-3 p-6">
            <h3 class="text-xl font-bold text-orange-500">Perjanjian Kerjasama</h3>
            <p class="text-3xl font-extrabold text-white">
              {{ $post->transactions->count() }} Kerjasama Aktif
            </p>

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

          <!-- Box 3: Status Periode -->
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

          <!-- Box 4: Maps -->
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

        <!-- Dokumentasi -->
        <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-lg p-6">
          <h3 class="text-orange-500 font-bold text-lg mb-4">Dokumentasi</h3>
          <div class="space-y-3">
            @php
              $raw   = $post->file_path;
              $files = $raw ? (json_decode($raw, true) ?: []) : [];
            @endphp

            @forelse($files as $idx => $path)
              @php
                $name        = basename($path);
                $publicUrl   = asset('storage/' . ltrim($path, '/')); 
                $downloadUrl = route('posts.files.download', [$post, $idx]);
                // ID unik untuk setiap file, misal "file-0", "file-1", dst.
                $fileId      = "file-{$idx}";
              @endphp

              <div
                class="flex items-center justify-between p-3 bg-white rounded-lg border"
                data-file-id="{{ $fileId }}"
                data-file-url="{{ $publicUrl }}"
              >
                <div class="truncate max-w-xs">{{ $name }}</div>

                <div class="flex space-x-2">
                  <!-- Tombol Preview: panggil openFile(fileId) -->
                <button
                  type="button"
                  onclick="openFile('{{ $fileId }}')"
                  class="text-orange-500 hover:text-orange-700"
                  title="Preview">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                    <path fill-rule="evenodd"
                          d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943
                            9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732
                            14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                          clip-rule="evenodd" />
                  </svg>
                </button>
        
                  <!-- Tombol Delete -->
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





      </div>

      <!-- Komisi Perusahaan -->
      <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/60 rounded-xl shadow-lg p-6 mt-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-orange-500 font-bold text-lg">Komisi</h2>
          <button id="btn-open-komisi"
                  class="bg-orange-400 hover:bg-orange-500 text-white font-bold px-4 py-2 rounded-lg">
            Tambah 
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-orange-300">
              <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">Anak Perusahaan</th>
                <th class="px-4 py-2">Item (Transaksi)</th>
                <th class="px-4 py-2">Persen Komisi</th>
                <th class="px-4 py-2">Nominal Komisi</th>
                <th class="px-4 py-2">Aksi</th>
              </tr>
            </thead>
            <tbody>


              @php
                // Kita asumsikan controller sudah melempar $commissions
                // yaitu koleksi Commission::with(['child','transaction'])->where('parent_post_id',$post->id)->get()
              @endphp

              @if(isset($commissions) && $commissions->isEmpty())
                <tr class="bg-white/50">
                  <td class="px-4 py-2" colspan="5">Tidak ada data komisi</td>
                </tr>
              @elseif(isset($commissions))
                @foreach($commissions as $idx => $c)
                  <tr class="bg-white/50 hover:bg-white/70 transition">
                    {{-- No --}}
                    <td class="px-4 py-2">{{ $idx + 1 }}</td>

                    {{-- Anak Perusahaan --}}
                    <td class="px-4 py-2">{{ $c->child->title }}</td>

                    {{-- Item/Transaksi (– jika null) --}}
                    <td class="px-4 py-2">
                      {{ optional($c->transaction)->nama_produk ?? '–' }}
                    </td>

                    {{--Persen Komisi --}}
                    <td class="px-4 py-2">
                     {{ number_format($c->commission_pct, 2, '.') }}%
                    </td>

                    {{-- Nominal Komisi --}}
                    <td class="px-4 py-2">
                      Rp {{ number_format($c->commission_amount, 2, ',', '.') }}
                    </td>

                   <!-- Action: -->
                <td class="px-4 py-2 flex gap-2">
                  <form action="{{ route('commissions.destroy', $c->id) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus komisi ini?');"
                        class="inline"
                  >
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
                @endforeach
              @endif
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
                <td class="px-4 py-2">{{ $transaction->nama_produk }} - {{ $transaction->jumlah }}</td>
                <td class="px-4 py-2">{{ $transaction->merk }} - {{ number_format($transaction->total_harga, 2) }}</td>
                <td class="px-4 py-2">{{ ucfirst($transaction->status) }}</td>
                <td class="px-4 py-2">{{ $transaction->approval_rs ? 'Ya' : 'Tidak' }}</td>
                <td class="px-4 py-2">{{ $transaction->approval_mitra ? 'Ya' : 'Tidak' }}</td>
                <td class="px-4 py-2 flex gap-2">
                  <a href="{{ route('posts.transactions.show', [$post, $transaction]) }}"
                     class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg text-xs font-bold no-underline">
                    Detail
                  </a>
                  <form
                    action="{{ route('posts.transactions.destroy', [$post, $transaction]) }}"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');"
                    class="inline"
                  >
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-400 hover:bg-red-500 text-white px-3 py-1 rounded-lg text-xs font-bold">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-2 text-center text-gray-600">Belum ada transaksi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Preview/Download (untuk existing files) -->
    <div x-show="fileModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg overflow-auto max-w-4xl w-full max-h-[90vh]" @click.away="fileModal = false">
        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b">
          <h3 class="text-lg font-medium">Preview File</h3>
          <button @click="fileModal = false"
                  class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <!-- Content -->
        <div class="p-4">
          <template x-if="isImage">
            <img :src="fileUrl" class="max-w-full h-auto mx-auto" alt="Preview">
          </template>
          <template x-if="!isImage">
            <iframe :src="fileUrl" class="w-full h-96 border-0"></iframe>
          </template>
        </div>
        <!-- Footer -->
        <div class="p-4 border-t flex justify-end space-x-2">
          <a :href="fileUrl" download
             class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
            Download
          </a>
          <button @click="fileModal = false"
                  class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Tambah Komisi -->
    <div id="modal-komisi" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
      <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2">
        <div class="flex justify-between items-center border-b px-4 py-3">
          <h3 class="text-lg font-medium text-gray-800">Tambah Komisi Baru</h3>
          <button id="btn-close-komisi"
                  class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6">
          <form action="{{ route('commissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <input type="hidden" name="parent_post_id" value="{{ $post->id }}">

            <!-- Dropdown Pilih Anak Perusahaan -->
            <div>
              <label for="child_post_id" class="block font-medium text-gray-700">Pilih Anak Perusahaan</label>
              <select name="child_post_id"
                      id="child_post_id"
                      class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-orange-400 focus:border-orange-400"
                      required>
                <option value="" disabled selected>-- Pilih perusahaan anak --</option>
                @foreach ($allChildren as $childOption)
                  <option value="{{ $childOption->id }}">
                    {{ $childOption->title }}
                  </option>
                @endforeach
              </select>
              @error('child_post_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
              @enderror
            </div>

            <!-- Dropdown Pilih Transaksi (di‐populate lewat JS) -->
            <div>
              <label for="transaction_id" class="block font-medium text-gray-700">Pilih Transaksi</label>
              <select name="transaction_id"
                      id="transaction_id"
                      class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-orange-400 focus:border-orange-400">
                <option value="" selected>-- (Pilih anak dahulu untuk melihat transaksinya) --</option>
              </select>
            </div>

            
            <!-- Kontainer untuk menampilkan daftar file yang di‐upload -->
            <div id="file-list" class="space-y-2"></div>

            <!-- Tombol Simpan & Batal -->
            <div class="flex justify-end space-x-2 pt-4">
              <button type="button"
                      id="btn-cancel-komisi"
                      class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Batal
              </button>
              <button type="submit"
                      class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded">
                Simpan Komisi
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Skrip untuk JS: Preview File Upload, Preview Dokumentasi, Dropdown Transaksi, dan Modal --}}
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi child‐transaction dropdown
        const childSelect = document.getElementById('child_post_id');
        const trxSelect   = document.getElementById('transaction_id');
        const childTransactions = {!! json_encode(
          $allChildren->mapWithKeys(function($child) {
            return [
              $child->id => $child->transactions->map(function($t) {
                return [
                  'id'           => $t->id,
                  'nama_produk'  => $t->nama_produk,
                  'total_harga'  => $t->total_harga,
                ];
              })->toArray()
            ];
          })
        ) !!};

        function populateTransactionOptions(childId) {
          trxSelect.innerHTML = '';
          const defaultOption = document.createElement('option');
          defaultOption.value   = '';
          defaultOption.textContent = '-- (Pilih transaksi ...) --';
          trxSelect.appendChild(defaultOption);

          if (!childTransactions[childId] || childTransactions[childId].length === 0) {
            return;
          }
          childTransactions[childId].forEach(function(trx) {
            const opt = document.createElement('option');
            opt.value = trx.id;
            opt.textContent = trx.nama_produk + ' – Rp ' +
              Number(trx.total_harga).toLocaleString('id-ID', { minimumFractionDigits: 2 });
            trxSelect.appendChild(opt);
          });
        }

        childSelect.addEventListener('change', function() {
          const selectedChildId = childSelect.value;
          if (selectedChildId) {
            populateTransactionOptions(selectedChildId);
          } else {
            trxSelect.innerHTML = '<option value="">-- (Pilih anak dahulu ...) --</option>';
          }
        });

        // Modal Tambah Komisi
        const btnOpen   = document.getElementById('btn-open-komisi');
        const btnClose  = document.getElementById('btn-close-komisi');
        const btnCancel = document.getElementById('btn-cancel-komisi');
        const modal     = document.getElementById('modal-komisi');

        btnOpen.addEventListener('click', () => modal.classList.remove('hidden'));
        btnClose.addEventListener('click', () => modal.classList.add('hidden'));
        btnCancel.addEventListener('click', () => modal.classList.add('hidden'));

        modal.addEventListener('click', function(evt) {
          if (evt.target === modal) {
            modal.classList.add('hidden');
          }
        });

        // ==== Skrip Preview File Upload ====
        const uploadBtn = document.getElementById('upload-btn');
        const fileInput = document.getElementById('file-input');
        const fileList  = document.getElementById('file-list');
        const dt = new DataTransfer();

        // Ketika tombol “Pilih File” diklik → trigger fileInput
        uploadBtn.addEventListener('click', () => fileInput.click());

        // Setelah user memilih file (bisa banyak), tambahkan ke dt dan render
        fileInput.addEventListener('change', e => {
          for (const file of e.target.files) {
            dt.items.add(file);
          }
          fileInput.files = dt.files;
          renderFileList();
          fileInput.value = ''; 
        });

        function renderFileList() {
          fileList.innerHTML = '';
          Array.from(dt.files).forEach((file, idx) => {
            const row = document.createElement('div');
            row.className = 'flex justify-between items-center bg-gray-100 rounded px-3 py-2';
            row.dataset.index = idx;

            const name = document.createElement('span');
            name.textContent = file.name;
            name.className = 'truncate';

            // Tombol preview
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
            previewBtn.addEventListener('click', () => openUploadPreview(idx));

            // Tombol delete
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

        function openUploadPreview(index) {
          const file = dt.files[index];
          if (!file) return;
          const url = URL.createObjectURL(file);
          const isImage = file.type.startsWith('image/');

          // Modal preview sederhana (sama formatnya dengan modal existing, tapi buat upload)
          const modalPreview = document.createElement('div');
          modalPreview.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
          modalPreview.innerHTML = `
            <div class="bg-white rounded-lg overflow-auto max-h-full">
              <div class="flex justify-end p-2">
                <button id="close-upload-modal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
              </div>
              <div class="p-4">
                ${ isImage 
                    ? `<img src="${url}" class="max-w-full h-auto mx-auto" alt="Preview">`
                    : `<iframe src="${url}" class="w-full h-96 border-0"></iframe>` }
              </div>
            </div>
          `;
          document.body.append(modalPreview);
          modalPreview.querySelector('#close-upload-modal').addEventListener('click', () => modalPreview.remove());
        }
  
      });
    </script>

    {{-- Skrip untuk open file --}}
    <script>
      function openFile(fileId) {
        const fileElement = document.querySelector(`[data-file-id="${fileId}"]`);
        if (fileElement && fileElement.dataset.fileUrl) {
          const fileUrl = fileElement.dataset.fileUrl;
          // Jika Anda ingin cek berdasarkan <img> di dalam fileElement:
          const isImage = !!fileElement.querySelector('img');
          // Atau cek berdasarkan ekstensi:
          // const isImage = /\.(jpe?g|png|gif)$/i.test(fileUrl);

          const modal = document.createElement('div');
          modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';

          modal.innerHTML = `
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-screen overflow-auto">
              <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-medium">Preview File</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <div class="p-4">
                ${isImage 
                  ? `<img src="${fileUrl}" class="max-w-full h-auto mx-auto" alt="Preview">` 
                  : `<iframe src="${fileUrl}" class="w-full h-96 border-0"></iframe>`}
              </div>
              <div class="p-4 border-t flex justify-end space-x-2">
                <a href="${fileUrl}" download 
                  class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-md no-underline">
                  Download
                </a>
                <button onclick="this.closest('.fixed').remove()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                  Tutup
                </button>
              </div>
            </div>
          `;

          document.body.appendChild(modal);
        }
      }
    </script>
</x-layout>
