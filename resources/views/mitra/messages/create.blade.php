<x-layout>
  <x-slot:title>Kirim Pesan – {{ $post->title }}</x-slot:title>

  <form action="{{ route('mitra.messages.store',$post) }}"
        method="POST" enctype="multipart/form-data"
        class="max-w-2xl mx-auto p-6 bg-white rounded shadow space-y-4">
    @csrf
    <div>
      <label class="font-semibold">Subject</label>
      <input name="subject" value="{{ old('subject') }}"
             class="w-full border rounded p-2">
    </div>
    <div>
      <label class="font-semibold">Pesan</label>
      <textarea name="body" rows="5"
                class="w-full border rounded p-2">{{ old('body') }}</textarea>
    </div>
    <div>
      <label class="font-semibold">Lampiran</label>
      <input type="file" name="attachments[]" multiple>
    </div>
    <div class="flex justify-between">
      <a href="{{ route('mitra.messages.index',$post) }}"
         class="px-4 py-2 bg-gray-300 rounded">Batal</a>
      <button class="px-6 py-2 bg-orange-500 text-white rounded">Kirim</button>
    </div>
  </form>
</x-layout>
