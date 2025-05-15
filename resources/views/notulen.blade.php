<x-layout>
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <x-slot:title>{{ $title }}</x-slot>
    <section>
        <div class="grid max-w-screen-xl px-4 py-4 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
            <div class="mr-auto place-self-center lg:col-span-7">
                <a href="{{ route('notulen.index') }}" class="block max-w-2xl mb-2 custom-small-heading font-bold tracking-tight leading-tight dark:text-white text-gray-500 hover:text-gray-600 no-underline">Rapat dan Kegiatan RSPM</a>                         
            </div>                   
        </div>
    </section>

    <section class="dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-visible">
                <!-- Search Form -->
                <div class="flex flex-col md:flex-row items-center justify-between space-y-2 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2 relative">
                        <form action="{{ route('notulen.index') }}" method="GET" class="max-w-lg mx-auto">
                            <div class="flex">
                                <input type="search" name="search" id="search-dropdown" 
                                       value="{{ request('search') }}" 
                                       class="block p-2.5 w-full ..." 
                                       placeholder="Cari Kegiatan" required />
                                       <button type="submit" class="absolute top-0 end-0 p-2.5 text-sm font-medium h-full text-white bg-blue-700 rounded-e-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>      
                                        <span class="sr-only">Search</span>
                                    </button>
                            </div>
                        </form>
                    </div>
                    <div class="flex justify-center md:w-1/2 md:justify-end">
                        <button id="addKegiatanBtn" class="btn btn-primary">Tambah Kegiatan</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <br>

  <!-- Form for Adding Kegiatan -->
    <div id="addKegiatanForm" class="hidden mt-4 max-w-5xl mx-auto bg-blue-50 p-6 rounded-lg shadow-lg dark:bg-gray-800">
        <h2 class="text-2xl font-bold mb-6 text-blue-900 dark:text-white">Tambah Kegiatan</h2>
        <form action="{{ route('notulen.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="mb-4">
                <label for="pertemuan" class="form-label block text-blue-700 dark:text-gray-300">Pertemuan</label>
                <input type="text" class="form-control block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="pertemuan" name="pertemuan" required>
            </div>

            <div class="mb-4">
                <label for="tanggal" class="form-label block text-blue-700 dark:text-gray-300">Tanggal</label>
                <input type="date" class="form-control block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="tanggal" name="tanggal" required>
            </div>

            <div class="mb-4">
                <label for="unit" class="form-label block text-blue-700 dark:text-gray-300">Unit</label>
                <select class="form-select block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="unit" name="unit" required onchange="toggleCustomUnit()">
                    <option value="" disabled selected>Pilih Unit</option>
                    <option value="IT">IT</option>
                    <option value="Marketing">Marketing</option>
                    <option value="SDM">SDM</option>
                    <option value="Finance">Finance</option>
                    <option value="Keperawatan">Keperawatan</option>
                    <option value="unit_lainnya">Unit Lainnya</option>
                </select>
            </div>

            <!-- Custom Unit Input -->
            <div class="mb-4" id="custom-unit-div" style="display: none;">
                <label for="custom_unit" class="form-label block text-blue-700 dark:text-gray-300">Masukkan Unit Baru</label>
                <input type="text" class="form-control block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="custom_unit" name="custom_unit">
                <button type="button" class="btn btn-primary mt-2 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" onclick="addCustomUnit()">Tambah Unit</button>
            </div>

            <div class="mb-4">
                <label for="jabatan" class="form-label block text-blue-700 dark:text-gray-300">Jabatan / Posisi</label>
                <input type="text" class="form-control block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="jabatan" name="jabatan" required>
            </div>

            <div class="mb-4">
                <label for="status" class="form-label block text-blue-700 dark:text-gray-300">Status</label>
                <select class="form-select block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="status" name="status" required>
                    <option value="" disabled selected>Status</option>
                    <option value="belum">Belum</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="jenis" class="form-label block text-blue-700 dark:text-gray-300">Lokasi</label>
                <select class="form-select block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="jenis" name="jenis" required onchange="toggleCustomLocation()">
                    <option value="" disabled selected>Lokasi</option>
                    <option value="daring">Daring</option>
                    <option value="Ruang Orchid(Gedung A)">Ruang Orchid(Gedung A)</option>
                    <option value="Ruang Mawar(Gedung B)">Ruang Mawar(Gedung B)</option>
                    <option value="Ruang Melati(Gedung C)">Ruang Melati(Gedung C)</option>
                    <option value="Ruang Edelwys(Gedung D)">Ruang Edelwys(Gedung D)</option>
                    <option value="lokasi_lainnya">Lokasi Lainnya</option>
                </select>
            </div>
        
            <!-- Custom Location Input -->
            <div class="mb-4" id="custom-location-div" style="display: none;">
                <label for="custom_location" class="form-label block text-blue-700 dark:text-gray-300">Masukkan Lokasi Baru</label>
                <input type="text" class="form-control block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="custom_location" name="custom_location">
                <button type="button" class="btn btn-primary mt-2 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600" onclick="addCustomLocation()">Tambah Lokasi</button>
            </div>
        

            <div class="mb-4">
                <label for="nama" class="form-label block text-blue-700 dark:text-gray-300">Nama PIC</label>
                <input type="text" class="form-control block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="nama" name="nama" required>
            </div>

            <div class="mb-4">
                <label for="no_hp" class="form-label block text-blue-700 dark:text-gray-300">No HP</label>
                <input type="text" class="form-control block w-full border border-blue-300 rounded-md p-2 bg-blue-100 text-blue-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" id="no_hp" name="no_hp" required>
            </div>

            <div class="flex justify-between mt-6">
                <button type="submit" class="btn btn-primary px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">Tambah</button>
                <button type="button" id="closeKegiatanFormBtn" class="btn btn-secondary px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md">Tutup</button>
            </div>
        </form>
    </div>


    <br>

    <!-- Table of Notulens -->
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg max-w-5xl mx-auto custom-table-width">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <!-- Table Head -->
            <thead class="text-xs text-gray-700 uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3 w-24">Tanggal</th> 
                    <th scope="col" class="px-6 py-3">Pertemuan</th>
                    <th scope="col" class="px-6 py-3">Nama Notulen</th>
                    <th scope="col" class="px-4 py-3 w-32">Unit</th>
                    <th scope="col" class="px-6 py-3">Jabatan</th>
                    <th scope="col" class="px-6 py-3">No HP</th>
                    <th scope="col" class="px-6 py-3">Jenis(Lokasi)</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-4 py-3 w-48 text-center">Aksi</th> 
                </tr>
            </thead>
            <!-- Table Body -->
            <tbody>
                @foreach($notulens as $notulen)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-4 py-4">{{ $notulen->tanggal }}</td>
                    <td class="px-6 py-4">{{ $notulen->pertemuan }}</td>
                    <td class="px-6 py-4">{{ $notulen->nama }}</td>
                    <td class="px-4 py-4">{{ $notulen->unit }}</td>
                    <td class="px-6 py-4">{{ $notulen->jabatan }}</td>
                    <td class="px-6 py-4">{{ $notulen->no_hp }}</td>
                    <td class="px-6 py-4">{{ $notulen->jenis }}</td>    
                    <td class="px-6 py-4">
                        <span class="{{ $notulen->status == 'selesai' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} px-2 py-1 rounded">
                            {{ $notulen->status }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('notulen.show', $notulen->id) }}" class="text-white bg-yellow-300 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-xs px-2 py-1.5 dark:bg-yellow-500 dark:hover:bg-yellow-500 focus:outline-none dark:focus:ring-yellow-500 no-underline">View</a>
                            <a href="{{ route('notulen.edit', $notulen->id) }}" class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-xs px-2 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 no-underline">Edit</a>
                            <form id="delete-form-{{ $notulen->id }}" action="{{ route('notulen.destroy', $notulen->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete({{ $notulen->id }})" class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-xs px-2 py-1.5 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">Delete</button>
                            </form>
                        </div>
                    </td>                          
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <br>

    <div class="flex justify-center items-center">
        <div>
            {{ $notulens->links('pagination::bootstrap-5') }}
        </div>
    </div>
    

    <br>
    
    <footer class="pb-8">
        <div class="mx-auto max-w-screen-xl">
            <hr class="my-2 border-gray-400 sm:mx-auto dark:border-gray-700 lg:my-4" />
            <div class="sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2024 | RSU Prima Medika</a>
                </span>                
                <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
                    <a href="https://www.primamedika.com/" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="m12.75 20.66 6.184-7.098c2.677-2.884 2.559-6.506.754-8.705-.898-1.095-2.206-1.816-3.72-1.855-1.293-.034-2.652.43-3.963 1.442-1.315-1.012-2.678-1.476-3.973-1.442-1.515.04-2.825.76-3.724 1.855-1.806 2.201-1.915 5.823.772 8.706l6.183 7.097c.19.216.46.34.743.34a.985.985 0 0 0 .743-.34Z"/>
                        </svg>
                    </a>                                                    
                    <a href="https://www.facebook.com/rsuprimamedika/?locale=id_ID" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="https://www.instagram.com/primamedikahospital/?hl=en" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="https://play.google.com/store/apps/details?id=com.haimed.primamedika&hl=id" class="text-gray-500 hover:text-gray-900 dark:hover:text-white">
                        <svg class="w-5 h-5 fill-current" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd"/>
                        </svg>
                    </a>                                      
                </div>
            </div>
        </div>
    </footer>

</x-layout>

<script>

    document.getElementById('addKegiatanBtn').addEventListener('click', function() {
        document.getElementById('addKegiatanForm').classList.toggle('hidden');
    });
    document.getElementById('closeKegiatanFormBtn').addEventListener('click', function() {
        document.getElementById('addKegiatanForm').classList.add('hidden');
    });

    function toggleCustomLocation() {
        const locationSelect = document.getElementById('jenis');
        const customLocationDiv = document.getElementById('custom-location-div');
        customLocationDiv.style.display = locationSelect.value === 'lokasi_lainnya' ? 'block' : 'none';
    }

    function addCustomLocation() {
        const customLocationInput = document.getElementById('custom_location');
        const customLocation = customLocationInput.value.trim();
        const selectElement = document.getElementById('jenis');

        if (customLocation) {
            // Create a new option element
            const newOption = document.createElement('option');
            newOption.value = customLocation;
            newOption.text = customLocation; 

            // Add the new option to the dropdown
            selectElement.add(newOption);

            // Reset the input field and hide it
            customLocationInput.value = '';
            selectElement.value = customLocation; 
            toggleCustomLocation();

            // Set the custom location value into the hidden input for form submission
            document.getElementById('hidden-custom-location').value = customLocation;
        } else {
            alert("Silakan masukkan lokasi yang valid.");
        }
    }

    function toggleCustomUnit() {
        const unitSelect = document.getElementById('unit');
        const customUnitDiv = document.getElementById('custom-unit-div');
        customUnitDiv.style.display = unitSelect.value === 'unit_lainnya' ? 'block' : 'none';
    }

    function addCustomUnit() {
        const customUnitInput = document.getElementById('custom_unit');
        const customUnit = customUnitInput.value.trim();
        const selectElement = document.getElementById('unit');

        if (customUnit) {
            // Create a new option element
            const newOption = document.createElement('option');
            newOption.value = customUnit;
            newOption.text = customUnit; 

            // Add the new option to the dropdown
            selectElement.add(newOption);

            // Reset the input field and hide it
            customUnitInput.value = '';
            selectElement.value = customUnit; 
            toggleCustomUnit();

            // Set the custom location value into the hidden input for form submission
            document.getElementById('hidden-custom-unit').value = customUnit;
        } else {
            alert("Silakan masukkan Unit yang valid.");
        }
    }


    function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    })
    }




</script>

<style>
    .mt-4 {
    margin-top: 1rem; /* Adjust as needed */
    }

    .mb-2 {
        margin-bottom: 0.5rem; /* Adjust for spacing */
    }

</style>
