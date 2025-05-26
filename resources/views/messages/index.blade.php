  <x-layout>
  <x-slot:title>Pesan – {{ $post->title }}</x-slot:title>

  <div class="container mx-auto py-8">
    <div class="bg-orange-100 p-6 rounded">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-orange-400">History Pesan {{ $post->title }}</h2>
        <a href="{{ route('posts.messages.create', $post) }}"
           class="bg-orange-400 text-white px-4 py-2 rounded hover:bg-orange-500 no-underline">
          + Pesan Baru
        </a>
      </div>

      {{-- Search bar --}}
      <form method="GET" action="{{ route('posts.messages.index', $post) }}" class="mb-4">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               placeholder="Cari Pesan..."
               class="border rounded p-2 w-1/2">
        <button type="submit"
                class="bg-orange-400 text-white px-3 py-1 rounded">
          Cari
        </button>
      </form>

      {{-- List pesan --}}
      <ul class="space-y-4">
        @forelse($msgs as $m)
          <li class="p-6 border rounded-xl hover:shadow-lg{{ $m->is_read ? 'bg-orange-50' : '' }}">
            <div class="flex justify-between">
              <div>
                <strong>{{ $m->subject }}</strong><br>
                <small>Dari: {{ $m->sender->name }} ({{ $m->sender->email }})</small>
              </div>
              <span class="text-sm text-gray-500">
                {{ $m->created_at->format('d M Y H:i') }}
              </span>
            </div>
            <p class="mt-2 text-gray-700">
              {{ Str::limit($m->body, 100) }}
            </p>
          </li>
        @empty
          <li class="text-gray-500">Tidak ada pesan.</li>
        @endforelse
      </ul>
    </div>
  </div>
</x-layout>
