@extends('layouts.appmaster')
@section('title', 'Barang Terlaris')
@section('content')
   <!-- Header + Filter Periode (sama persis seperti bulanan.blade.php) -->
<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4 pt-2">
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Barang Terlaris</h1>
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

<!-- Tabel Semua Barang Terlaris -->
<div class="bg-white rounded-lg shadow-lg mb-6 border border-gray-200 overflow-x-auto">
    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-sm font-medium text-gray-700">Daftar Semua Barang Terlaris ({{ $terlarisAll->total() }})</h2>
        <div class="flex items-center gap-4">
            <!-- Show entries -->
            <div class="flex items-center gap-2 text-sm text-gray-700">
                <span>Show</span>
                <form method="GET">
                    @foreach(request()->query() as $key => $value)
                        @if(!in_array($key, ['per_page', 'page']))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="per_page" onchange="this.form.submit()"
                            class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page', 15) == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
            </div>
            <!-- Export Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('master.laporan.terlaris.export', array_merge(request()->query(), ['format' => 'pdf'])) }}"
                   class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                    Print PDF
                </a>
                <a href="{{ route('master.laporan.terlaris.export', array_merge(request()->query(), ['format' => 'excel'])) }}"
                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded text-xs font-medium transition">
                    Print Excel
                </a>
            </div>
        </div>
    </div>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
            <tr>
                <th class="px-4 py-2 text-left border-r border-gray-200">Rank</th>
                <th class="px-4 py-2 text-left border-r border-gray-200">Kode</th>
                <th class="px-4 py-2 text-left border-r border-gray-200">Barang</th>
                <th class="px-4 py-2 text-right border-r border-gray-200">Terjual</th>
                <th class="px-4 py-2 text-right">Omzet</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($terlarisAll as $i => $item)
            <tr>
                <td class="px-4 py-3 font-bold text-indigo-600 border-r border-gray-100">#{{ $terlarisAll->firstItem() + $i }}</td>
                <td class="px-4 py-3 font-mono text-sm border-r border-gray-100">{{ $item->id_barang }}</td>
                <td class="px-4 py-3 border-r border-gray-100">{{ $item->nama_barang }}</td>
                <td class="px-4 py-3 text-right font-bold border-r border-gray-100">{{ $item->qty }}</td>
                <td class="px-4 py-3 text-right text-green-600">Rp {{ number_format($item->omzet, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-gray-400">Tidak ada penjualan pada periode ini</td></tr>
            @endforelse
        </tbody>
    </table>
    <!-- Pagination (sama seperti di bulanan) -->
    <div class="flex items-center justify-between mt-4 mb-6 px-3">
        <div class="flex items-center gap-2 text-sm text-gray-700">
            <span>Show</span>
            <form method="GET">
                <select name="per_page"
                    onchange="this.form.submit()"
                    class="border border-gray-300 rounded-md px-2 py-1 text-sm bg-white 
                        focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="30" {{ request('per_page', 15) == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                </select>
            </form>
        </div>
        <div class="flex items-center gap-4 select-none">
            @if ($terlarisAll->onFirstPage())
                <span class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-300">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $terlarisAll->appends(request()->query())->previousPageUrl() }}"
                   class="flex items-center justify-center w-8 h-8 rounded-md border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif
            <span class="text-gray-700 text-sm font-medium">
                {{ $terlarisAll->currentPage() }} of {{ $terlarisAll->lastPage() }}
            </span>
            @if ($terlarisAll->hasMorePages())
                <a href="{{ $terlarisAll->appends(request()->query())->nextPageUrl() }}"
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
@endsection