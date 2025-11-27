@extends('layouts.app-admin')

@section('title', 'Pembelian')

@section('content')
<div class="container mx-auto">
  <div class="flex flex-col items-start mb-4 sm:mb-6">
    <h1 class="text-2xl sm:text-2xl font-semibold text-gray-800 text-left">Data Pembelian</h1>
    <p class="text-xs sm:text-sm text-gray-500 mt-1 text-left">Daftar pembelian dan informasi</p>
  </div>

  <div class="flex flex-col items-start mb-4 gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-2 w-full sm:w-auto">
      <form method="GET" action="{{ route('admin.pembelian.index') }}" class="flex items-center gap-2">
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
      <form method="GET" action="{{ route('admin.pembelian.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
        <div class="relative border rounded-md w-full sm:w-64">
          <svg class="absolute left-3 top-2.5 h-4 sm:h-5 w-4 sm:w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
          </svg>
          <input name="q" value="{{ request()->query('q', '') }}" placeholder="Search…" aria-label="Search" class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-xs sm:text-sm w-full" />
        </div>
      </form>

      <a href="{{ route('admin.pembelian.create') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
        <svg class="h-4 sm:h-5 w-4 sm:w-5 sm:mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Tambah</span>
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded text-xs sm:text-sm">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-lg shadow-sm overflow-x-auto border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-50">
        <tr class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
          <th class="w-24 sm:w-32 px-2 sm:px-4 py-2 sm:py-3 border-r border-gray-200">ID Pembelian</th>
          <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r">Supplier</th>
          <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r">User</th>
          <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r">Jumlah Bayar</th>
          <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r">Tanggal Pembelian</th> 
          <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r">Tanggal Terima</th>
          <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($pembelian as $item)
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r border-gray-100">{{ $item->id_pembelian }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ $item->supplier->nama_supplier }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ $item->user->nama_lengkap }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">{{ $item->tanggal_pembelian->format('d/m/Y') }}</td>
            <td class="px-2 sm:px-4 py-2 text-xs sm:text-sm text-gray-700 border-r">
              @if($item->tanggal_terima)
                {{ $item->tanggal_terima->format('d/m/Y') }}
              @else
                <form action="{{ route('admin.pembelian.selesai', $item->id_pembelian) }}" method="POST" style="display:inline;">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 hover:bg-green-200" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan pembelian ini? Ini akan menambah stok barang.')">Selesai</button>
                </form>
              @endif
            </td>
            <td class="px-2 sm:px-4 py-2 text-center">
              <div class="flex justify-center items-center gap-2 sm:gap-3">
                @if(!$item->tanggal_terima)
                  <a href="{{ route('admin.pembelian.edit', $item->id_pembelian) }}" class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200">Edit</a>
                @else
                  <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Edit</span>
                @endif
                @if(!$item->tanggal_terima)  
                  <form action="{{ route('admin.pembelian.destroy', $item->id_pembelian) }}" method="POST" onsubmit="return confirm('Apakah anda yakin ingin menghapus data pembelian ini?');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-700 hover:bg-rose-200">Delete</button>
                  </form>
                @else
                  <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Delete</span>
                @endif
                <a href="{{ route('admin.pembelian.show', $item->id_pembelian) }}" class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 hover:bg-purple-200">View</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-6 sm:py-8 text-center text-xs sm:text-sm text-gray-500">Tidak ada data pembelian.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2">
    <div class="text-xs sm:text-sm text-gray-600">
      @if(isset($pembelian) && $pembelian->total())
        Menampilkan {{ $pembelian->firstItem() }} sampai {{ $pembelian->lastItem() }} dari {{ $pembelian->total() }} data
      @endif
    </div>
    <div>
      @if(isset($pembelian) && method_exists($pembelian, 'links'))
        {{ $pembelian->appends(request()->query())->links() }}
      @endif
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.querySelector('input[name="q"]');
  const tableBody = document.querySelector('tbody');
  const perPageSelect = document.querySelector('#per_page');

  function fetchData() {
    const query = searchInput.value;
    const perPage = perPageSelect.value;

    fetch(`{{ route('admin.pembelian.index') }}?q=${encodeURIComponent(query)}&per_page=${perPage}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(html => {
      const parser = new DOMParser();
      const newDoc = parser.parseFromString(html, 'text/html');
      const newTbody = newDoc.querySelector('tbody');
      if (newTbody) {
        tableBody.innerHTML = newTbody.innerHTML;
      }
    })
    .catch(error => console.error('Error:', error));
  }

  if (searchInput) searchInput.addEventListener('keyup', fetchData);
});
</script>
@endsection