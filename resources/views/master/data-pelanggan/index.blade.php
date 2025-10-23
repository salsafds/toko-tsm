@extends('layouts.appmaster')

@section('title', 'Data Pelanggan')

@section('content')
<div class="container mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Data Pelanggan</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan data pelanggan</p>
        </div>
    </div>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('master.data-pelanggan.index') }}" class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600">Show</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()" class="ml-2 rounded-md border text-sm px-2 py-1">
                    @php $per = request()->query('per_page', 10); @endphp
                    <option value="5" {{ $per==5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $per==10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $per==25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $per==50 ? 'selected' : '' }}>50</option>
                </select>
            </form>
        </div>

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('master.data-pelanggan.index') }}" class="flex items-center gap-2">
                <div class="relative border rounded-md">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                    <input name="q" value="{{ request()->query('q', '') }}" placeholder="Search…" aria-label="Search"
                           class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-sm w-64" />
                </div>
            </form>

            <a href="{{ route('master.data-pelanggan.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
                + Tambah
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
                <tr class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="w-32 px-4 py-3 border-r border-gray-200">ID Pelanggan</th>
                    <th class="w-auto flex-1 border-r px-4 py-3">Nama Pelanggan</th>
                    <th class="w-32 px-4 py-3 border-r">Kategori</th>
                    <th class="w-32 px-4 py-3 border-r">Kota</th>
                    <th class="w-48 px-4 py-3 border-r">Alamat</th>
                    <th class="w-32 px-4 py-3 border-r">Telepon</th>
                    <th class="w-32 px-4 py-3 border-r">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($pelanggans ?? collect() as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="w-auto flex-1 border-r px-4 py-2 text-sm text-gray-700 border-gray-100">{{ $item->id_pelanggan }}</td>
                        <td class="w-auto flex-1 border-r px-4 py-2 text-sm text-gray-700">{{ $item->nama_pelanggan }}</td>
                        <td class="w-32 border-r px-4 py-2 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $item->kategori_pelanggan)) }}</td>
                        <td class="w-32 border-r px-4 py-2 text-sm text-gray-700">{{ $item->kota->nama_kota ?? '-' }}</td>
                        <td class="w-48 border-r px-4 py-2 text-sm text-gray-700">{{ $item->alamat_pelanggan }}</td>
                        <td class="w-32 border-r px-4 py-2 text-sm text-gray-700">{{ $item->nomor_telepon }}</td>
                        <td class="w-32 px-4 py-2 border-r text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('master.data-pelanggan.edit', $item->id_pelanggan) }}"
                                   class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                    Edit
                                </a>
                                <form action="{{ route('master.data-pelanggan.destroy', $item->id_pelanggan) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pelanggan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-700 hover:bg-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-300">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                            Tidak ada data pelanggan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            @if(isset($pelanggans) && $pelanggans->total())
                Menampilkan {{ $pelanggans->firstItem() }} sampai {{ $pelanggans->lastItem() }} dari {{ $pelanggans->total() }} data
            @endif
        </div>

        <div>
            @if(isset($pelanggans) && method_exists($pelanggans, 'links'))
                {{ $pelanggans->appends(request()->query())->links() }}
            @endif
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('input[name="q"]');
    const tableBody = document.querySelector('tbody');
    const perPageSelect = document.querySelector('#per_page');

    // Fungsi untuk memuat data berdasarkan search
    function fetchData() {
        const query = searchInput.value;
        const perPage = perPageSelect.value;

        fetch(`{{ route('master.data-pelanggan.index') }}?q=${encodeURIComponent(query)}&per_page=${perPage}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            // Ambil isi tabel <tbody> dari hasil response
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newTbody = newDoc.querySelector('tbody');

            if (newTbody) {
                tableBody.innerHTML = newTbody.innerHTML;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Jalankan pencarian saat user mengetik (real-time)
    searchInput.addEventListener('keyup', fetchData);
});
</script>
@endsection