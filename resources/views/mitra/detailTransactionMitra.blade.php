<x-layout>
  <x-slot:title>Detail &amp; Edit Transaksi – {{ $transaction->nama_produk }}</x-slot:title>

  {{-- Feedback Messages --}}
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
    // Decode JSON field bukti_pembayaran
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

    // Compute status from approvals
    if ($transaction->approval_rs && $transaction->approval_mitra) {
      $computedStatus = 'Selesai';
    } elseif (! $transaction->approval_rs && ! $transaction->approval_mitra) {
      $computedStatus = 'Dibatalkan';
    } else {
      $computedStatus = 'Proses';
    }
  @endphp

  <div class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 py-10 px-4">
    <div class="max-w-screen-xl mx-auto">
      <form
        action="{{ route('mitra.transactions.update', [$post, $transaction]) }}"
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

        {{-- Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

          {{-- Left & Middle: Read-only + Mitra fields --}}
          <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Read-only fields --}}
            @foreach([
              'Nama Produk'      => $transaction->nama_produk,
              'Merk Produk'      => $transaction->merk,
              'Jumlah'           => $transaction->jumlah,
              'Harga/Satuan'     => number_format($transaction->harga_satuan, 2),
              'Tipe Pembayaran'  => $transaction->tipe_pembayaran,
              'PIC RS'           => optional($transaction->rsUser)->name,
              'Approval RS'      => $transaction->approval_rs ? 'Ya' : 'Tidak',
              'Status'           => $computedStatus,
            ] as $label => $value)
              <div>
                <label class="block text-orange-500 font-semibold">{{ $label }}</label>
                <p class="w-full border rounded px-3 py-2 bg-gray-100">{{ $value }}</p>
              </div>
            @endforeach

            {{-- Editable: PIC Mitra --}}
            <div>
              <label class="block text-orange-500 font-semibold">PIC Mitra</label>
              <input
                type="text"
                name="pic_mitra"
                value="{{ old('pic_mitra', $transaction->pic_mitra) }}"
                class="w-full border rounded px-3 py-2"
                required
              >
            </div>

            {{-- Editable: Approval Mitra --}}
            <div>
              <label class="block text-orange-500 font-semibold">Approval Mitra</label>
              <select
                name="approval_mitra"
                class="w-full border rounded px-3 py-2"
                required
              >
                <option value="1" {{ old('approval_mitra', $transaction->approval_mitra)==1?'selected':'' }}>Ya</option>
                <option value="0" {{ old('approval_mitra', $transaction->approval_mitra)==0?'selected':'' }}>Tidak</option>
              </select>
            </div>

          </div>

          {{-- Right: Bukti Pembayaran --}}
          <div class="bg-gray-50 p-6 rounded border">
            <h2 class="text-xl font-semibold text-orange-600 mb-4">Bukti Pembayaran</h2>

            {{-- Existing Files --}}
            <div class="space-y-3">
              @foreach($files as $i => $file)
                @php
                  $name = basename($file);
                  $ext  = pathinfo($file, PATHINFO_EXTENSION);
                  $url  = asset('storage/' . $file);
                @endphp
                <div class="flex items-center justify-between bg-white border rounded px-3 py-2">
                  <div class="flex items-center gap-2">
                    @if(in_array($ext, ['jpg','jpeg','png','gif']))
                      <i class="far fa-image text-orange-500"></i>
                    @elseif($ext === 'pdf')
                      <i class="far fa-file-pdf text-red-500"></i>
                    @else
                      <i class="far fa-file-alt text-gray-500"></i>
                    @endif
                    <a href="{{ $url }}" target="_blank" class="text-blue-600 hover:underline">{{ $name }}</a>
                  </div>
                  <div class="flex gap-2">
                    <button type="button" onclick="renameFile({{ $i }}, '{{ pathinfo($name, PATHINFO_FILENAME) }}')"
                      class="text-blue-500 hover:text-blue-700" title="Rename"><i class="fas fa-edit"></i></button>
                    <button type="button" onclick="deleteFile({{ $i }})"
                      class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash"></i></button>
                  </div>
                </div>
              @endforeach
            </div>

            {{-- Upload New Files --}}
            <hr class="my-4">
            <label class="block text-gray-700 font-medium mb-2">Tambah Bukti Pembayaran</label>
            <input
              type="file"
              name="bukti_pembayaran[]"
              multiple
              accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
              class="block w-full text-sm text-gray-600
                     file:py-2 file:px-4 file:rounded file:border-0
                     file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700
                     hover:file:bg-orange-100"
            >
            <p class="mt-1 text-xs text-gray-500">
              Format: JPG, PNG, PDF, DOC, XLS (Max 2MB/file)
            </p>
          </div>

        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-4 mt-6">
          <button type="button" onclick="history.back()"
                  class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Kembali</button>
          <button type="submit"
                  class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Hidden form for rename/delete --}}
  <form id="fileActionForm" method="POST" style="display:none">
    @csrf
    @method('PUT')
    <input type="hidden" name="action_type" id="actionType">
    <input type="hidden" name="file_index"  id="fileIndex">
    <input type="hidden" name="new_name"    id="newFileName">
  </form>

  {{-- Rename/Delete handlers --}}
  <script>
    function deleteFile(index) {
      Swal.fire({
        title: 'Hapus File?',
        text: "Anda yakin ingin menghapus file ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!'
      }).then((res) => {
        if (res.isConfirmed) {
          document.getElementById('actionType').value = 'delete';
          document.getElementById('fileIndex').value  = index;
          document.getElementById('fileActionForm').action =
            "{{ route('posts.transactions.update', [$post, $transaction]) }}";
          document.getElementById('fileActionForm').submit();
        }
      });
    }

    function renameFile(index, currName) {
      Swal.fire({
        title: 'Rename File',
        input: 'text',
        inputLabel: 'Nama baru (tanpa ekstensi)',
        inputValue: currName,
        showCancelButton: true,
        confirmButtonText: 'Simpan'
      }).then((res) => {
        if (res.isConfirmed && res.value.trim() !== '') {
          document.getElementById('actionType').value = 'rename';
          document.getElementById('fileIndex').value  = index;
          document.getElementById('newFileName').value= res.value.trim();
          document.getElementById('fileActionForm').action =
            "{{ route('posts.transactions.update', [$post, $transaction]) }}";
          document.getElementById('fileActionForm').submit();
        }
      });
    }
  </script>
</x-layout>
