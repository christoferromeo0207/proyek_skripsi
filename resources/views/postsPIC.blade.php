{{-- resources/views/postsPIC.blade.php --}}
<x-layout>
  <x-slot:title>Mitra PIC Saya</x-slot>

  <div class="min-vh-100 bg-light py-5">
    <div class="container">

      {{-- Page Header --}}
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
        <h1 class="h3 text-primary mb-3 mb-md-0">Mitra di Bawah PIC Anda</h1>
        <a href="{{ route('posts.index') }}" class="btn btn-sm btn-primary">
          Lihat Semua Mitra
        </a>
      </div>

      {{-- Cards Grid --}}
      <div class="row g-4">
        @forelse($posts as $post)
          <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0">
              
              {{-- Badge & Timestamp --}}
              <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center py-2">
                <span class="badge 
                  @php
                    $color = strtolower($post->category->name ?? 'secondary');
                    // Pilihan warna badge berdasarkan kategori
                    switch($color) {
                      case 'asuransi':   $badgeClass = 'bg-success';    break;
                      case 'bank':       $badgeClass = 'bg-info';       break;
                      case 'hotel':      $badgeClass = 'bg-warning';    break;
                      case 'klinik':     $badgeClass = 'bg-danger';     break;
                      case 'rumah sakit':$badgeClass = 'bg-danger';     break;
                      default:           $badgeClass = 'bg-secondary'; break;
                    }
                  @endphp
                  {{ $badgeClass }}
                ">
                  {{ $post->category->name ?? 'Tanpa Kategori' }}
                </span>
                <small class="text-muted">
                  {{ $post->created_at->diffForHumans() }}
                </small>
              </div>

              {{-- Judul & Deskripsi --}}
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-dark mb-3">
                  {{ $post->title }}
                </h5>
                <p class="card-text text-secondary flex-grow-1">
                  {{ Str::limit($post->excerpt, 120, '...') }}
                </p>
                {{-- Nama PIC --}}
                <p class="mt-3 mb-0 text-muted small">
                  <strong>PIC:</strong> {{ optional($post->picUser)->name ?? 'Belum ada PIC' }}
                </p>
              </div>

              {{-- Tombol Detail --}}
              <div class="card-footer bg-white border-top-0 py-3">
                <a href="{{ route('posts.show', $post->slug) }}" class="btn btn-sm btn-outline-primary w-100">
                  Lihat Detail
                </a>
              </div>

            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="alert alert-secondary text-center mb-0">
              Anda belum memiliki mitra apa pun di bawah PIC Anda.
            </div>
          </div>
        @endforelse
      </div>

      {{-- Paginasi --}}
      @if ($posts->hasPages())
        <div class="mt-5 d-flex justify-content-center">
          {{ $posts->links('vendor.pagination.bootstrap-5') }}
        </div>
      @endif

    </div>
  </div>
</x-layout>
