<x-layout>
    {{-- {{ dd($categories->toArray()) }} --}}
    <x-slot:title>Kelola Jadwal Mitra</x-slot:title>

    <div
        class="w-full min-h-screen bg-gradient-to-br from-orange-200 to-orange-400 flex flex-col items-center py-12 px-4">
        <!-- Header Card -->
        <div class="bg-orange-50 px-12 py-6 rounded-xl shadow-md mb-6 text-center max-w-md w-full mt-10">
            <h1 class="text-3xl font-extrabold text-orange-600">Periode Perusahaan</h1>
            <p class="text-base font-semibold text-orange-500 mt-1">per month</p>
        </div>

        <!-- Year Selector -->
        <div class="flex items-center gap-3 text-orange-600 font-semibold mb-8">
            <button onclick="changeYear(-1)" class="text-xl hover:text-orange-800">&#8592;</button>
            <span id="year-display" class="text-lg" data-year="{{ $selectedYear }}">Tahun: {{ $selectedYear }}</span>
            <button onclick="changeYear(1)" class="text-xl hover:text-orange-800">&#8594;</button>
        </div>

        <!-- Table -->
        <div class="w-full max-w-6xl mx-auto overflow-auto bg-white rounded-xl shadow-md">
            <table class="w-full border border-orange-200 text-sm">
                <thead class="bg-orange-300 text-white">
                    <tr class="text-left">
                        <th class="px-4 py-3 border border-white font-semibold">Kategori</th>
                        @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'] as $monthName)
                            <th class="px-4 py-3 border border-white font-semibold text-center">{{ $monthName }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="px-4 py-3 border border-orange-200 text-gray-800 font-medium cursor-pointer"
                                onclick="toggleCompanies('{{ $category->id }}')">
                                {{ $category->name }}
                            </td>

                            @foreach (range(1, 12) as $month)
                                @php
                                    $count = $category
                                        ->posts()
                                        ->where(function ($q) use ($selectedYear, $month) {
                                            $q->whereYear('tanggal_awal', '<', $selectedYear)->orWhere(function (
                                                $qq,
                                            ) use ($selectedYear, $month) {
                                                $qq->whereYear('tanggal_awal', $selectedYear)->whereMonth(
                                                    'tanggal_awal',
                                                    '<=',
                                                    $month,
                                                );
                                            });
                                        })
                                        ->where(function ($q) use ($selectedYear, $month) {
                                            $q->whereYear('tanggal_akhir', '>', $selectedYear)->orWhere(function (
                                                $qq,
                                            ) use ($selectedYear, $month) {
                                                $qq->whereYear('tanggal_akhir', $selectedYear)->whereMonth(
                                                    'tanggal_akhir',
                                                    '>=',
                                                    $month,
                                                );
                                            });
                                        })
                                        ->count();
                                @endphp

                                <!-- Penanda ada perusahaan yang aktif atau tidak -->
                                <td
                                    class="px-2 py-3 text-center border border-orange-200
                                {{ $count > 0 ? 'bg-green-200 text-green-800 font-bold' : '' }}">
                                    {{ $count > 0 ? $count : '' }}
                                </td>
                            @endforeach

                        </tr>

                        <!-- Card Perusahaan-->
                        <tr id="companies-{{ $category->id }}" class="hidden">
                            <td colspan="14" class="px-4 py-3 border border-orange-200">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($category->posts as $post)
                                        <div class="bg-white p-4 rounded-lg shadow-md cursor-pointer"
                                            onclick="openTransactionModal({{ $post->id }})">
                                            <h3 class="text-lg font-semibold text-orange-500">{{ $post->title }}</h3>
                                            @if ($post->tanggal_akhir)
                                                <p class="text-sm text-gray-500">Berakhir pada:
                                                    {{ \Carbon\Carbon::parse($post->tanggal_akhir)->format('d F Y') }}
                                                </p>
                                            @else
                                                <p class="text-sm text-gray-500">Tidak ada tanggal akhir</p>
                                            @endif
                                        </div>

                                        {{-- Hidden transaction content --}}
                                        <div id="transaction-data-{{ $post->id }}" class="hidden">
                                            <div class="grid grid-cols-1 gap-4">
                                                @forelse($post->transactions as $transaction)
                                                    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                                                        <h4 class="font-semibold">{{ $transaction['nama_produk'] }}
                                                        </h4>
                                                        <p class="text-sm">Jumlah: {{ $transaction['jumlah'] }}</p>
                                                        <p class="text-sm">Harga: {{ $transaction['harga_satuan'] }}
                                                        </p>
                                                        <p class="text-sm">Total: {{ $transaction['total_harga'] }}</p>
                                                        <p class="text-sm">Tanggal Mulai:
                                                            {{ $transaction['tanggal_mulai'] ?? '-' }}</p>
                                                        <p class="text-sm">Tanggal Selesai:
                                                            {{ $transaction['tanggal_selesai'] ?? '-' }}</p>
                                                    </div>
                                                @empty
                                                    <p class="text-sm text-gray-500">Belum ada transaksi.</p>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Total --}}
        <div class="mt-6 max-w-6xl mx-auto">
            <span class="text-orange-600 font-bold">
                Jumlah Mitra Kerjasama: {{ $totalMitra }}
            </span>
        </div>

    </div>

    <!-- Modal -->
    <div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
            <h2 class="text-2xl font-bold text-orange-600">Transaksi Perusahaan</h2>
            <div id="transactionContent" class="mt-4">
                <!-- Transaction details will be inserted here -->
            </div>
            <button onclick="closeModal()" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Tutup</button>
        </div>
    </div>

    <script>
        function changeYear(diff) {
            const el = document.getElementById('year-display');
            let yr = parseInt(el.dataset.year, 10) + diff;
            const url = new URL(window.location.href);
            url.searchParams.set('year', yr);
            window.location.href = url;
        }

        function toggleCompanies(categoryId) {
            const row = document.getElementById('companies-' + categoryId);
            row.classList.toggle('hidden');
        }

        function openTransactionModal(postId) {
            const content = document.getElementById('transaction-data-' + postId);
            const modalContent = document.getElementById('transactionContent');
            if (content && modalContent) {
                modalContent.innerHTML = content.innerHTML;
                document.getElementById('transactionModal').classList.remove('hidden');
            }
        }

        function closeModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        }
    </script>

</x-layout>
