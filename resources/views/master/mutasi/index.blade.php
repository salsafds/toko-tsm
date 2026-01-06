@extends('layouts.appmaster')
@section('title', 'Laporan Mutasi Barang')
@section('content')
<div class="container mx-auto">
    <div class="flex flex-col items-start mb-4 sm:mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Laporan Mutasi Barang</h1>
        <p class="text-sm text-gray-500 mt-1">Riwayat stok masuk dan keluar semua barang</p>
    </div>

    {{-- Header + Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center gap-2">
                @php
                    $currentPerPage = request()->query('per_page', 10);
                @endphp
                <label class="text-sm text-gray-600">Show</label>
                <select name="per_page" onchange="this.form.submit()" class="rounded border px-3 py-1 text-sm">
                    <option value="5" {{ $currentPerPage == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $currentPerPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $currentPerPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $currentPerPage == 50 ? 'selected' : '' }}>50</option>
                </select>
                @if(request()->has('period'))   <input type="hidden" name="period" value="{{ request('period') }}"> @endif
                @if(request()->has('sort'))     <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request()->has('direction'))<input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
            </form>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" class="flex items-center gap-2">
                <select name="period" onchange="this.form.submit()" class="rounded border px-4 py-2 text-sm font-medium">
                    <option value="all" {{ request('period','all')=='all' ? 'selected':'' }}>Semua Periode</option>
                    <option value="1w" {{ request('period')=='1w' ? 'selected':'' }}>1 Minggu Terakhir</option>
                    <option value="1m" {{ request('period')=='1m' ? 'selected':'' }}>1 Bulan Terakhir</option>
                    <option value="3m" {{ request('period')=='3m' ? 'selected':'' }}>3 Bulan Terakhir</option>
                    <option value="1y" {{ request('period')=='1y' ? 'selected':'' }}>1 Tahun Terakhir</option>
                </select>

                @if(request()->has('per_page'))  <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif
                @if(request()->has('sort'))      <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request()->has('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif
            </form>

            <div class="flex gap-2">
                <a href="{{ route('master.mutasi.export', ['format'=>'pdf','period'=>request('period','all')]) }}"
                   class="px-4 py-2 bg-red-600 text-white rounded text-sm flex items-center hover:bg-red-700">
                    PDF
                </a>
                <a href="{{ route('master.mutasi.export', ['format'=>'excel','period'=>request('period','all')]) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded text-sm flex items-center hover:bg-green-700">
                    Excel
                </a>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg shadow-sm overflow-x-auto border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{-- Helper link sorting --}}
                    @php
                        $currentSort = request('sort', 'tanggal');
                        $currentDir  = request('direction', 'asc');
                        $nextDir     = $currentDir === 'asc' ? 'desc' : 'asc';

                        $sortLink = function($column) use ($currentSort, $currentDir, $nextDir) {
                            $dir = ($currentSort === $column) ? $nextDir : 'asc';
                            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
                        };

                        $icon = function($column) use ($currentSort, $currentDir) {
                            if ($currentSort !== $column) return '↑↓';
                            return $currentDir === 'asc' ? '↑' : '↓';
                        };
                    @endphp

                    <th class="px-4 py-3 text-left border-r">
                        <a href="{{ $sortLink('tanggal') }}" class="flex items-center justify-between hover:text-gray-900">
                            Tanggal dan Waktu
                            <span class="ml-2 text-xs text-gray-400 font-bold">{{ $icon('tanggal') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left border-r">
                        <a href="{{ $sortLink('nama_barang') }}" class="flex items-center justify-between hover:text-gray-900">
                            Barang
                            <span class="ml-2 text-xs text-gray-400 font-bold">{{ $icon('nama_barang') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-center border-r">
                        <a href="{{ $sortLink('kuantitas') }}" class="flex items-center justify-center hover:text-gray-900">
                            Qty
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('kuantitas') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-right border-r">
                        <a href="{{ $sortLink('harga_beli') }}" class="flex items-center justify-end hover:text-gray-900">
                            Harga Satuan
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('harga_beli') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-right border-r">
                        <a href="{{ $sortLink('total_harga') }}" class="flex items-center justify-end hover:text-gray-900">
                            Total Harga
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('total_harga') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-center border-r">
                        <a href="{{ $sortLink('margin') }}" class="flex items-center justify-center hover:text-gray-900">
                            Margin %
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('margin') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-right border-r">
                        <a href="{{ $sortLink('average_price') }}" class="flex items-center justify-end hover:text-gray-900">
                            Avg Price
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('average_price') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-center border-r">
                        <a href="{{ $sortLink('stok_awal') }}" class="flex items-center justify-center hover:text-gray-900">
                            Stok Awal
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('stok_awal') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-center border-r text-green-600 font-bold">Masuk</th>
                    <th class="px-4 py-3 text-center border-r text-red-600 font-bold">Keluar</th>
                    <th class="px-4 py-3 text-center border-r">
                        <a href="{{ $sortLink('saldo_akhir') }}" class="flex items-center justify-center hover:text-gray-900">
                            Stok Akhir
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('saldo_akhir') }}</span>
                        </a>
                    </th>
                    <th class="px-4 py-3 text-right border-r">
                        <a href="{{ $sortLink('nilai_stok') }}" class="flex items-center justify-end hover:text-gray-900">
                            Nilai Stok
                            <span class="ml-1 text-xs text-gray-400 font-bold">{{ $icon('nilai_stok') }}</span>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100 text-sm">
                @forelse($paginated as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-r text-gray-700 whitespace-nowrap">
                            {{ $row['tanggal']->format('d-m-Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3 border-r text-gray-800 font-medium">{{ $row['nama_barang'] }}</td>
                        <td class="px-4 py-3 text-center border-r">{{ number_format($row['kuantitas']) }}</td>
                        <td class="px-4 py-3 text-right border-r">Rp {{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right border-r">Rp {{ number_format($row['total_harga'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center border-r">{{ number_format($row['margin'], 2) }}%</td>
                        <td class="px-4 py-3 text-right border-r font-medium">Rp {{ number_format($row['average_price'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center border-r">{{ number_format($row['stok_awal']) }}</td>
                        <td class="px-4 py-3 text-center border-r text-green-600 font-bold">
                            {{ $row['masuk'] > 0 ? number_format($row['masuk']) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center border-r text-red-600 font-bold">
                            {{ $row['keluar'] > 0 ? number_format($row['keluar']) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center border-r font-bold">{{ number_format($row['saldo_akhir']) }}</td>
                        <td class="px-4 py-3 text-right border-r font-bold">Rp {{ number_format($row['nilai_stok'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-10 text-center text-gray-500">
                            Belum ada data mutasi barang pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-600">
            @if(isset($paginated) && $paginated->total())
                Menampilkan {{ $paginated->firstItem() }} sampai {{ $paginated->lastItem() }} dari {{ $paginated->total() }} transaksi
            @endif
        </div>
        <div>
            @if(isset($paginated) && method_exists($paginated, 'links'))
                {{ $paginated->appends(request()->query())->links('vendor.pagination.custom') }}
            @endif
        </div>
    </div>
</div>
@endsection