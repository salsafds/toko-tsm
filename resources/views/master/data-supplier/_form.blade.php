<form action="{{ $action ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="supplierForm">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID (readonly) --}}
  <div class="grid grid-cols-1 gap-1">
    <label class="block text-sm font-medium text-gray-700">ID Supplier</label>
    <input
      type="text"
      name="id_supplier"
      value="{{ old('id_supplier', isset($supplier) ? $supplier->id_supplier : ($nextId ?? '')) }}"
      readonly
      class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
      aria-readonly="true"
    >
    <p class="text-xs text-gray-500">
      @if(isset($supplier))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Nama Supplier --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="nama_supplier" class="block text-sm font-medium text-gray-700">Nama Supplier <span class="text-rose-600">*</span></label>
    <input
      id="nama_supplier"
      name="nama_supplier"
      value="{{ old('nama_supplier', isset($supplier) ? ($supplier->nama_supplier ?? '') : '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_supplier') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama supplier"
      aria-invalid="{{ $errors->has('nama_supplier') ? 'true' : 'false' }}"
      autofocus
    >
    @if ($errors->has('nama_supplier'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_supplier') }}</p>
    @else
      <p id="nama_supplier_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Nama perusahaan atau pemasok.</p>
    @endif
  </div>

  {{-- Alamat --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat <span class="text-rose-600">*</span></label>
    <textarea
      id="alamat"
      name="alamat"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('alamat') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan alamat supplier"
      rows="3"
      aria-invalid="{{ $errors->has('alamat') ? 'true' : 'false' }}"
    >{{ old('alamat', isset($supplier) ? ($supplier->alamat ?? '') : '') }}</textarea>
    @if ($errors->has('alamat'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('alamat') }}</p>
    @else
      <p id="alamat_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Alamat lengkap supplier.</p>
    @endif
  </div>

  {{-- Negara, Provinsi, Kota (sederhana: input id, you can convert to select later) --}}
  <div class="grid grid-cols-3 gap-3">
    <div>
      <label for="id_negara" class="block text-sm font-medium text-gray-700">ID Negara</label>
      <input id="id_negara" name="id_negara" value="{{ old('id_negara', isset($supplier) ? ($supplier->id_negara ?? '') : '') }}" class="w-full rounded-md border px-3 py-2 text-sm" placeholder="ID Negara (opsional)">
    </div>
    <div>
      <label for="id_provinsi" class="block text-sm font-medium text-gray-700">ID Provinsi</label>
      <input id="id_provinsi" name="id_provinsi" value="{{ old('id_provinsi', isset($supplier) ? ($supplier->id_provinsi ?? '') : '') }}" class="w-full rounded-md border px-3 py-2 text-sm" placeholder="ID Provinsi (opsional)">
    </div>
    <div>
      <label for="id_kota" class="block text-sm font-medium text-gray-700">ID Kota</label>
      <input id="id_kota" name="id_kota" value="{{ old('id_kota', isset($supplier) ? ($supplier->id_kota ?? '') : '') }}" class="w-full rounded-md border px-3 py-2 text-sm" placeholder="ID Kota (opsional)">
    </div>
  </div>

  {{-- Telepon & Email --}}
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label for="telepon_supplier" class="block text-sm font-medium text-gray-700">Telepon</label>
      <input id="telepon_supplier" name="telepon_supplier" value="{{ old('telepon_supplier', isset($supplier) ? ($supplier->telepon_supplier ?? '') : '') }}" class="w-full rounded-md border px-3 py-2 text-sm" placeholder="No. telepon (opsional)">
    </div>
    <div>
      <label for="email_supplier" class="block text-sm font-medium text-gray-700">Email</label>
      <input id="email_supplier" name="email_supplier" value="{{ old('email_supplier', isset($supplier) ? ($supplier->email_supplier ?? '') : '') }}" class="w-full rounded-md border px-3 py-2 text-sm" placeholder="Email (opsional)">
    </div>
  </div>

  {{-- Submit / Cancel --}}
  <div class="flex items-center gap-3">
    <button
      type="submit"
      class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 {{ isset($isEdit) && $isEdit ? 'disabled:opacity-50' : '' }}"
      {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}
      id="submitButton">
      @if(isset($supplier)) Update @else Simpan @endif
    </button>

    <a href="{{ route('master.data-supplier.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>
