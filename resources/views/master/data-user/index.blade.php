@extends('layouts.appmaster')
@section('title', 'Data User')

@section('content')
<div class="container mx-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Data User</h1>
      <p class="text-sm text-gray-500 mt-1">Daftar pengguna sistem</p>
    </div>
  </div>

  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <form method="GET" action="{{ route('master.data-user.index') }}" class="flex items-center gap-2">
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
      <form method="GET" action="{{ route('master.data-user.index') }}" class="flex items-center gap-2">
        <div class="relative border rounded-md">
          <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
          </svg>
          <input name="q" value="{{ request()->query('q','') }}" placeholder="Search…" class="pl-10 pr-3 py-2 rounded-md border-gray-200 text-sm w-64" />
        </div>
      </form>

      <a href="{{ route('master.data-user.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-md text-sm hover:bg-blue-800">
        + Tambah
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200 table-auto">
      <thead class="bg-gray-50">
        <tr class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
          <th class="px-4 py-3 border-r">ID</th>
          <th class="px-4 py-3 border-r">Nama</th>
          <th class="px-4 py-3 border-r">Username</th>
          <th class="px-4 py-3 border-r">Role</th>
          <th class="px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse($users as $item)
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-2 text-sm text-gray-700 border-r">{{ $item->id_user }}</td>
            <td class="px-4 py-2 text-sm text-gray-700 border-r">{{ $item->nama_lengkap }}</td>
            <td class="px-4 py-2 text-sm text-gray-700 border-r">{{ $item->username }}</td>
            <td class="px-4 py-2 text-sm text-gray-700 border-r">{{ $item->role->nama_role ?? '-' }}</td>
            <td class="px-4 py-2 text-center">
              <div class="flex justify-center items-center gap-2">
                <a href="{{ route('master.data-user.edit', $item->id_user) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 hover:bg-blue-200">Edit</a>
                <form action="{{ route('master.data-user.destroy', $item->id_user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data user ini?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-rose-100 text-rose-700 hover:bg-rose-200">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">Tidak ada data user.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4 flex items-center justify-between">
    <div class="text-sm text-gray-600">
      @if(isset($users) && $users->total())
        Menampilkan {{ $users->firstItem() }} sampai {{ $users->lastItem() }} dari {{ $users->total() }} data
      @endif
    </div>
    <div>
      @if(isset($users) && method_exists($users, 'links'))
        {{ $users->appends(request()->query())->links() }}
      @endif
    </div>
  </div>
</div>
@endsection
