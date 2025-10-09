<form action="{{ $action ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="satuanForm">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID (readonly) --}}
  <div class="grid grid-cols-1 gap-1">
    <label class="block text-sm font-medium text-gray-700">ID Satuan</label>
    <input
      type="text"
      name="id_satuan"
      value="{{ old('id_satuan', isset($satuan) ? $satuan->id_satuan : ($nextId ?? '')) }}"
      readonly
      class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
      aria-readonly="true"
    >
    <p class="text-xs text-gray-500">
      @if(isset($satuan))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Nama Satuan --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="nama_satuan" class="block text-sm font-medium text-gray-700">Nama Satuan <span class="text-rose-600">*</span></label>
    <input
      id="nama_satuan"
      name="nama_satuan"
      value="{{ old('nama_satuan', isset($satuan) ? ($satuan->nama_satuan ?? '') : '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_satuan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama satuan"
      aria-invalid="{{ $errors->has('nama_satuan') ? 'true' : 'false' }}"
      autofocus
    >
    @if ($errors->has('nama_satuan'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_satuan') }}</p>
    @else
      <p id="nama_satuan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Contoh: kg, liter, buah, pack.</p>
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
      @if(isset($satuan))
        Update
      @else
        Simpan
      @endif
    </button>
    <a href="{{ route('master.data-satuan.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>