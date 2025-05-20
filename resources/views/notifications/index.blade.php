<x-layout>
  <x-slot:title>Notifikasi</x-slot:title>

  <div class="min-h-screen bg-gradient-to-br from-orange-300 to-orange-400 text-white py-12 px-4">

    <div class="mx-auto max-w-4xl bg-white/20 backdrop-blur-md rounded-2xl p-6 space-y-6">

      {{-- Header + Dropdown + Search + Button --}}
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold text-orange-600">Notifikasi</h2>

        <div class="flex flex-wrap items-center gap-3">

          {{-- Dropdown Mitra --}}
          <form id="form-filter" method="GET" action="{{ route('notifications') }}" class="flex items-center gap-2">
            <select name="post"
                    onchange="document.getElementById('form-filter').submit()"
                    class="px-4 py-2 rounded bg-white text-gray-800">
              @foreach($posts as $p)
                <option value="{{ $p->id }}"
                  {{ $p->id == $selectedPost->id ? 'selected' : '' }}>
                  {{ $p->title }}
                </option>
              @endforeach
            </select>

            {{-- Search bar --}}
            <input type="text"
                   name="q"
                   value="{{ $q }}"
                   placeholder="Cari Pesan..."
                   class="px-4 py-2 w-64 rounded bg-white text-gray-800">
            <button type="submit"
                    class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
              Cari
            </button>
          </form>

          
        </div>
      </div>

      {{-- Daftar Pesan --}}
      <div class="bg-white rounded-lg p-4 space-y-4">
        @forelse($messages as $msg)
          <div class="border-2 border-orange-300 rounded-lg bg-white p-4">
            <div class="flex justify-between">
              <div>
                <small class="text-gray-500">To: {{ $selectedPost->title }}</small>
                <h3 class="font-bold text-lg">{{ $msg->subject }}</h3>
                <p class="text-sm text-gray-700">Dari: {{ $msg->sender->name }}</p>
              </div>
              <span class="text-sm text-gray-500">
                {{ $msg->created_at->format('d M Y H:i') }}
              </span>
            </div>
            <p class="mt-2 text-gray-800">{{ $msg->body }}</p>
          </div>
        @empty
          <p class="text-center text-gray-700 py-8">Belum ada pesan untuk mitra ini.</p>
        @endforelse
      </div>

    </div>

  </div>
</x-layout>
