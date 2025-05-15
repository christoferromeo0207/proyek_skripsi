<x-layout>
    <x-slot:title>View Notulen</x-slot>

    @if (session('success'))
    <div class="bg-green-500 text-white p-4 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Detail Notulen</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <!-- Notulen Details -->
            <p class="text-lg mb-2"><strong>Tanggal:</strong> {{ $notulen->tanggal }}</p>
            <p class="text-lg mb-2"><strong>Pertemuan:</strong> {{ $notulen->pertemuan }}</p>
            <p class="text-lg mb-2"><strong>Nama:</strong> {{ $notulen->nama }}</p>
            <p class="text-lg mb-2"><strong>Unit:</strong> {{ $notulen->unit }}</p>
            <p class="text-lg mb-2"><strong>Jabatan:</strong> {{ $notulen->jabatan }}</p>
            <p class="text-lg mb-2"><strong>No HP:</strong> {{ $notulen->no_hp }}</p>
            <p class="text-lg mb-2"><strong>Lokasi:</strong> {{ $notulen->jenis }}</p>
            <p class="text-lg mb-2"><strong>Hasil:</strong> {{ $notulen->hasil }}</p>
            <p class="text-lg mb-2"><strong>Status:</strong> {{ $notulen->status }}</p>

            @if ($notulen->file_path)
            @php
                // Get the original filename from the file path
                $filename = basename($notulen->file_path); // Extracts the filename from the path
            @endphp
            <div class="mb-6">
                <p class="m-0 text-lg"><strong>Uploaded File:</strong></p>
                <div class="flex items-center justify-between mt-2 border border-gray-300 rounded-lg p-4">
                    <div class="flex items-center space-x-4">
                        <a href="{{ Storage::url($notulen->file_path) }}" target="_blank" class="relative group flex items-center">
                            <i class="fas fa-file-alt text-blue-600 hover:text-blue-800 text-3xl" 
                               title="{{ $filename }}"></i> 
                            <span class="ml-2 text-lg text-gray-700">{{ $filename }}</span>
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-edit text-blue-600 cursor-pointer text-2xl" onclick="toggleRenameField()"></i> 
                        <form action="{{ route('notulens.deleteFile', $notulen->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-3xl">
                                <i class="fas fa-trash"></i> 
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            <!-- Rename File Form -->
            <form action="{{ route('notulens.renameFile', $notulen->id) }}" method="POST" class="mt-4" id="renameForm" style="display: none;">
                @csrf
                <div class="flex items-center">
                    <input type="text" name="new_name" placeholder="Enter new file name" class="border border-gray-300 rounded-lg px-4 py-2 mr-2" required>
                    <button type="submit" class="bg-blue-500 text-white rounded-lg px-4 py-2 hover:bg-blue-600">Rename</button>
                    <br>
                </div>
            </form>
            @endif
            <br>

            <!-- Back to List Button -->
            <a href="{{ route('notulens.index') }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded shadow hover:bg-gray-300 transition no-underline">Back to List</a>
        </div>
    </div>

    <script>
        function toggleRenameField() {
            var form = document.getElementById('renameForm');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</x-layout>
