@extends('layouts.appmaster')

@section('title', 'Laporan Mutasi Barang')

@section('content')
<div class="container mx-auto">
    <div class="flex flex-col items-start mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-2xl font-semibold text-gray-800 text-left">Laporan Mutasi Barang</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1 text-left">Riwayat stok masuk dan keluar semua barang</p>
    </div>

    <div class="flex flex-col items-start mb-4 gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form method="GET" class="flex items-center gap-2">
                <label for="per_page" class="text-xs sm:text-sm text-gray-600">Show</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()"
                        class="rounded-md border text-xs sm:text-sm px-2 py-1">
                    @php $per = request()->query('per_page', 10); @endphp
                    <option value="10" {{ $per == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $per == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $per == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $per == 100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <!-- Tombol Print PDF – tampil cantik tapi klik ga ngapa-ngapain -->
            <button type="button" onclick="event.preventDefault();"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print PDF
            </button>

            <!-- Tombol Export Excel – tampil cantik tapi klik ga ngapa-ngapain -->
            <button type="button" onclick="event.preventDefault();"
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m-4-6v6m8-6v6m-8-8h8a2 2 0 012 2v10a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                </svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- Flash message (jika ada) --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded text-xs sm:text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-x-auto border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
                <tr class="text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3 text-left border-r border-gray-200">Tanggal</th>
                    <th class="px-4 py-3 text-left border-r">Barang</th>
                    <th class="px-4 py-3 text-center border-r">Qty</th>
                    <th class="px-4 py-3 text-right border-r">Harga Beli</th>
                    <th class="px-4 py-3 text-right border-r">Total Harga</th>
                    <th class="px-4 py-3 text-center border-r">Margin %</th>
                    <th class="px-4 py-3 text-right border-r">Avg Price</th>
                    <th class="px-4 py-3 text-center border-r">Stok Awal</th>
                    <th class="px-4 py-3 text-center border-r text-green-600 font-bold">Masuk</th>
                    <th class="px-4 py-3 text-center border-r text-red-600 font-bold">Keluar</th>
                    <th class="px-4 py-3 text-center border-r font-bold">Saldo Akhir</th>
                    <th class="px-4 py-3 text-right border-r font-bold">Total Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100 text-xs sm:text-sm">
                @forelse($paginated as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 border-r text-gray-700">
                            {{ $row['tanggal']->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-4 py-3 border-r text-gray-800 font-medium">
                            {{ $row['nama_barang'] }}
                        </td>
                        <td class="px-4 py-3 text-center border-r">
                            {{ number_format($row['kuantitas']) }}
                        </td>
                        <td class="px-4 py-3 text-right border-r">
                            Rp {{ number_format($row['harga_beli'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-right border-r">
                            Rp {{ number_format($row['total_harga'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center border-r">
                            {{ number_format($row['margin'], 2) }}%
                        </td>
                        <td class="px-4 py-3 text-right border-r font-medium">
                            Rp {{ number_format($row['average_price'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center border-r">
                            {{ number_format($row['stok_awal']) }}
                        </td>
                        <td class="px-4 py-3 text-center border-r text-green-600 font-bold">
                            {{ $row['masuk'] > 0 ? number_format($row['masuk']) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center border-r text-red-600 font-bold">
                            {{ $row['keluar'] > 0 ? number_format($row['keluar']) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-center border-r font-bold text-gray-800">
                            {{ number_format($row['saldo_akhir']) }}
                        </td>
                        <td class="px-4 py-3 text-right border-r font-bold">
                            Rp {{ number_format($row['total_amount'], 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-10 text-center text-gray-500">
                            Belum ada data mutasi barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination + Info --}}
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-xs sm:text-sm text-gray-600">
            @if($paginated->total())
                Menampilkan {{ $paginated->firstItem() }} sampai {{ $paginated->lastItem() }} dari {{ $paginated->total() }} transaksi
            @endif
        </div>
        <div>
            {{ $paginated->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection