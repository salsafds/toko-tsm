<form action="{{ $action ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="negaraForm">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID (readonly) --}}
  <div class="grid grid-cols-1 gap-1">
    <label class="block text-sm font-medium text-gray-700">ID Negara</label>
    <input
      type="text"
      name="id_negara"
      value="{{ old('id_negara', isset($negara) ? $negara->negara : ($nextId ?? '')) }}"
      readonly
      class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
      aria-readonly="true"
    >
    <p class="text-xs text-gray-500">
      @if(isset($negara))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Nama Negara --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="nama_negara" class="block text-sm font-medium text-gray-700">Nama Negara <span class="text-rose-600">*</span></label>
    <input
      id="nama_negara"
      name="nama_negara"
      value="{{ old('nama_negara', isset($negara) ? ($negara->nama_negara ?? '') : '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_negara') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama negara"
      aria-invalid="{{ $errors->has('nama_negara') ? 'true' : 'false' }}"
      autofocus
    >
    @if ($errors->has('nama_negara'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_negara') }}</p>
    @else
      <p id="nama_negara_error" class="text-sm text-red-600 mt-1 hidden"></p>
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
      @if(isset($negara))
        Update
      @else
        Simpan
      @endif
    </button>
    <a href="{{ route('master.data-negara.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>