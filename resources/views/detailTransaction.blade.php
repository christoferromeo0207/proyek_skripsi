{{-- resources/views/posts/transactions/edit.blade.php --}}
<x-layout>
  <x-slot:title>Detail &amp; Edit Transaksi – {{ $transaction->nama_produk }}</x-slot:title>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      {{ session('error') }}
    </div>
  @endif

  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @php
    // Decode field bukti_pembayaran yang disimpan sebagai JSON/array
    $raw = $transaction->getRawOriginal('bukti_pembayaran');
    if (is_string($raw)) {
      $decoded = json_decode($raw, true);
      if (is_string($decoded)) {
        $decoded = json_decode($decoded, true);
      }
      $files = is_array($decoded) ? $decoded : [];
    } elseif (is_array($raw)) {
      $files = $raw;
    } else {
      $files = [];
    }
  @endphp

  <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 py-10 px-4">
    <div class="max-w-screen-xl mx-auto">
      <form
        action="{{ route('posts.transactions.update', [$post, $transaction]) }}"
        method="POST"
        enctype="multipart/form-data"
        class="bg-white p-8 rounded-2xl shadow-lg"
      >
        @csrf
        @method('PUT')

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold text-orange-600">Detail &amp; Edit Transaksi</h1>
        </div>

        {{-- Grid: 2 kolom field + 1 kolom bukti pembayaran --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {{-- Kolom Kiri & Tengah: Form fields --}}
          <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Produk -->
            <div>
              <label class="block text-orange-500 font-semibold">Nama Produk</label>
              <input type="text" name="nama_produk"
                     value="{{ old('nama_produk', $transaction->nama_produk) }}"
                     class="w-full border rounded px-3 py-2" required>
            </div>
            <!-- Jumlah -->
            <div>
              <label class="block text-orange-500 font-semibold">Jumlah</label>
              <input type="number" name="jumlah" min="1"
                     value="{{ old('jumlah', $transaction->jumlah) }}"
                     class="w-full border rounded px-3 py-2" required>
            </div>
            <!-- Merk -->
            <div>
              <label class="block text-orange-500 font-semibold">Merk Produk</label>
              <input type="text" name="merk"
                     value="{{ old('merk', $transaction->merk) }}"
                     class="w-full border rounded px-3 py-2" required>
            </div>
            <!-- Harga per Satuan -->
            <div>
              <label class="block text-orange-500 font-semibold">Harga per Satuan</label>
              <input type="number" name="harga_satuan" step="0.01" min="1"
                     value="{{ old('harga_satuan', $transaction->harga_satuan) }}"
                     class="w-full border rounded px-3 py-2" required>
            </div>
            <!-- Tipe Pembayaran -->
            <div>
              <label class="block text-orange-500 font-semibold">Tipe Pembayaran</label>
              <select name="tipe_pembayaran" class="w-full border rounded px-3 py-2" required>
                <option disabled value="">Pilih metode</option>
                @foreach(['Transfer','Cash','Kredit'] as $m)
                  <option value="{{ $m }}"
                    {{ old('tipe_pembayaran', $transaction->tipe_pembayaran) === $m ? 'selected' : '' }}>
                    {{ $m }}
                  </option>
                @endforeach
              </select>
            </div>
            <!-- Status -->
            <div>
              <label class="block text-orange-500 font-semibold">Status</label>
              <select name="status" class="w-full border rounded px-3 py-2" required>
                @foreach(['Proses','Selesai','Dibatalkan'] as $st)
                  <option value="{{ $st }}"
                    {{ old('status', $transaction->status) === $st ? 'selected' : '' }}>
                    {{ $st }}
                  </option>
                @endforeach
              </select>
            </div>
            <!-- PIC RS -->
            <div>
              <label class="block text-orange-500 font-semibold">PIC Rumah Sakit</label>
              <select name="pic_rs" class="w-full border rounded px-3 py-2" required>
                <option value="">— Pilih PIC RS —</option>
                @foreach($users as $user)
                  <option value="{{ $user->id }}"
                    {{ old('pic_rs', $transaction->pic_rs) == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <!-- Approval RS -->
            <div>
              <label class="block text-orange-500 font-semibold">Approval RS</label>
              <select name="approval_rs" class="w-full border rounded px-3 py-2" required>
                <option value="1" {{ old('approval_rs', $transaction->approval_rs)==1?'selected':'' }}>Ya</option>
                <option value="0" {{ old('approval_rs', $transaction->approval_rs)==0?'selected':'' }}>Tidak</option>
              </select>
            </div>
            <!-- PIC Mitra -->
            <div>
              <label class="block text-orange-500 font-semibold">PIC Mitra</label>
              <input type="text" name="pic_mitra"
                     value="{{ old('pic_mitra', $transaction->pic_mitra) }}"
                     class="w-full border rounded px-3 py-2" required>
            </div>
            <!-- Approval Mitra -->
            <div>
              <label class="block text-orange-500 font-semibold">Approval Mitra</label>
              <select name="approval_mitra" class="w-full border rounded px-3 py-2" required>
                <option value="1" {{ old('approval_mitra', $transaction->approval_mitra)==1?'selected':'' }}>Ya</option>
                <option value="0" {{ old('approval_mitra', $transaction->approval_mitra)==0?'selected':'' }}>Tidak</option>
              </select>
            </div>
          </div>

          {{-- Kolom Kanan: Kotak Bukti Pembayaran --}}
          <div class="bg-gray-50 p-6 rounded border">
            <h2 class="text-xl font-semibold text-orange-600 mb-4">Bukti Pembayaran</h2>

            {{-- Tampilkan file yang sudah ada --}}
            <div class="space-y-3">
              @foreach($files as $i => $file)
                @php
                  $name = basename($file);
                  $ext  = pathinfo($file, PATHINFO_EXTENSION);
                  $url  = Storage::url($file);
                @endphp
                <div class="flex items-center justify-between bg-white border rounded px-3 py-2">
                  <div class="flex items-center gap-2">
                    @if(in_array($ext, ['jpg','png','jpeg','gif']))
                      <i class="far fa-image text-orange-500"></i>
                    @elseif($ext==='pdf')
                      <i class="far fa-file-pdf text-red-500"></i>
                    @else
                      <i class="far fa-file-alt text-gray-500"></i>
                    @endif
                    <a href="{{ $url }}" target="_blank" class="text-blue-600 hover:underline">{{ $name }}</a>
                  </div>
                  <div class="flex gap-2">
                    <button type="button" onclick="renameFile('{{ $i }}','{{ pathinfo($name, PATHINFO_FILENAME) }}')"
                            class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                    <button type="button" onclick="deleteFile('{{ $i }}')"
                            class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                  </div>
                </div>
              @endforeach
            </div>

            {{-- Upload bukti baru --}}
            <hr class="my-4">
            <label class="block text-gray-700 font-medium mb-2">Tambah Bukti Pembayaran</label>
            <input type="file" name="bukti_pembayaran[]" multiple
                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
                   class="block w-full text-sm text-gray-600
                          file:py-2 file:px-4 file:rounded file:border-0
                          file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700
                          hover:file:bg-orange-100">
            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, PDF, DOC, XLS (Max 2MB/file)</p>
          </div>
        </div>

        {{-- Buttons --}}
        <div class="flex justify-end gap-4 mt-6">
          <button type="button" onclick="history.back()"
                  class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Kembali</button>
          <button type="submit"
                  class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Form tersembunyi untuk action rename/delete --}}
  <form id="fileActionForm" method="POST" style="display:none">
    @csrf @method('PUT')
    <input type="hidden" name="action_type" id="actionType">
    <input type="hidden" name="file_index"  id="fileIndex">
    <input type="hidden" name="new_name"    id="newFileName">
  </form>

  <script>
    function deleteFile(index){
      Swal.fire({
        title: 'Hapus File?',
        text: "Anda yakin?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
      }).then((res)=>{
        if(res.isConfirmed){
          document.getElementById('actionType').value = 'delete';
          document.getElementById('fileIndex').value  = index;
          document.getElementById('fileActionForm').action = "{{ route('posts.transactions.update', [$post, $transaction]) }}";
          document.getElementById('fileActionForm').submit();
        }
      });
    }

    function renameFile(index, curr){
      Swal.fire({
        title: 'Rename File',
        input: 'text',
        inputValue: curr,
        showCancelButton: true,
        confirmButtonText: 'Simpan'
      }).then((res)=>{
        if(res.isConfirmed){
          document.getElementById('actionType').value = 'rename';
          document.getElementById('fileIndex').value  = index;
          document.getElementById('newFileName').value= res.value;
          document.getElementById('fileActionForm').action = "{{ route('posts.transactions.update', [$post, $transaction]) }}";
          document.getElementById('fileActionForm').submit();
        }
      });
    }
  </script>
</x-layout>
