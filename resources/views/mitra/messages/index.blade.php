<x-layout>
  <x-slot:title>Pesan Terkirim – {{ $post->title }}</x-slot:title>

  <h2 class="text-2xl font-bold text-orange-600">History Pesan Anda</h2>
  <a href="{{ route('mitra.messages.create',$post) }}"
     class="bg-orange-500 text-white px-4 py-2 rounded">+ Kirim Pesan</a>

  <form method="GET" class="mt-4 mb-6 flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Cari subject…"
           class="border rounded p-2 flex-1">
    <button class="bg-orange-500 text-white px-4 py-2 rounded">Cari</button>
  </form>

  <ul class="space-y-4">
    @forelse($msgs as $m)
      <li class="p-4 border {{ $m->is_read?'bg-gray-50':'' }}">
        <div class="flex justify-between">
          <div>
            <strong>{{ $m->subject }}</strong><br>
            <small>Ke: {{ $post->picUser?->name }}</small>
          </div>
          <span class="text-sm">{{ $m->created_at->format('d M Y H:i') }}</span>
        </div>
        <p class="mt-2">{{ Str::limit($m->body,100) }}</p>
      </li>
    @empty
      <li class="text-gray-500">Belum ada pesan terkirim.</li>
    @endforelse
  </ul>
</x-layout>
