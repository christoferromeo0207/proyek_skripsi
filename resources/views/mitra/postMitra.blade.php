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
      </div>

      <div class="bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 space-y-4 text-center">
        <h3 class="text-orange-500 font-bold">PIC RS</h3>
        <p class="font-semibold text-gray-800">{{ $post->picUser?->name ?? '-' }}</p>
      </div>

      <div class="bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 space-y-4 text-center">
        <h3 class="text-orange-500 font-bold">PIC Mitra</h3>
        <p class="font-semibold text-gray-800">{{ $post->pic_mitra }}</p>
      </div>
    </div>

    <!-- Dokumentasi -->
    <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-lg p-6">
      <h3 class="text-orange-500 font-bold text-lg mb-4">Dokumentasi</h3>
      <div class="space-y-3">
        @php
          $files = json_decode($post->file_path, true) ?: [];
        @endphp
        @forelse($files as $idx => $path)
          @php
            $name = basename($path);
            $url  = asset('storage'.ltrim($path, '/'));
          @endphp
          <div class="flex items-center justify-between p-3 bg-white rounded-lg border cursor-pointer">
            <span class="truncate max-w-xs" @click="fileUrl='{{ $url }}'; isImage=/(png|jpe?g|gif)$/i.test('{{ $url }}'); fileModal=true">
              {{ $name }}
            </span>
            <a href="{{ $url }}" download class="text-green-500 hover:text-green-700">⬇️</a>
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
</x-layout>
