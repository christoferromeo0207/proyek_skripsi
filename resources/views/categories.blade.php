<x-layout>
    <x-slot:title>Categories</x-slot>

    {{-- ALERT FLASH MESSAGE --}}
    @if (session('success'))
        <div class="mx-6 mt-4 px-4 py-3 rounded bg-green-100 border border-green-400 text-green-800">
            {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="mx-6 mt-4 px-4 py-3 rounded bg-red-100 border border-red-400 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="min-h-screen w-full bg-gradient-to-br from-orange-200 to-orange-400 text-orange-500 p-0 m-0">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        @foreach ($categories as $category)
        <!-- Card for each category -->
        <div class="bg-white rounded-lg shadow-lg p-4 relative">
            <h3 class="text-lg text-orange-400 font-bold mb-2">{{ $category->name }}</h3>
            
            <!-- Display the number of posts in the category -->
            <p class="text-gray-500 mb-2">{{ $category->posts->count() }} {{ Str::singular('Perusahaan', $category->posts->count()) }}</p>

            <!-- Button to open modal -->
            <button data-modal="modal-{{ $category->id }}" class="absolute top-2 right-2 text-gray-500 hover:text-blue-500 transform hover:scale-125 transition-transform duration-200">
                <!-- Default icon -->
                <svg class="default-icon" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" style="fill: rgb(90, 89, 89);">
                    <path d="M20 5h-8.586L9.707 3.293A.997.997 0 0 0 9 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V7c0-1.103-.897-2-2-2zM4 19V7h16l.002 12H4z"></path>
                </svg>
            </button>

            <!-- Modal content -->
            <div id="modal-{{ $category->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-start pt-6 z-50">
                <div class="bg-white rounded-lg px-6 w-full sm:w-3/4 lg:w-2/3 max-w-5xl overflow-y-auto max-h-[90vh]">
                    <h2 class="text-2xl font-bold mb-2 text-center">{{ $category->name }}</h2>

                    <!-- Responsive and scrollable table of posts for this category -->
                    <div class="overflow-x-auto mb-4">
                        <h3 class="text-lg text-gray-800 font-bold text-center mb-4">Daftar Perusahaan Kategori {{ $category->name }}</h3>
                        <table class="table-auto w-full text-left text-gray-800">
                            <thead>
                              <tr>
                                <th class="px-4 py-2 border">Title</th>
                                <th class="px-4 py-2 border">PIC Mitra</th>
                                <th class="px-4 py-2 border">Body</th>
                                <th class="px-4 py-2 border">Action</th>
                              </tr>   
                            </thead>
                            <tbody>
                              @forelse ($category->posts as $post)
                                <tr>
                                  <td class="px-4 py-2 border">{{ $post->title }}</td>
                                  <td class="px-4 py-2 border">{{ $post->pic_mitra }}</td>
                                  <td class="px-4 py-2 border">{{ Str::limit($post->body, 150) }}</td> 
                                  <td class="px-4 py-2 border text-center">
                                    <a href="{{ route('posts.show', $post->slug) }}"
                                       class="inline-block p-1 hover:bg-gray-100 rounded">
                                      <svg xmlns="http://www.w3.org/2000/svg"
                                           width="24" height="24" viewBox="0 0 24 24"
                                           class="w-6 h-6 text-gray-700 hover:text-orange-600">
                                        <path d="M12 9a3.02 3.02 0 0 0-3 3c0 1.642 1.358 3 3 3 1.641 0 3-1.358 3-3 0-1.641-1.359-3-3-3z"/>
                                        <path d="M12 5c-7.633 0-9.927 6.617-9.948 6.684L1.946 12l.105.316C2.073 12.383 4.367 19 12 19s9.927-6.617 9.948-6.684l.106-.316-.105-.316C21.927 11.617 19.633 5 12 5zm0 12c-5.351 0-7.424-3.846-7.926-5C4.578 10.842 6.652 7 12 7c5.351 0 7.424 3.846 7.926 5-.504 1.158-2.578 5-7.926 5z"/>
                                      </svg>
                                    </a>
                                  </td>
                                </tr>
                              @empty
                                <tr>
                                  <td colspan="4"
                                      class="px-4 py-2 border text-center text-gray-500">
                                    Tidak ada perusahaan yang tersedia dalam kategori ini.
                                  </td>
                                </tr>
                              @endforelse
                            </tbody>
                          </table>
                          
                    </div>
                    
                    <!-- Button to Add Data -->
                    <button onclick="openAddDataForm({{ $category->id }})" class="mb-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Add Data
                    </button>

                  <!-- Form to Add Data (Updated) -->
                <div id="add-data-form-{{ $category->id }}" class="hidden mt-4">
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!--Start Form-->
                    <form action="{{ route('addData', $category->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Nama Perusahaan:</label>
                            <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"placeholder="Masukkan Nama Perusahaan" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="slug" class="block text-gray-700 text-sm font-bold mb-2">Slug:</label>
                            <input type="text" name="slug" id="slug" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: nama-perusahaan" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="body" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi:</label>
                            <textarea name="body" id="body" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Deskripsi Perusahaan..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone Number:</label>
                            <input type="text" name="phone" id="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan Phone Number..." required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                            <input type="text" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan Email..." required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="alamat" class="block text-gray-700 text-sm font-bold mb-2">Alamat:</label>
                            <input type="text" name="alamat" id="alamat" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan Alamat..." required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="keterangan_bpjs" class="block text-gray-700 text-sm font-bold mb-2">Keterangan BPJS:</label>
                            <select type="text" name="keterangan_bpjs" id="keterangan_bpjs" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan Keterangan BPJS..." required>
                                <option value="" disabled selected>Menggunakan BPJS?</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="pembayaran" class="block text-gray-700 text-sm font-bold mb-2">Pembayaran:</label>
                            <select name="pembayaran" id="pembayaran" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="" disabled selected>Pilih Metode Pembayaran</option>
                                <option value="Ditagihkan ke Perusahaan">Ditagihkan ke Perusahaan</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Online Payment">Online Payment</option>
                            </select>
                        </div>
                        
                        
                        <div class="mb-4">
                            <label for="tanggal_awal" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Awal:</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan Tanggal Awal..." required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="tanggal_akhir" class="block text-gray-700 text-sm font-bold mb-2">Tanggal Akhir:</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Masukkan Tanggal Akhir..." required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="PIC" class="block text-sm font-medium text-gray-700">PIC</label>
                            <select id="PIC" name="PIC" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                                <option value="">Select a PIC</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('PIC') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('PIC')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="pic_mitra" class="block text-gray-700 text-sm font-bold mb-2">PIC Mitra:</label>
                            <input type="text" name="pic_mitra" id="pic_mitra" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"placeholder="Masukkan PIC Mitra" required>
                        </div>

                        <div>
                            <label class="block text-black-500 font-semibold mb-1">Dokumentasi:</label>
                            
                            <!-- File upload area -->
                            <div id="file-upload-area" class="border-2 border-dashed border-black-300 rounded-lg p-4 text-center cursor-pointer hover:bg-orange-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mt-2 text-black font-medium">Klik untuk mengunggah file</p>
                                <p class="text-xs text-gray-500 mt-1">Format: PNG, JPG, JPEG, PDF (Maks. 2MB)</p>
                                <input type="file" name="file_path[]" id="file-upload-input" class="hidden" accept=".png,.jpg,.jpeg,.pdf" multiple>
                            </div>
                            
                            <div id="selected-files" class="mt-4 space-y-3">
                            </div>
                        </div>

                        
                        <button type="submit" class="px-4 py-2 bg-green-400 text-white rounded hover:bg-green-600">Submit</button>
                    </form>
                    <!--End Form-->
                </div>


                <!-- Close button -->
                <button type="button" onclick="closeModal({{ $category->id }})" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Close
                </button>

                </div>
                
            </div>
        </div>
        @endforeach
    </div>
    <!-- Button Kembali -->
    <div class="mt-4 mb-4 text-start pl-6">
        <a href="{{ '/dashboard' }}" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded shadow hover:bg-gray-300 transition no-underline">Back</a>
    </div>
    

    <script>
        // Function to open add data form inside the modal
        function openAddDataForm(categoryId) {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
            document.getElementById(`add-data-form-${categoryId}`).classList.remove('hidden');
        }
        // Function to close modal
        function closeModal(categoryId) {
            document.getElementById(`modal-${categoryId}`).classList.add('hidden');
        }
        
    </script>

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
                            <a href="${fileUrl}" download class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md mr-2">
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
