<form action="{{ $action ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="roleForm">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID (readonly) --}}
  <div class="grid grid-cols-1 gap-1">
    <label class="block text-sm font-medium text-gray-700">ID Role</label>
    <input
      type="text"
      name="id_role"
      value="{{ old('id_role', isset($role) ? $role->id_role : ($nextId ?? '')) }}"
      readonly
      class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
      aria-readonly="true"
    >
    <p class="text-xs text-gray-500">
      @if(isset($role))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Nama Role --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="nama_role" class="block text-sm font-medium text-gray-700">Nama Role <span class="text-rose-600">*</span></label>
    <input
      id="nama_role"
      name="nama_role"
      value="{{ old('nama_role', isset($role) ? ($role->nama_role ?? '') : '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_role') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama role"
      aria-invalid="{{ $errors->has('nama_role') ? 'true' : 'false' }}"
      autofocus
    >
    @if ($errors->has('nama_role'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_role') }}</p>
    @else
      <p id="nama_role_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Contoh: Admin, Pengguna, Manajer.</p>
    @endif
  </div>

  {{-- Keterangan --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan <span class="text-rose-600">*</span></label>
    <textarea
      id="keterangan"
      name="keterangan"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('keterangan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan keterangan role"
      rows="4"
      aria-invalid="{{ $errors->has('keterangan') ? 'true' : 'false' }}"
    >{{ old('keterangan', isset($role) ? ($role->keterangan ?? '') : '') }}</textarea>
    @if ($errors->has('keterangan'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('keterangan') }}</p>
    @else
      <p id="keterangan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Masukkan deskripsi singkat tentang role ini.</p>
    @endif
  </div>

  {{-- Submit / Cancel --}}
  <div class="flex items-center gap-3">
    <button 
      type="submit" 
      class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 {{ isset($isEdit) && $isEdit ? 'disabled:opacity-50' : '' }}"
      {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}
      id="submitButton"
    >
      @if(isset($role))
        Update
      @else
        Simpan
      @endif
    </button>
    <a href="{{ route('master.data-role.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>