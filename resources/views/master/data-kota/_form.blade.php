<form action="{{ $action ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="kotaForm">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID (readonly) --}}
  <div class="grid grid-cols-1 gap-1">
    <label class="block text-sm font-medium text-gray-700">ID Kota</label>
    <input
      type="text"
      name="id_kota"
      value="{{ old('id_kota', isset($kota) ? $kota->id_kota : ($nextId ?? '')) }}"
      readonly
      class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
      aria-readonly="true"
    >
    <p class="text-xs text-gray-500">
      @if(isset($kota))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Negara & Provinsi --}}
  <div class="grid grid-cols-2 gap-3">
    <div class="grid grid-cols-1 gap-1">
      <label for="id_negara" class="block text-sm font-medium text-gray-700">Negara <span class="text-rose-600">*</span></label>
      <select
        id="id_negara"
        name="id_negara"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_negara') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_negara') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Negara --</option>
        @if(isset($negara))
          @foreach($negara as $n)
            <option value="{{ $n->id_negara }}" {{ old('id_negara', $kota->id_negara ?? '') == $n->id_negara ? 'selected' : '' }}>
              {{ $n->nama_negara }}
            </option>
          @endforeach
        @endif
      </select>

      @if ($errors->has('id_negara'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_negara') }}</p>
      @else
        <p id="id_negara_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih negara untuk kota ini.</p>
      @endif
    </div>

    <div class="grid grid-cols-1 gap-1">
      <label for="id_provinsi" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-rose-600">*</span></label>
      <select
        id="id_provinsi"
        name="id_provinsi"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_provinsi') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_provinsi') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Provinsi --</option>
        @if(isset($provinsi))
          @foreach($provinsi as $p)
            <option value="{{ $p->id_provinsi }}" {{ old('id_provinsi', $kota->id_provinsi ?? '') == $p->id_provinsi ? 'selected' : '' }}>
              {{ $p->nama_provinsi }}
            </option>
          @endforeach
        @endif
      </select>

      @if ($errors->has('id_provinsi'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_provinsi') }}</p>
      @else
        <p id="id_provinsi_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih provinsi untuk kota ini.</p>
      @endif
    </div>
  </div>

  {{-- Nama Kota --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="nama_kota" class="block text-sm font-medium text-gray-700">Nama Kota <span class="text-rose-600">*</span></label>
    <input
      id="nama_kota"
      name="nama_kota"
      value="{{ old('nama_kota', isset($kota) ? ($kota->nama_kota ?? '') : '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_kota') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama kota"
      aria-invalid="{{ $errors->has('nama_kota') ? 'true' : 'false' }}"
      autofocus
    >
    @if ($errors->has('nama_kota'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_kota') }}</p>
    @else
      <p id="nama_kota_error" class="text-sm text-red-600 mt-1 hidden"></p>
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
      @if(isset($kota))
        Update
      @else
        Simpan
      @endif
    </button>
    <a href="{{ route('master.data-kota.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>
