
<x-layout>
  <x-slot:title>Notifikasi</x-slot:title>

  <div class="min-h-screen bg-gradient-to-br from-orange-300 to-orange-400 text-white py-12 px-4">
    <div class="mx-auto max-w-4xl bg-white/20 backdrop-blur-md rounded-2xl p-6 space-y-6">

      {{-- Header + Dropdown + Search --}}
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h2 class="text-2xl font-bold text-orange-600">Notifikasi</h2>
        <form id="form-filter" method="GET" action="{{ route('notifications') }}" class="flex items-center gap-2">
          {{-- Dropdown Mitra --}}
          <select name="post"
                  onchange="document.getElementById('form-filter').submit()"
                  class="px-4 py-2 rounded bg-white text-gray-800">
            @foreach($posts as $p)
              <option value="{{ $p->id }}" {{ $p->id == $selectedPost->id ? 'selected' : '' }}>
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

      {{-- Daftar Pesan --}}
      <div class="bg-white rounded-lg p-4 space-y-4">
        @forelse($messages as $msg)
          <div class="border-2 border-orange-300 rounded-lg bg-white p-6">
            <div class="flex justify-between items-start">
              <div class="space-y-1">
                <div class="text-gray-500 text-sm">To: {{ $selectedPost->title }}</div>
                <div class="text-gray-500 text-sm">Dari: {{ $msg->sender->name }}</div>
                <h3 class="text-xl font-semibold text-orange-600">{{ $msg->subject }}</h3>
              </div>
              <div class="text-gray-500 text-xs">{{ $msg->created_at->format('d M Y H:i') }}</div>
            </div>
            <div class="mt-4 text-gray-800 leading-relaxed">
              {{ $msg->body }}
            </div>
          </div>
        @empty
          <p class="text-center text-gray-700 py-8">Belum ada pesan untuk mitra ini.</p>
        @endforelse
      </div>

      
          {{-- Activity Log --}}
          <div class="mt-8 bg-white rounded-lg p-4 space-y-6">
            <h2 class="text-xl font-bold text-orange-600 mb-4">Activity Log</h2>

            @forelse($activities as $act)
              <div class="border-2 border-gray-300 rounded-lg bg-white p-6 space-y-4">
                <div class="flex justify-between items-start">
                  <div class="space-y-1">
                    <p class="text-gray-800 font-semibold">{{ $act->description }}</p>

                    @if($act->causer)
                      <p class="text-orange-500 text-sm">
                        Oleh: {{ $act->causer->name }} ({{ $act->causer->jabatan }})
                      </p>
                    @endif

                    @if($act->properties->has('attributes'))
                      <div class="mt-2">
                        <p class="text-gray-600 text-sm font-medium">Perubahan:</p>
                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                          @foreach($act->properties['attributes'] as $field => $new)
                            @php
                              $old = data_get($act->properties, "old.{$field}");
                              $newValue = is_array($new) ? json_encode($new, JSON_UNESCAPED_UNICODE) : $new;
                              $oldValue = is_array($old) ? json_encode($old, JSON_UNESCAPED_UNICODE) : $old;
                            @endphp
                            <li>
                              <span class="font-semibold">{{ ucfirst($field) }}:</span>
                              @if(! is_null($oldValue))
                                <span class="text-gray-400">{{ $oldValue }}</span>
                                &nbsp;→&nbsp;
                              @endif
                              <span>{{ $newValue }}</span>
                            </li>
                          @endforeach
                        </ul>
                      </div>
                    @endif
                  </div>

                  <span class="text-gray-500 text-xs">
                    {{ $act->created_at->format('d M Y H:i') }}
                  </span>
                </div>
              </div>
            @empty
              <p class="text-center text-gray-700 py-8">
                Belum ada aktivitas untuk mitra ini.
              </p>
            @endforelse
          </div>



    </div>
  </div>

</x-layout>
