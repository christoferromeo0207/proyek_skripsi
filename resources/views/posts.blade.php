<x-layout>
  <x-slot:title> {{ $title }}</x-slot:title>

  <div class="w-full bg-gradient-to-br from-orange-200 to-orange-400 text-white p-0 m-0 flex flex-col">


  <div class="py-4 px-4 mx-auto max-w-screen-xl lg:px-6">
    <div class="mx-auto max-w-screen-md sm:text-center">  
        <form action="/posts">
          
          @if(request('category'))
          <input type="hidden" name="category" value="{{ request('category') }}">
          @endif
          
          @if(request('pic_mitra'))
          <input type="hidden" name="pic_mitra" value="{{ request('pic_mitra') }}">
          @endif

        {{-- <div class="items-center mx-auto mb-3 space-y-4 max-w-screen-sm sm:flex sm:space-y-0">
            <div class="relative w-full">
                <label for="search" class="hidden mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Search</label>
                <input class="block p-3 pl-10 w-full text-sm text-gray-900 bg-orange-50 rounded-lg border border-orange-300 sm:rounded-none sm:rounded-l-lg 
                focus:ring-orange-500 focus:border-orange-500 dark:bg-orange-700 dark:border-orange-600 dark:placeholder-gray-400 dark:text-white 
                dark:focus:ring-orange-500 dark:focus:border-orange-500" placeholder="Search for company" type="search" id="search" name="search" autocomplete="on">
            </div>
            <div>
                <button type="submit" class="py-3 px-5 w-full text-sm font-medium text-center text-white rounded-lg border cursor-pointer
                 bg-orange-500 border-orange-500 sm:rounded-none sm:rounded-r-lg hover:bg-orange-700 focus:ring-4 focus:ring-orange-200
                  dark:bg-orange-600 dark:hover:bg-orange-700 dark:focus:ring-orange-800">Search</button>       
                </div>
          </div> --}}
        </form>
      </div>
    </div>
  

      <!--Button Add Post-->
      <div class="mt-4 ml-48 font-bold">
        <a href="{{ '/categories' }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600 transition no-underline">Add Data</a>
      </div>
    


  <div class="my-4 py-4 px-4 mx-auto max-w-screen-xl lg:py-8 lg:px-0">
    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">

      @forelse ($posts as $post)
        <article class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
          <div class="flex justify-between items-center mb-5 text-gray-500">
            @if($post->category)
              <a href="/posts?category={{ $post->category->slug }}">  
                <span 
                  class="text-sm font-bold inline-flex items-center px-2.5 py-0.5 rounded" 
                  style="
                    background-color: {{ $post->category->color }}30; 
                    color: {{ $post->category->color }};
                    font-weight:;
                  "
                >
                  {{ $post->category->name }}
                </span>
              </a>
            @else
              <span class="text-xs text-gray-500">No Category</span>
            @endif
            <span class="text-sm">{{ $post->created_at->diffForHumans() }}</span> 
          </div>
          
          <a href="/posts/{{ $post->slug }}" class="no-underline hover:underline decoration-none">
            <h2 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
              {{ $post->title }}
            </h2>
          </a>
          
          <p class="mb-5 font-light text-gray-500 dark:text-gray-400">{{ Str::limit($post->body, 100) }}</p>
          
          <div class="flex justify-between items-center">
            @if($post->pic_mitra)
              <span class="font-medium text-sm dark:text-orange-500">
                {{ $post->pic_mitra }}
              </span>
            @else
              <!-- Jika pic_mitra tidak diketahui -->
              <span class="text-sm text-orange-500">-</span>
            @endif
            
            <a href="/posts/{{ $post->slug }}" class="font-medium text-sm text-orange-600 no-underline hover:underline decoration-none">Read More &raquo;</a>
          </div>


        </article>                 
      @empty
        <div>
          <p class="font-semibold text-xl my-4">Tidak Ada Perusahaan Terdaftar</p> 
          <a href="/dashboard" class="block text-orange-600 hover:underline">&laquo; Back to dashboard</a>
        </div>
      @endforelse


    </div>  
  </div>


{{ $posts->links() }}



</x-layout>




<script>
  document.getElementById('addPostBtn').addEventListener('click', function() {
       document.getElementById('addPostForm').classList.toggle('hidden');
   });
   document.getElementById('closePostBtn').addEventListener('click', function() {
       document.getElementById('addPostForm').classList.add('hidden');
   });

</script>


