{{-- resources/views/messages/create.blade.php --}}
<x-layout>
  <x-slot:title>Pesan Baru – {{ $post->title }}</x-slot:title>

  <div class="w-11/12 md:w-4/5 lg:w-3/4 bg-white/30 rounded-2xl shadow-lg backdrop-blur-md p-6 mx-auto mt-10">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('mitra.informasi.messages.store', $post) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">
      @csrf

      {{-- From Mitra--}}
      <div>
        <label class="block text-orange-600 font-semibold mb-1">From Mitra</label>
        <input type="text"
               value="{{ auth()->user()->email }}"
               disabled
               class="w-full border border-orange-300 rounded-lg p-2 bg-gray-100">
      </div>

      {{-- To Markting--}}
      <div>
        <label class="block text-orange-600 font-semibold mb-1">To Marketing</label>
        <input type="text" disabled
          value="{{ optional($post->picUser)->email }}"
          class="w-full border border-orange-300 rounded-lg p-2 bg-gray-100">
      </div>

      {{-- Subject --}}
      <div>
        <label for="subject" class="block text-orange-600 font-semibold mb-1">Judul Pesan</label>
        <input type="text"
               id="subject"
               name="subject"
               value="{{ old('subject') }}"
               class="w-full border border-orange-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-orange-300">
        @error('subject')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Body --}}
      <div>
        <label for="body" class="block text-orange-600 font-semibold mb-1">Deskripsi</label>
        <textarea id="body"
                  name="body"
                  rows="5"
                  class="w-full border border-orange-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-orange-300">{{ old('body') }}</textarea>
        @error('body')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Attachments --}}
      <div>
        <label class="block text-orange-600 font-semibold mb-1">Upload File (opsional)</label>
        <input type="file"
               name="attachments[]"
               multiple
               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
               class="w-full border border-orange-300 rounded-lg p-2">
        {{-- Validasi array-level --}}
        @error('attachments')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
        {{-- Validasi per-file --}}
        @error('attachments.*')
          <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Actions --}}
      <div class="flex justify-end gap-3">
        <a href="{{ route('posts.messages.index', $post) }}"
           class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition no-underline">
          Batal
        </a>
        <button type="submit"
                class="px-4 py-2 bg-orange-400 hover:bg-orange-500 text-white font-semibold rounded-lg transition">
          Kirim
        </button>
      </div>
    </form>
  </div>
</x-layout>
