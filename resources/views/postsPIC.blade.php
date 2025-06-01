{{-- resources/views/postsPIC.blade.php --}}
<x-layout>
  <x-slot:title>Mitra PIC Saya</x-slot:title>

  <div class="w-full bg-gradient-to-br from-orange-200 to-orange-400 text-white flex flex-col">
    <div class="py-4 px-4 mx-auto max-w-screen-xl lg:px-6">

      {{-- Search & Category Filters --}}
      <form action="{{ route('posts.index') }}" method="GET" class="space-y-4">
        {{-- Search bar --}}
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Cari Nama Perusahaan..."
          class="block w-full md:w-2/3 mx-auto p-3 pl-4 text-gray-900 bg-white rounded-full border border-transparent focus:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-300 transition"
        />

        {{-- Category pills --}}
        <div class="flex flex-wrap justify-center gap-2">
          {{-- “All” --}}
          <a
            href="{{ route('posts.index', request()->except(['category','page'])) }}"
            class="px-3 py-1 rounded-full text-sm font-semibold
                   {{ ! request()->filled('category') 
                       ? 'bg-white text-orange-600' 
                       : 'bg-white bg-opacity-20 text-orange-600 no-underline ' }}
                   hover:bg-white hover:text-orange-600 transition no-underline"
          >
            Semua Kategori
          </a>

          @foreach($categories as $cat)
            <a
              href="{{ route('posts.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}"
              class="px-3 py-1 rounded-full text-sm font-semibold
                     {{ request('category') == $cat->id 
                         ? 'bg-white text-orange-600' 
                         : 'bg-white bg-opacity-30 text-orange-600 no-underline' }}
                     hover:bg-orange-200 hover:text-orange-600 transition"
            >
              {{ $cat->name }}
            </a>
          @endforeach
        </div>
      </form>
    </div>

    {{-- Add Data Button --}}
    <div class="mt-4 ml-4 md:ml-48 font-bold">
      <a
        href="{{ route('categories.index') }}"
        class="inline-block bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600 transition no-underline"
      >
        Add Data
      </a>
    </div>

    {{-- My PIC Posts Section --}}
    <div class="my-8 px-4 mx-auto max-w-screen-xl lg:px-0">
      <h2 class="text-2xl font-bold text-orange-500 mb-4">Mitra di Bawah PIC Anda</h2>

      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse($myPosts as $post)
          <article class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-5">
              @if($post->category)
                <a href="{{ route('posts.index', ['category' => $post->category->id] + request()->except('page')) }}">
                  <span
                    class="text-sm font-bold inline-flex items-center px-2.5 py-0.5 rounded"
                    style="background-color: {{ $post->category->color }}30; color: {{ $post->category->color }};"
                  >
                    {{ $post->category->name }}
                  </span>
                </a>
              @else
                <span class="text-xs text-gray-500">No Category</span>
              @endif
              <span class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
            </div>

            <a href="{{ route('posts.show', $post->slug) }}" class="no-underline hover:underline">
              <h2 class="mb-2 text-xl font-bold text-gray-900">{{ $post->title }}</h2>
            </a>
            <p class="mb-5 text-gray-600">{{ Str::limit($post->body, 100) }}</p>

            <div class="flex justify-between items-center">
              <span class="text-sm text-orange-600 font-medium">
                {{ optional($post->picUser)->name ?? '-' }}
              </span>
            </div>
          </article>
        @empty
          <div class="col-span-3 text-center text-gray-200">
            Anda belum memiliki mitra di bawah PIC Anda.
          </div>
        @endforelse
      </div>

      {{-- Pagination for My PIC Posts --}}
      @if($myPosts->hasPages())
        <div class="px-4 pt-8 pb-4 mx-auto max-w-screen-xl">
          {{ $myPosts->withQueryString()->links() }}
        </div>
      @endif
    </div>

    {{-- Separator --}}
    <div class="border-b border-white/50 mx-4 md:mx-48"></div>

    {{-- Other Posts Section --}}
    <div class="my-8 px-4 mx-auto max-w-screen-xl lg:px-0">
      <h2 class="text-2xl font-bold text-orange-500 mb-4">Post Lainnya</h2>

      <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @forelse($otherPosts as $post)
          <article class="p-6 bg-white rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-5">
              @if($post->category)
                <a href="{{ route('posts.index', ['category' => $post->category->id] + request()->except('page')) }}">
                  <span
                    class="text-sm font-bold inline-flex items-center px-2.5 py-0.5 rounded"
                    style="background-color: {{ $post->category->color }}30; color: {{ $post->category->color }};"
                  >
                    {{ $post->category->name }}
                  </span>
                </a>
              @else
                <span class="text-xs text-gray-500">No Category</span>
              @endif
              <span class="text-sm text-gray-500">{{ $post->created_at->diffForHumans() }}</span>
            </div>

            <a href="{{ route('posts.show', $post->slug) }}" class="no-underline hover:underline">
              <h2 class="mb-2 text-xl font-bold text-gray-900">{{ $post->title }}</h2>
            </a>
            <p class="mb-5 text-gray-600">{{ Str::limit($post->body, 100) }}</p>

            <div class="flex justify-between items-center">
              <span class="text-sm text-orange-600 font-medium">
                {{ optional($post->picUser)->name ?? '-' }}
              </span>
            </div>
          </article>
        @empty
          <div class="col-span-3 text-center text-gray-200">
            Tidak ada Post Lainnya.
          </div>
        @endforelse
      </div>

      {{-- Pagination for Other Posts --}}
      @if($otherPosts->hasPages())
        <div class="px-4 pt-8 pb-8 mx-auto max-w-screen-xl">
          {{ $otherPosts->withQueryString()->links() }}
        </div>
      @endif
    </div>
  </div>
</x-layout>
