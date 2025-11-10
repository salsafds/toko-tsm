@extends('layouts.app-admin')

@section('title', 'Data Penjualan')

@section('content')
<div class="container mx-auto">
  <div class="flex flex-col items-start mb-4 sm:mb-6">
    <h1 class="text-2xl sm:text-2xl font-semibold text-gray-800 text-left">Data Penjualan</h1>
    <p class="text-xs sm:text-sm text-gray-500 mt-1 text-left">Daftar penjualan dan informasi transaksi</p>
  </div>

  <div class="flex flex-col items-start mb-4 gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-2 w-full sm:w-auto">
      <form method="GET" action="{{ route('admin.penjualan.index') }}" class="flex items-center gap-2">
        <label for="per_page" class="text-xs sm:text-sm text-gray-600">Show</label>
        <select name="per_page" id="per_page" onchange="this.form.submit()" class="rounded-md border text-xs sm:text-sm px-2 py-1">
          @php $per = request()->query('per_page', 10); @endphp
          <option value="5" {{ $per==5 ? 'selected' : '' }}>5</option>
          <option value="10" {{ $per==10 ? 'selected' : '' }}>10</option>
          <option value="25" {{ $per==25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ $per==50 ? 'selected' : '' }}>50</option>
        </select>
      </form>
    </div>

    <div class="flex items-center gap-2 w-full sm:w-auto">
      <form method="GET" action="{{ route('admin.penjualan.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
        <div class="relative border rounded-md w-full sm:w-64">
          <svg class="absolute left-3 top-2.5 h-4 sm:h-5 w-4 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
          </svg>
          <input name="q" value="{{ request()->query('q', '') }}" placeholder="Search…" aria-label="Search"
                 class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-xs sm:text-sm w-full" />
        </div>
      </form>

      <a href="{{ route('admin.penjualan.create') }}" 
         class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
        <svg class="h-4 sm:h-5 w-4 sm:w-5 sm:mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Tambah</span>
      </a>
    </div>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded text-xs sm:text-sm">
      {{ session('success') }}
    </div>
  @endif

  {{-- Table --}}
  <div class="bg-white rounded-lg shadow-sm overflow-x-auto border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-50">
        <tr class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
          <th class="w-24 sm:w-32 px-2 sm:px-4 py-2 sm:py-3 border-r border-gray-200">ID</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">User</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Pembeli</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Jenis Pembeli</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Total Harga</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Jenis Bayar</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Tanggal Order</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3 border-r">Tanggal Selesai</th>
          <th class="px-2 sm:px-4 py-2 sm:py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($penjualans as $item)
          @php
            $isSelesai = !is_null($item->tanggal_selesai);
            $namaPembeli = $item->pelanggan ? $item->pelanggan->nama_pelanggan : ($item->anggota ? $item->anggota->nama_anggota : 'N/A');
            $jenisPembeli = $item->pelanggan ? 'Pelanggan' : ($item->anggota ? 'Anggota' : 'N/A');
          @endphp
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r border-gray-100">{{ $item->id_penjualan }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ $item->user?->nama_lengkap ?? 'N/A' }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ $namaPembeli }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">{{ $jenisPembeli }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-right">Rp {{ number_format($item->total_harga_penjualan, 0, ',', '.') }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">{{ ucfirst($item->jenis_pembayaran) }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">{{ $item->tanggal_order->format('d-m-Y H:i') }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r text-center">
              @if($isSelesai)
                <span class="text-green-600 font-medium">{{ $item->tanggal_selesai->format('d-m-Y H:i') }}</span>
              @else
                <form action="{{ route('admin.penjualan.selesai', $item->id_penjualan) }}" method="POST" class="inline">
                  @csrf
                  @method('PATCH')
                  <button type="submit" 
                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200 transition-colors"
                          onclick="return confirm('Tandai transaksi ini selesai? Stok akan dikurangi.');">
                    Selesai
                  </button>
                </form>
              @endif
            </td>
            <td class="px-2 sm:px-4 py-2 text-center">
              <div class="flex justify-center items-center gap-1 sm:gap-2">
                @if(!$isSelesai)
                  <a href="{{ route('admin.penjualan.edit', $item->id_penjualan) }}"
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200">
                    Edit
                  </a>
                  <form action="{{ route('admin.penjualan.destroy', $item->id_penjualan) }}" method="POST"
                        onsubmit="return confirm('Hapus transaksi ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-700 hover:bg-rose-200">
                      Delete
                    </button>
                  </form>
                @else
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                    Edit
                  </span>
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                    Delete
                  </span>
                @endif
                <a href="{{ route('admin.penjualan.print', $item->id_penjualan) }}" target="_blank"
                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200">
                  Print
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="px-4 py-6 sm:py-8 text-center text-xs sm:text-sm text-gray-500">
              Tidak ada data penjualan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2">
    <div class="text-xs sm:text-sm text-gray-600">
      @if(isset($penjualans) && $penjualans->total())
        Menampilkan {{ $penjualans->firstItem() }} sampai {{ $penjualans->lastItem() }} dari {{ $penjualans->total() }} data
      @endif
    </div>
    <div>
      @if(isset($penjualans) && method_exists($penjualans, 'links'))
        {{ $penjualans->appends(request()->query())->links() }}
      @endif
    </div>
  </div>
</div>
@endsection