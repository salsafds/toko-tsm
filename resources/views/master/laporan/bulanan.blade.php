@extends('layouts.appmaster')
@section('title', 'Laporan Bulanan')
@section('content')
{{-- Header + Filter Periode --}}
    <div class="mb-6">
        <div class="mb-4">
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Laporan Toko Koperasi</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Periode Laporan: <span class="font-medium text-gray-700">{{ $periodeTeks }}</span>
            </p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                
                <form method="GET" class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    
                    <div class="relative w-full sm:w-56">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        </div>
                        <select name="periode" onchange="this.form.submit()" class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-700 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-2.5 shadow-sm border">
                            <option value="" disabled {{ !$periode ? 'selected' : '' }}>Pilih Jenis Periode</option>
                            <option value="7hari" {{ $periode == '7hari' ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="1bulan_terakhir" {{ $periode == '1bulan_terakhir' ? 'selected' : '' }}>1 Bulan Terakhir</option>
                            <option value="3bulan" {{ $periode == '3bulan' ? 'selected' : '' }}>3 Bulan Terakhir</option>
                            <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Laporan Bulanan</option>
                            <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Laporan Tahunan</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-64">
                        @if($periode === 'bulanan')
                            <input type="month" name="bulan" 
                                value="{{ request('bulan') ?? now()->format('Y-m') }}" 
                                onchange="this.form.submit()"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 border px-3">
                        @elseif($periode === 'tahunan')
                            <select name="tahun" onchange="this.form.submit()" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 border px-3 bg-white">
                                @for($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                                @endfor
                            </select>
                        @else
                            <div class="block w-full rounded-lg border border-dashed border-gray-300 bg-gray-50 text-gray-400 sm:text-sm py-2.5 px-3 text-center select-none">
                                Otomatis
                            </div>
                        @endif
                    </div>
                </form>

                <div class="flex items-center gap-3 w-full lg:w-auto border-t lg:border-t-0 lg:border-l border-gray-100 pt-3 lg:pt-0 lg:pl-4">
                    
                    <a href="{{ route('master.laporan.bulanan.export', array_merge(request()->query(), ['format' => 'pdf', 'type' => 'global'])) }}" 
                    class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-md text-sm font-medium shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF Laporan
                    </a>
                    
                    <a href="{{ route('master.laporan.bulanan.export', array_merge(request()->query(), ['format' => 'excel', 'type' => 'global'])) }}" 
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-md text-sm font-medium shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Excel Laporan
                    </a>
                    
                </div>

            </div>
        </div>
    </div>

    {{-- Main Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Omzet Penjualan</div>
            <div class="mt-2 text-xl font-bold text-gray-900">Rp {{ number_format($omzet, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Laba Kotor</div>
            <div class="mt-2 text-xl font-bold text-green-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Margin {{ $marginPersen }}%</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Nilai Stok Akhir</div>
            <div class="mt-2 text-xl font-bold text-purple-600">Rp {{ number_format($nilaiStokAkhir, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100">
            <div class="text-sm font-medium text-gray-500">Stok Kritis</div>
            <div class="mt-2 text-xl font-bold text-red-600">{{ $stokKritis->count() }} barang</div>
        </div>
    </div>

    {{-- Ringkasan Pembelian & HPP --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        {{-- Ringkasan Pembelian --}}
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-sm font-semibold text-gray-700 mb-5">Ringkasan Pembelian</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Nota Pembelian (periode ini)</span>
                    <span class="font-bold text-gray-800">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                    <span class="text-gray-600 flex items-center">
                        Barang Masuk Stok (sudah diterima)
                    </span>
                    <span class="font-bold text-green-600">Rp {{ number_format($pembelianMasuk, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        {{-- HPP & Laba Kotor --}}
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-sm font-semibold text-gray-700 mb-5">HPP & Laba Kotor</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">HPP (Biaya Pokok Penjualan)</span>
                    <span class="font-bold text-orange-600">Rp {{ number_format($hpp, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t-2 border-green-100">
                    <span class="font-bold text-gray-800">Laba Kotor Bersih</span>
                    <span class="font-bold text-green-600">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
@if($stokKritis->total() > 0)
<div class="bg-white rounded-lg shadow-lg mb-6 border border-gray-200 overflow-x-auto">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-sm font-medium text-gray-700">
            Stok Kritis (Stok < 10) 
        </h2>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-gray-600">Show</span>
            <form method="GET">
                @foreach(request()->query() as $key => $value)
                    @if(!in_array($key, ['per_page_stok', 'page_stok']))
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <select name="per_page_stok" onchange="this.form.submit()"
                        class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10"  {{ $perPageStok == 10  ? 'selected' : '' }}>10</option>
                    <option value="15"  {{ $perPageStok == 15  ? 'selected' : '' }}>15</option>
                    <option value="30"  {{ $perPageStok == 30  ? 'selected' : '' }}>30</option>
                    <option value="50"  {{ $perPageStok == 50  ? 'selected' : '' }}>50</option>
                </select>
            </form>
        </div>
    </div>

    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr class="text-xs text-gray-500 uppercase">
                <th class="px-5 py-3 text-center border-r border-gray-200">Kode</th>
                <th class="px-5 py-3 text-center border-r border-gray-200">Nama Barang</th>
                <th class="px-5 py-3 text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <span>Stok</span>
                        <div class="flex flex-col -space-y-1">
                            <a href="{{ request()->fullUrlWithQuery(['sort_stok' => 'asc', 'page_stok' => null]) }}"
                               class="leading-none {{ $sortStok === 'asc' ? 'text-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 10l7-7 7 7z"/></svg>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort_stok' => 'desc', 'page_stok' => null]) }}"
                               class="leading-none {{ $sortStok === 'desc' ? 'text-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600' }}">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M17 10l-7 7-7-7z"/></svg>
                            </a>
                        </div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($stokKritis as $b)
            <tr class="hover:bg-red-50">
                <td class="px-5 py-4 font-mono text-sm border-r border-gray-100">{{ $b->id_barang }}</td>
                <td class="px-5 py-4 border-r border-gray-100">{{ $b->nama_barang }}</td>
                <td class="px-5 py-4 text-center font-bold text-red-600">{{ $b->stok }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <!-- Pagination --}}
    <div class="flex justify-end mt-4 mb-6 px-3">
        <div class="flex items-center gap-4 select-none">
            @if ($stokKritis->onFirstPage())
                <span class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $stokKritis->appends(request()->query())->previousPageUrl() }}"
                   class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            <span class="text-gray-700 text-sm font-medium">
                {{ $stokKritis->currentPage() }} of {{ $stokKritis->lastPage() }}
            </span>

            @if ($stokKritis->hasMorePages())
                <a href="{{ $stokKritis->appends(request()->query())->nextPageUrl() }}"
                   class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    </div>
</div>
@else
<div class="bg-white rounded-lg shadow-sm p-6 mb-6 text-center text-gray-500">
    Tidak ada barang dengan stok kritis saat ini.
</div>
@endif

{{-- 10 Barang Terlaris + Bar Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- CHART --}}
        <div class="bg-white p-5 rounded-lg shadow-lg">
            <h2 class="text-sm font-medium text-gray-700 mb-4 text-center font-semibold">10 Barang Terlaris (Qty)</h2>
            <div class="relative h-80 w-full pb-12">
                <canvas id="chartTerlaris"></canvas>
            </div>
        </div>

        {{-- Detail Terlaris --}}
        <div class="bg-white p-5 rounded-lg shadow-lg border border-gray-200 overflow-x-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-medium text-gray-700">Detail Terlaris</h2>
                <a href="{{ route('master.laporan.terlaris') . '?' . http_build_query(request()->query()) }}" class="text-xs text-indigo-600 hover:underline">Lihat selengkapnya</a>
            </div>
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

    {{-- Riwayat Transaksi --}}
    <div id="riwayat-transaksi" class="bg-white rounded-lg shadow-lg mb-6 border border-gray-200 overflow-x-auto">
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
                            {{ $t->jenis == 'penjualan' ? 'Penjualan' : 'Pembelian' }}
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

        <div class="flex items-center justify-between mt-4 mb-6 px-3">
             <div class="flex items-center gap-2 text-sm text-gray-700">
                <span>Show</span>
                <form method="GET">
                    <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page', 15) == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
            </div>

            <div class="flex items-center gap-4 select-none">
                @if ($transaksi->onFirstPage())
                    <span class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-300">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    </span>
                @else
                    <a href="{{ $transaksi->appends(request()->query())->previousPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                    </a>
                @endif

                <span class="text-gray-700 text-sm font-medium">
                    {{ $transaksi->currentPage() }} of {{ $transaksi->lastPage() }}
                </span>

                @if ($transaksi->hasMorePages())
                    <a href="{{ $transaksi->appends(request()->query())->nextPageUrl() }}" class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </a>
                @else
                    <span class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-300">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </span>
                @endif
            </div>
        </div>
    </div>

        {{-- Daftar Semua Barang --}}
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-x-auto">
            <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h2 class="text-sm font-medium text-gray-700">
                    Daftar Barang Saat Ini ({{ $daftarBarang->total() }} barang)
                </h2>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto">
                    {{-- Search Box --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="searchBarang" placeholder="Cari kode atau nama barang..."
                               class="block w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    </div>

                    {{-- Show entries --}}
                    <div class="flex items-center gap-2 text-sm text-gray-700">
                        <span>Show</span>
                        <form method="GET">
                            @foreach(request()->query() as $key => $value)
                                @if(!in_array($key, ['per_page_barang', 'page_barang']))
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <select name="per_page_barang" onchange="this.form.submit()"
                                    class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white">
                                <option value="10"  {{ $perPageBarang == 10  ? 'selected' : '' }}>10</option>
                                <option value="25"  {{ $perPageBarang == 25  ? 'selected' : '' }}>25</option>
                                <option value="50"  {{ $perPageBarang == 50  ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPageBarang == 100 ? 'selected' : '' }}>100</option>
                                <option value="200" {{ $perPageBarang == 200 ? 'selected' : '' }}>200</option>
                            </select>
                        </form>
                    </div>

                    {{-- Export Buttons --}}
                    <div class="flex gap-2">
                        <a href="{{ route('master.laporan.bulanan.export', array_merge(request()->query(), ['format' => 'pdf', 'section' => 'daftar_barang'])) }}"
                           class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                            Print PDF
                        </a>
                        <a href="{{ route('master.laporan.bulanan.export', array_merge(request()->query(), ['format' => 'excel', 'section' => 'daftar_barang'])) }}"
                           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                            Print Excel
                        </a>
                    </div>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200 text-sm" id="tabelDaftarBarang">
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
                <tbody class="divide-y divide-gray-100" id="tbodyDaftarBarang">
                    @forelse($daftarBarang as $b)
                    <tr class="{{ $b->stok < 10 ? 'bg-red-50' : '' }}" data-kode="{{ strtolower($b->id_barang) }}" data-nama="{{ strtolower($b->nama_barang) }}">
                        <td class="px-5 py-4 font-mono text-sm border-r border-gray-100">{{ $b->id_barang }}</td>
                        <td class="px-5 py-4 border-r border-gray-100">{{ $b->nama_barang }}</td>
                        <td class="px-5 py-4 text-center text-purple-600 border-r border-gray-100">{{ $b->margin }}%</td>
                        <td class="px-5 py-4 text-right border-r border-gray-100">
                            Rp {{ number_format($b->harga_beli, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-4 text-center font-bold border-r border-gray-100 {{ $b->stok < 10 ? 'text-red-600' : '' }}">
                            {{ $b->stok }}
                        </td>
                        <td class="px-5 py-4 text-right font-bold text-green-600">
                            Rp {{ number_format($b->retail, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">Tidak ada data barang</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            
        </div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // SEARCH BARANG
        const searchInput = document.getElementById('searchBarang');
        if (searchInput) { 
            const tbody = document.getElementById('tbodyDaftarBarang');
            const rows = tbody.querySelectorAll('tr');

            let timeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    const query = searchInput.value.toLowerCase().trim();

                    let hasVisible = false;

                    rows.forEach(row => {
                        const kode = row.getAttribute('data-kode') || '';
                        const nama = row.getAttribute('data-nama') || '';

                        if (kode.includes(query) || nama.includes(query)) {
                            row.style.display = '';
                            hasVisible = true;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Tampilkan pesan tidak ditemukan
                    const noResult = document.getElementById('noResultRow');
                    if (noResult) noResult.remove();

                    if (query && !hasVisible) {
                        const tr = document.createElement('tr');
                        tr.id = 'noResultRow';
                        tr.innerHTML = `<td colspan="6" class="text-center py-8 text-gray-500">Tidak ditemukan barang dengan kata kunci "<strong>${searchInput.value}</strong>"</td>`;
                        tbody.appendChild(tr);
                    }
                }, 300);
            });
        }

        // CHART TERLARIS
        const canvas = document.getElementById('chartTerlaris');
        if (!canvas) return;

        const terlaris = @json($terlaris);

        if (!terlaris || terlaris.length === 0) {
            canvas.parentElement.innerHTML = `
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 text-lg">Tidak ada data penjualan pada periode ini</p>
                </div>`;
            return;
        }

        const qtyData = terlaris.map(item => parseInt(item.qty) || 0);
        const maxQty = Math.max(...qtyData, 10);

        let stepSize = 10;
        if (maxQty > 500) stepSize = 100;
        else if (maxQty > 200) stepSize = 50;
        else if (maxQty > 100) stepSize = 20;
        else if (maxQty > 50) stepSize = 10;
        else if (maxQty > 20) stepSize = 5;

        const maxY = Math.ceil(maxQty / stepSize) * stepSize + stepSize;

        const labels = terlaris.map(item => {
            let name = item.nama_barang || 'Unknown';
            const words = name.split(' ');
            let lines = [];
            let currentLine = '';
            words.forEach(word => {
                if ((currentLine + word).length > 20) {
                    lines.push(currentLine.trim());
                    currentLine = word + ' ';
                } else {
                    currentLine += word + ' ';
                }
            });
            if (currentLine.trim()) lines.push(currentLine.trim());
            return lines.join('\n');
        });

        if (window.terlarisChart instanceof Chart) {
            window.terlarisChart.destroy();
        }

        window.terlarisChart = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Terjual (unit)',
                    data: qtyData,
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 'flex',
                    maxBarThickness: 60,
                    hoverBackgroundColor: '#1d4ed8'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { size: 13 },
                        bodyFont: { size: 14 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: context => `Terjual: ${context.parsed.y} unit`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: maxY,
                        ticks: {
                            stepSize: stepSize,
                            padding: 10,
                            font: { size: 12 },
                            callback: value => value + ' unit'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 0,
                            minRotation: 0,
                            align: 'center',
                            autoSkip: false,
                            font: { size: 11, lineHeight: 1.4 },
                            padding: 10
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection