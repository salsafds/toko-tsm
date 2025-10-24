
<form action="{{ $action ?? '#' }}" method="POST" class="space-y-6" id="supplierForm"
      novalidate  {{-- Tambahkan ini untuk mencegah popup browser --}}
      data-provinsis-url="{{ route('master.data-supplier.provinsis', ':id_negara') }}"
      data-kotas-url="{{ route('master.data-supplier.kotas', ':id_provinsi') }}">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID Supplier --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">ID Supplier</label>
    <input type="text" name="id_supplier" value="{{ old('id_supplier', isset($supplier) ? $supplier->id_supplier : ($nextId ?? '')) }}"
           readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
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
    <label for="nama_supplier" class="block text-sm font-medium text-gray-700">
      Nama Supplier <span class="text-rose-600">*</span>
    </label>
    <input
      id="nama_supplier"
      name="nama_supplier"
      value="{{ old('nama_supplier', $supplier->nama_supplier ?? '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_supplier') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama supplier"
      aria-invalid="{{ $errors->has('nama_supplier') ? 'true' : 'false' }}"
    >
    @if ($errors->has('nama_supplier'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_supplier') }}</p>
    @else
      <p id="nama_supplier_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Contoh: PT Maju Jaya.</p>
    @endif
  </div>

  {{-- Alamat --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="alamat" class="block text-sm font-medium text-gray-700">
      Alamat <span class="text-rose-600">*</span>
    </label>
    <textarea
      id="alamat"
      name="alamat"
      rows="3"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('alamat') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan alamat supplier"
      aria-invalid="{{ $errors->has('alamat') ? 'true' : 'false' }}"
    >{{ old('alamat', $supplier->alamat ?? '') }}</textarea>
    @if ($errors->has('alamat'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('alamat') }}</p>
    @else
      <p id="alamat_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Masukkan alamat lengkap supplier.</p>
    @endif
  </div>

  {{-- Negara, Provinsi, Kota --}}
  <div class="grid grid-cols-3 gap-3">
    <div class="grid grid-cols-1 gap-1">
      <label for="id_negara" class="block text-sm font-medium text-gray-700">
        Negara <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_negara"
        name="id_negara"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_negara') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        data-selected="{{ old('id_negara', $supplier->id_negara ?? '') }}"
        aria-invalid="{{ $errors->has('id_negara') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Negara --</option>
        @foreach($negara as $n)
          <option value="{{ $n->id_negara }}" {{ old('id_negara', $supplier->id_negara ?? '') == $n->id_negara ? 'selected' : '' }}>
            {{ $n->nama_negara }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_negara'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_negara') }}</p>
      @else
        <p id="id_negara_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih negara supplier.</p>
      @endif
    </div>

    <div class="grid grid-cols-1 gap-1">
      <label for="id_provinsi" class="block text-sm font-medium text-gray-700">
        Provinsi <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_provinsi"
        name="id_provinsi"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_provinsi') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        data-selected="{{ old('id_provinsi', $supplier->id_provinsi ?? '') }}"
        aria-invalid="{{ $errors->has('id_provinsi') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Provinsi --</option>
        @if(isset($provinsi))
          @foreach($provinsi as $p)
            <option value="{{ $p->id_provinsi }}" {{ old('id_provinsi', $supplier->id_provinsi ?? '') == $p->id_provinsi ? 'selected' : '' }}>
              {{ $p->nama_provinsi }}
            </option>
          @endforeach
        @endif
      </select>
      @if ($errors->has('id_provinsi'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_provinsi') }}</p>
      @else
        <p id="id_provinsi_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih provinsi supplier.</p>
      @endif
    </div>

    <div class="grid grid-cols-1 gap-1">
      <label for="id_kota" class="block text-sm font-medium text-gray-700">
        Kota <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_kota"
        name="id_kota"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_kota') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        data-selected="{{ old('id_kota', $supplier->id_kota ?? '') }}"
        aria-invalid="{{ $errors->has('id_kota') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Kota --</option>
        @if(isset($kota))
          @foreach($kota as $k)
            <option value="{{ $k->id_kota }}" {{ old('id_kota', $supplier->id_kota ?? '') == $k->id_kota ? 'selected' : '' }}>
              {{ $k->nama_kota }}
            </option>
          @endforeach
        @endif
      </select>
      @if ($errors->has('id_kota'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_kota') }}</p>
      @else
        <p id="id_kota_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih kota supplier.</p>
      @endif
    </div>
  </div>

  {{-- Telepon dan Email --}}
  <div class="grid grid-cols-2 gap-3">
    <div class="grid grid-cols-1 gap-1">
      <label for="telepon_supplier" class="block text-sm font-medium text-gray-700">Telepon <span class="text-rose-600">*</span></label>
      <input
        id="telepon_supplier"
        name="telepon_supplier"
        value="{{ old('telepon_supplier', $supplier->telepon_supplier ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('telepon_supplier') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan nomor telepon"
      >
      @if ($errors->has('telepon_supplier'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('telepon_supplier') }}</p>
      @else
        <p id="telepon_supplier_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Gunakan format nomor yang valid (contoh: 08123456789).</p>
      @endif
    </div>

    <div>
      <label for="email_supplier" class="block text-sm font-medium text-gray-700">Email</label>
      <input
        id="email_supplier"
        name="email_supplier"
        value="{{ old('email_supplier', $supplier->email_supplier ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('email_supplier') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan email (opsional)"
      >
      @if ($errors->has('email_supplier'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('email_supplier') }}</p>
      @else
        <p id="email_supplier_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>
  </div>

  {{-- Tombol --}}
  <div class="flex items-center gap-3">
    <button id="submitButton" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 {{ isset($isEdit) && $isEdit ? 'disabled:opacity-50' : '' }}"
            {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}>
      @if(isset($supplier)) Update @else Simpan @endif
    </button>
    <a href="{{ route('master.data-supplier.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>