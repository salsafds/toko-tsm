@extends('layouts.appmaster')

@section('title', 'Data Agen Ekspedisi')

@section('content')
<div class="container mx-auto">
    <div class="flex flex-col items-start mb-4 sm:mb-6">
        <h1 class="text-2xl font-semibold text-gray-800 text-left">Data Agen Ekspedisi</h1>
        <p class="text-sm text-gray-500 mt-1 text-left">Daftar agen ekspedisi dan kontak</p>
    </div>

    {{-- Kontrol: Show (kiri) + Search & Tambah (kanan atas di desktop, berdampingan di mobile seperti Data Bahasa) --}}
    <div class="flex flex-col items-start mb-4 gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form method="GET" action="{{ route('master.data-agen-ekspedisi.index') }}" class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600">Show</label>
                <select name="per_page" id="per_page" onchange="this.form.submit()" class="rounded-md border text-sm px-2 py-1">
                    @php $per = request()->query('per_page', 10); @endphp
                    <option value="5" {{ $per==5?'selected':'' }}>5</option>
                    <option value="10" {{ $per==10?'selected':'' }}>10</option>
                    <option value="25" {{ $per==25?'selected':'' }}>25</option>
                    <option value="50" {{ $per==50?'selected':'' }}>50</option>
                </select>
            </form>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <form method="GET" action="{{ route('master.data-agen-ekspedisi.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative border rounded-md w-full sm:w-64">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                    <input name="q" value="{{ request()->query('q','') }}" placeholder="Search…" class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-sm w-full" />
                </div>
            </form>

            <a href="{{ route('master.data-agen-ekspedisi.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
                <svg class="h-5 w-5 sm:mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Tambah</span>
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-gray-200 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table – dengan horizontal scroll di mobile --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <div class="min-w-[800px]">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-50">
                    <tr class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="min-w-[80px] px-3 py-3 border-r border-gray-200 whitespace-nowrap">ID</th>
                        <th class="min-w-[140px] px-3 py-3 border-r whitespace-nowrap">Nama</th>
                        <th class="min-w-[120px] px-3 py-3 border-r whitespace-nowrap">Telepon</th>
                        <th class="min-w-[140px] px-3 py-3 border-r whitespace-nowrap">Email</th>
                        <th class="min-w-[100px] px-3 py-3 border-r whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($agens as $item)
                        <tr class="hover:bg-gray-50 transition-colors text-xs sm:text-sm">
                            <td class="px-3 py-2 border-r border-gray-100 whitespace-nowrap">{{ $item->id_ekspedisi }}</td>
                            <td class="px-3 py-2 border-r whitespace-nowrap">{{ $item->nama_ekspedisi }}</td>
                            <td class="px-3 py-2 border-r whitespace-nowrap">{{ $item->telepon_ekspedisi }}</td>
                            <td class="px-3 py-2 border-r whitespace-nowrap truncate max-w-[140px]" title="{{ $item->email_ekspedisi }}">{{ $item->email_ekspedisi }}</td>
                            <td class="px-3 py-2 border-r text-center">
                                <div class="flex justify-center items-center gap-1">
                                    <a href="{{ route('master.data-agen-ekspedisi.edit', $item->id_ekspedisi) }}"
                                       class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200">
                                        Edit
                                    </a>
                                    <form action="{{ route('master.data-agen-ekspedisi.destroy', $item->id_ekspedisi) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data agen ekspedisi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-700 hover:bg-rose-200">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                Tidak ada data agen ekspedisi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="text-sm text-gray-600">
            @if(isset($agens) && $agens->total())
                Menampilkan {{ $agens->firstItem() }} sampai {{ $agens->lastItem() }} dari {{ $agens->total() }} data
            @endif
        </div>

        <div>
            @if(isset($agens) && method_exists($agens, 'links'))
                {{ $agens->appends(request()->query())->links('vendor.pagination.custom') }}
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

        fetch(`{{ route('master.data-agen-ekspedisi.index') }}?q=${encodeURIComponent(query)}&per_page=${perPage}`, {
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

    if (searchInput) {
        searchInput.addEventListener('keyup', fetchData);
    }
});
</script>
@endsection