<x-layout>
    <x-slot:title>Authors</x-slot>

    <div class="py-4 px-4 mx-auto max-w-screen-xl lg:px-6">
        <div class="mx-auto max-w-screen-md sm:text-center">
            <form action="/authors" class="max-w-md mx-auto">
            
                @if(request('author'))
                <input type="hidden" name="author" value="{{ request('author') }}">
                @endif
                
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="default-search" name="search" class="block w-full pl-10 pr-16 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for article" autocomplete="off" required />
                    <button type="submit" class="text-white absolute inset-y-0 right-0 px-4 py-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-r-lg text-sm dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                </div>
            </form>
        </div>
    </div>
    


    <div class="row layout-top-spacing" id="cancel-row">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="p-4 lg:p-6">
                    <div class="table-responsive">
                        <table id="zero-config" class="table table-striped w-full">
                            <thead>
                                <tr>
                                    <th><b>Id</b></th>
                                    <th><b>Nama</b></th>
                                    <th><b>Username</b></th>
                                    <th><b>Email</b></th>
                                    <th><b>Tanggal Bergabung</b></th>
                                    <th><b>Action</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($authors as $author)
                                <tr>
                                    <td>{{ $author->id }}</td>
                                    <td>{{ $author->name }}</td>
                                    <td>{{ $author->username }}</td>
                                    <td>{{ $author->email }}</td>
                                    <td>{{ $author->created_at->format('d/m/Y') }}</td>
                                    <td class="text-primary">
                                        <button class="edit-button bg-blue-500 hover:text-blue-600 text-white px-4 py-2 rounded" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal{{ $author->id }}">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach

                                @foreach ($authors as $author)
                                <div class="modal fade" id="editModal{{ $author->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $author->id }}" aria-hidden="true" data-bs-backdrop="false">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $author->id }}" style="font-weight: bold;">Edit Data Pengguna</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('updateUser', $author->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label for="name{{ $author->id }}" class="form-label">Nama</label>
                                                        <input type="text" class="form-control" id="name{{ $author->id }}" name="name" value="{{ old('name', $author->name) }}">
                                                    </div>
                                
                                                    <div class="mb-3">
                                                        <label for="username{{ $author->id }}" class="form-label">Username</label>
                                                        <input type="text" class="form-control" id="username{{ $author->id }}" name="username" value="{{ old('username', $author->username) }}">
                                                    </div>
                                
                                                    <div class="mb-3">
                                                        <label for="email{{ $author->id }}" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="email{{ $author->id }}" name="email" value="{{ old('email', $author->email) }}">
                                                    </div>
                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Pagination Links -->
                        <div class="mt-4">
                            {{ $authors->links() }} <!-- Tampilkan pagination -->
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-4 text-start pl-6">
        <a href="{{ route('dashboard') }}" class="inline-block px-4 py-2 pr-8 text-white bg-blue-500 hover:bg-blue-600 rounded no-underline">
            Kembali
        </a>
    </div>
    
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
