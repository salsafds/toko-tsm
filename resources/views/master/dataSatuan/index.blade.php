@extends('layouts.appmaster')

@section('title', 'Data Satuan')

@section('content')
<div class="container mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <form method="GET" action="{{ route('master.dataSatuan.index') }}" class="flex items-center gap-2">
        <label for="per_page" class="text-sm text-gray-600">Show</label>
        <select name="per_page" id="per_page" onchange="this.form.submit()" class="ml-2 rounded-md border-gray-200 text-sm px-2 py-1">
          @php $per = request()->query('per_page', 10); @endphp
          <option value="5" {{ $per==5 ? 'selected' : '' }}>5</option>
          <option value="10" {{ $per==10 ? 'selected' : '' }}>10</option>
          <option value="25" {{ $per==25 ? 'selected' : '' }}>25</option>
          <option value="50" {{ $per==50 ? 'selected' : '' }}>50</option>
        </select>
      </form>
    </div>

    <div class="flex items-center gap-3">
      <form method="GET" action="{{ route('master.dataSatuan.index') }}" class="flex items-center gap-2">
        <div class="relative">
          <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
          </svg>
          <input name="q" value="{{ request()->query('q', '') }}" placeholder="Search…" aria-label="Search"
                 class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-sm w-64" />
        </div>
      </form>

      <a href="{{ route('master.dataSatuan.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
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
  <div class="bg-white rounded-lg shadow-sm overflow-visible">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr class="text-left text-xs text-gray-500 uppercase">
          <th class="px-4 py-3">ID Satuan</th>  {{-- Ubah label --}}
          <th class="px-4 py-3">Nama Satuan</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead> 
      <tbody class="bg-white divide-y divide-gray-100">
      @forelse($satuans ?? collect() as $item)
          <tr>
              <td class="px-4 py-3 text-sm text-gray-700">{{ $item->id_satuan }}</td>
              <td class="px-4 py-3 text-sm text-gray-700">{{ $item->nama_satuan }}</td>
              <td class="px-4 py-3 text-sm text-gray-700 text-right">
                  {{-- Dropdown Options dengan Alpine.js v3 (bundled via Vite) --}}
                  <div x-data="{ open: false }" class="inline-block relative text-left">
                      <button type="button" 
                              class="inline-flex items-center justify-center gap-2 px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors" 
                              @click="open = !open"
                              @keydown.escape.window="open = false">
                          Options
                          <svg class="h-4 w-4 transition-transform duration-200" 
                              :class="{ 'rotate-180': open }" 
                              xmlns="http://www.w3.org/2000/svg" 
                              fill="none" 
                              viewBox="0 0 24 24" 
                              stroke="currentColor">
                              <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                          </svg>
                      </button>

                      {{-- Dropdown Menu --}}
                      <div x-show="open" 
                          x-transition:enter="transition ease-out duration-100"
                          x-transition:enter-start="transform opacity-0 scale-95"
                          x-transition:enter-end="transform opacity-100 scale-100"
                          x-transition:leave="transition ease-in duration-75"
                          x-transition:leave-start="transform opacity-100 scale-100"
                          x-transition:leave-end="transform opacity-0 scale-95"
                          @click.away="open = false"
                          class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50 ring-1 ring-black ring-opacity-5 focus:outline-none origin-top-right">
                          
                          {{-- Edit --}}
                          <a href="{{ route('master.dataSatuan.edit', $item->id_satuan) }}" 
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                              Edit
                          </a>
                          
                          {{-- Divider --}}
                          <hr class="my-1 border-gray-200">
                          
                          {{-- Delete --}}
                          <form action="{{ route('master.dataSatuan.destroy', $item->id_satuan) }}" 
                                method="POST" 
                                class="block"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data satuan ini?')">
                              @csrf
                              @method('DELETE')
                              <button type="submit" 
                                      class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 focus:bg-red-50 focus:outline-none">
                                  Delete
                              </button>
                          </form>
                      </div>
                  </div>
              </td>
          </tr>
      @empty
          <tr>
              <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                  Tidak ada data satuan.
              </td>
              </tr>
          @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4 flex items-center justify-between">
    <div class="text-sm text-gray-600">
      {{-- showing X to Y of Z --}}
      @if(isset($satuans) && $satuans->total())
        Menampilkan {{ $satuans->firstItem() }} sampai {{ $satuans->lastItem() }} dari {{ $satuans->total() }} data
      @endif
    </div>

    <div>
      {{-- Laravel paginator --}}
      @if(isset($satuans) && method_exists($satuans, 'links'))
        {{ $satuans->appends(request()->query())->links() }}
      @endif
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.querySelector('input[name="q"]');
  const tableBody = document.querySelector('tbody');
  const perPageSelect = document.querySelector('#per_page');

  <script>
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.querySelector('input[name="q"]');
  const tableBody = document.querySelector('tbody');
  const perPageSelect = document.querySelector('#per_page');

  // Fungsi untuk memuat data berdasarkan search
  function fetchData() {
    const query = searchInput.value;
    const perPage = perPageSelect.value;

    fetch(`{{ route('master.dataSatuan.index') }}?q=${encodeURIComponent(query)}&per_page=${perPage}`, {
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