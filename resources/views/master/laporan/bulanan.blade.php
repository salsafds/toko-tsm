@extends('layouts.appmaster')
@section('title', 'Laporan Bulanan')
@section('content')
   <!-- Header + Filter Periode -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4 pt-2">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Laporan Toko Koperasi</h1>
        <p class="text-sm text-gray-500 mt-1">Periode {{ $periodeTeks }}</p>
    </div>
    <!-- FORM FILTER -->
    <form method="GET" class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
        <!-- Dropdown Periode -->
        <div class="w-64 sm:w-64">
            <select name="periode" id="periode" onchange="this.form.submit()"
                    class="w-full rounded-md border border-gray-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="" disabled {{ !$periode ? 'selected' : '' }}>-- Pilih Periode --</option>
                <option value="7hari" {{ $periode == '7hari' ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="1bulan_terakhir" {{ $periode == '1bulan_terakhir' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                <option value="3bulan" {{ $periode == '3bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
            </select>
        </div>
        <!-- Kotak Kanan: STATIS -->
        <div class="w-72">
            <div class="flex items-center border border-gray-200 rounded-md overflow-hidden bg-white shadow-sm">
                <!-- Icon Kalender -->
                <label for="input-bulan"
                       class="flex items-center justify-center w-12 h-11 cursor-pointer transition-colors
                              {{ $periode === 'bulanan' ? 'text-gray-700 hover:bg-gray-50' : 'text-gray-400 cursor-not-allowed' }}"
                       onclick="{{ $periode === 'bulanan' ? '' : 'event.preventDefault();' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </label>
                <!-- Konten Tengah -->
                <div class="flex-1">
                    <!-- Input Bulan (bulanan) -->
                    <input type="month" id="input-bulan" name="bulan"
                           value="{{ request('bulan') ?? now()->format('Y-m') }}"
                           onchange="this.form.submit()"
                           class="w-full px-3 py-2.5 text-sm text-gray-800 outline-none {{ $periode === 'bulanan' ? 'block' : 'hidden' }}">
                    <!-- Dropdown Tahun (tahunan) -->
                    <select name="tahun" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 text-sm text-gray-800 outline-none {{ $periode === 'tahunan' ? 'block' : 'hidden' }}">
                        @for($y = now()->year; $y >= now()->year - 10; $y--)
                            <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <!-- Teks Rentang (7hari, 1bulan_terakhir, 3bulan) -->
                    <div class="px-3 py-2.5 text-sm font-medium text-gray-700 truncate {{ in_array($periode, ['7hari','1bulan_terakhir','3bulan']) ? 'block' : 'hidden' }}">
                        @if($periode == '7hari')
                            {{ now()->subDays(6)->format('j') }} – {{ now()->format('j F Y') }}
                        @elseif($periode == '1bulan_terakhir')
                            {{ now()->subDays(29)->format('j M') }} – {{ now()->format('j M Y') }}
                        @elseif($periode == '3bulan')
                            {{ now()->subMonths(2)->translatedFormat('M') }} – {{ now()->translatedFormat('M Y') }}
                        @endif
                    </div>
                    <!-- Placeholder -->
                    <div class="px-3 py-2.5 text-sm text-gray-400 {{ !$periode || !in_array($periode, ['7hari','1bulan_terakhir','3bulan','bulanan','tahunan']) ? 'block' : 'hidden' }}">
                        -- Pilih periode terlebih dahulu --
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

    <!-- 4 Kartu Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Omzet Penjualan</div>
            <div class="mt-2 text-xl font-bold text-gray-900">Rp {{ number_format($omzet, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Laba Kotor</div>
            <div class="mt-2 text-xl font-bold text-green-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Margin {{ $marginPersen }}%</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Nilai Stok Akhir</div>
            <div class="mt-2 text-xl font-bold text-purple-600">Rp {{ number_format($nilaiStokAkhir, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Stok Kritis</div>
            <div class="mt-2 text-xl font-bold text-red-600">{{ $stokKritis->count() }} barang</div>
        </div>
    </div>

    <!-- Ringkasan Pembelian & HPP -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- Ringkasan Pembelian -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 mb-5">Ringkasan Pembelian</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Nota Pembelian (periode ini)</span>
                    <span class="text font-bold text-gray-800">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                    <span class="text-gray-600 flex items-center">
                        Barang Masuk Stok (sudah diterima)
                    </span>
                    <span class="text font-bold text-green-600">Rp {{ number_format($pembelianMasuk, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <!-- HPP & Laba Kotor -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-sm font-semibold text-gray-700 mb-5">HPP & Laba Kotor</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">HPP (Biaya Pokok Penjualan)</span>
                    <span class="text-lg font-bold text-orange-600">Rp {{ number_format($hpp, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t-2 border-green-100">
                    <span class="font-bold text-gray-800">Laba Kotor Bersih</span>
                    <span class="text-lg font-bold text-green-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Stok Kritis -->
    @if($stokKritis->count() > 0)
    <div class="bg-white rounded-lg shadow-sm mb-6 border border-gray-200 overflow-x-auto">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-sm font-medium text-gray-700">Stok Kritis (Stok < 10)</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-500 uppercase">
                    <th class="px-5 py-3 text-center border-r border-gray-200">Kode</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Nama Barang</th>
                    <th class="px-5 py-3 text-center">Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($stokKritis as $b)
                <tr class="hover:bg-red-50">
                    <td class="px-5 py-4 font-mono text-sm border-r border-gray-100">{{ $b->id_barang }}</td>
                    <td class="px-5 py-4 border-r border-gray-100">{{ $b->nama_barang }}</td>
                    <td class="px-5 py-4 text-left font-bold text-red-600">{{ $b->stok }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- 10 Barang Terlaris + Bar Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white p-5 rounded-lg shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-4">10 Barang Terlaris (Qty)</h2>
            <canvas id="chartTerlaris" class="h-80"></canvas>
        </div>
        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
            <h2 class="text-sm font-medium text-gray-700 mb-4">Detail Terlaris</h2>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left border-r border-gray-200">Rank</th>
                        <th class="px-4 py-2 text-left border-r border-gray-200">Barang</th>
                        <th class="px-4 py-2 text-right border-r border-gray-200">Terjual</th>
                        <th class="px-4 py-2 text-right">Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($terlaris as $i => $item)
                    <tr>
                        <td class="px-4 py-3 font-bold text-indigo-600 border-r border-gray-100">#{{ $i+1 }}</td>
                        <td class="px-4 py-3 border-r border-gray-100">{{ Str::limit($item->nama_barang, 28) }}</td>
                        <td class="px-4 py-3 text-right font-bold border-r border-gray-100">{{ $item->qty }}</td>
                        <td class="px-4 py-3 text-right text-green-600">Rp {{ number_format($item->omzet, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada penjualan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div id="riwayat-transaksi" class="bg-white rounded-lg shadow-sm mb-6 border border-gray-200 overflow-x-auto">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-medium text-gray-700">Riwayat Transaksi</h2>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <label class="text-sm text-gray-600">Jenis:</label>
                    <select name="jenis" onchange="this.form.submit()" class="rounded-md border border-gray-200 px-3 py-1 text-sm">
                        <option value="all" {{ $jenis == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="jual" {{ $jenis == 'jual' ? 'selected' : '' }}>Jual</option>
                        <option value="beli" {{ $jenis == 'beli' ? 'selected' : '' }}>Beli</option>
                    </select>
                    <input type="hidden" name="periode" value="{{ $periode }}">
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    <input type="hidden" name="sort_tanggal" value="{{ $sortTanggal }}">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                </form>
                <a href="{{ route('master.laporan.bulanan', ['format' => 'pdf'] + request()->query()) }}" class="bg-red-500 text-white px-3 py-1 rounded text-sm">Print PDF</a>
                <a href="{{ route('master.laporan.bulanan', ['format' => 'excel'] + request()->query()) }}" class="bg-green-500 text-white px-3 py-1 rounded text-sm">Print Excel</a>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left border-r border-gray-200">
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-500 uppercase tracking-wider">Tanggal</span>
                            <div class="flex flex-col -space-y-1">
                                <a href="{{ request()->fullUrlWithQuery(['sort_tanggal' => 'desc']) }}"
                                   class="leading-none {{ $sortTanggal === 'desc' ? 'text-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 10l7-7 7 7z"/></svg>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort_tanggal' => 'asc']) }}"
                                   class="leading-none {{ $sortTanggal === 'asc' ? 'text-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17 10l-7 7-7-7z"/></svg>
                                </a>
                            </div>
                        </div>
                    </th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">ID</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Jenis</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Sumber</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Pihak</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Kasir</th>
                    <th class="px-5 py-3 text-center">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($transaksi as $t)
                <tr>
                    <td class="px-5 py-4 border-r border-gray-100">{{ $t->tanggal->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 border-r border-gray-100">{{ $t->id }}</td>
                    <td class="px-5 py-4 border-r border-gray-100">
                        <span class="px-2 py-1 rounded text-xs font-medium {{ $t->jenis == 'penjualan' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ $t->jenis == 'penjualan' ? 'Jual' : 'Beli' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 border-r border-gray-100">
                        @if($t->sumber == 'toko')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Toko</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Marketplace</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 border-r border-gray-100">{{ $t->nama_pihak }}</td>
                    <td class="px-5 py-4 text-gray-600 border-r border-gray-100">{{ $t->kasir }}</td>
                    <td class="px-5 py-4 text-left font-bold {{ $t->jenis == 'penjualan' ? 'text-green-600' : 'text-orange-600' }}">
                        Rp {{ number_format($t->total, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2 px-5 pb-4">
            <div class="text-xs sm:text-sm text-gray-600">
                @if($transaksi->total())
                    Menampilkan {{ $transaksi->firstItem() }} sampai {{ $transaksi->lastItem() }} dari {{ $transaksi->total() }} data
                @endif
            </div>
            <div>
                @if($transaksi->hasPages())
                    <div class="flex items-center space-x-1">
                        @if($transaksi->onFirstPage())
                            <span class="px-2 py-1 text-xs bg-gray-200 text-gray-500 rounded cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $transaksi->previousPageUrl() }}" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition">Previous</a>
                        @endif

                        @foreach($transaksi->getUrlRange(1, $transaksi->lastPage()) as $page => $url)
                            @if($page == $transaksi->currentPage())
                                <span class="px-2 py-1 text-xs bg-blue-500 text-white rounded">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($transaksi->hasMorePages())
                            <a href="{{ $transaksi->nextPageUrl() }}" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition">Next</a>
                        @else
                            <span class="px-2 py-1 text-xs bg-gray-200 text-gray-500 rounded cursor-not-allowed">Next</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Daftar Semua Barang -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-sm font-medium text-gray-700">Daftar Barang Saat Ini</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Kode</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Nama Barang</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Margin</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Harga Beli</th>
                    <th class="px-5 py-3 text-center border-r border-gray-200">Stok</th>
                    <th class="px-5 py-3 text-center">Harga Jual</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($daftarBarang as $b)
                <tr>
                    <td class="px-5 py-4 border-r border-gray-100">{{ $b->id_barang }}</td>
                    <td class="px-5 py-4 border-r border-gray-100">{{ $b->nama_barang }}</td>
                    <td class="px-5 py-4 text-left text-purple-600 border-r border-gray-100">{{ $b->margin }}%</td>
                    <td class="px-5 py-4 text-left border-r border-gray-100">Rp {{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-left border-r border-gray-100 {{ $b->stok < 10 ? 'text-red-600 font-bold' : '' }}">{{ $b->stok }}</td>
                    <td class="px-5 py-4 text-left font-bold text-green-600">Rp {{ number_format($b->retail, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('js')
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    <script>
        new Chart(document.getElementById('chartTerlaris'), {
            type: 'bar',
            data: {
                labels: @json($terlaris->pluck('nama_barang')),
                datasets: [{
                    label: 'Terjual (Qty)',
                    data: @json($terlaris->pluck('qty')),
                    backgroundColor: '#6366f1',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endpush
@endsection