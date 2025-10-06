@extends('layouts.appmaster')

@section('title', 'Data Satuan')

@section('content')
<div class="container mx-auto p-6">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <form method="GET" action="{{ route('master.dataSatuan.index') ?? url()->current() }}" class="flex items-center gap-2">
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
      <form method="GET" action="{{ route('master.dataSatuan.index') ?? url()->current() }}" class="flex items-center gap-2">
        <div class="relative">
          <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
          </svg>
          <input name="q" value="{{ request()->query('q', '') }}" placeholder="Search…" aria-label="Search"
                 class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-sm w-64" />
        </div>
      </form>

      <a href="{{ route('master.dataSatuan.create') ?? '#' }}" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
        + Tambah
      </a>
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr class="text-left text-xs text-gray-500 uppercase">
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">Nama Satuan</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($satuans ?? collect() as $item)
          <tr>
            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->id }}</td>
            <td class="px-4 py-3 text-sm text-gray-700">{{ $item->nama }}</td>
            <td class="px-4 py-3 text-sm text-gray-700 text-right">
              {{-- Options dropdown (simple) --}}
              <div class="inline-block relative text-left">
                <button type="button" class="inline-flex items-center gap-2 px-3 py-1 border rounded text-sm" x-data @click="$refs.menu.classList.toggle('hidden')">
                  Options
                  <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>

                <div x-data x-cloak class="absolute right-0 mt-2 w-36 bg-white border rounded shadow-sm hidden" x-ref="menu">
                  <a href="{{ route('master.dataSatuan.show', $item->id) ?? '#' }}" class="block px-3 py-2 text-sm hover:bg-gray-50">View</a>
                  <a href="{{ route('master.dataSatuan.edit', $item->id) ?? '#' }}" class="block px-3 py-2 text-sm hover:bg-gray-50">Edit</a>
                  <form action="{{ route('master.dataSatuan.destroy', $item->id) ?? '#' }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-50">Delete</button>
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
      {{-- If you use Laravel paginator, $satuans->links() will render pagination. 
           Here we show it as Tailwind-styled links --}}
      @if(isset($satuans) && method_exists($satuans, 'links'))
        {{ $satuans->appends(request()->query())->links() }}
      @else
        {{-- static placeholder pagination --}}
        <nav class="inline-flex items-center gap-1 text-sm" aria-label="Pagination">
          <a class="px-3 py-1 border rounded" href="#">Prev</a>
          <a class="px-3 py-1 border rounded bg-blue-600 text-white" href="#">1</a>
          <a class="px-3 py-1 border rounded" href="#">2</a>
          <a class="px-3 py-1 border rounded" href="#">3</a>
          <a class="px-3 py-1 border rounded" href="#">Next</a>
        </nav>
      @endif
    </div>
  </div>
</div>
@endsection
