<form action="{{ isset($barang) ? route('admin.data-barang.update', $barang->id_barang) : route('admin.data-barang.store') }}" method="POST" class="space-y-6" id="barangForm">
  @csrf
  @if(isset($barang))
    @method('PUT')
  @endif
  
  <div class="grid grid-cols-2 gap-4">
    {{-- ID Barang --}}
    <div>
      <label class="block text-sm font-medium text-gray-700">ID Barang</label>
      <input type="text" name="id_barang" value="{{ old('id_barang', isset($barang) ? $barang->id_barang : ($nextId ?? '')) }}"
             readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
      <p class="text-xs text-gray-500">
        @if(isset($barang))
          ID tidak dapat diubah.
        @else
          ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
        @endif
      </p>
    </div>

    {{-- Nama Barang --}}
    <div>
      <label for="nama_barang" class="block text-sm font-medium text-gray-700">
        Nama Barang <span class="text-rose-600">*</span>
      </label>
      <input
        id="nama_barang"
        name="nama_barang"
        value="{{ old('nama_barang', $barang->nama_barang ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_barang') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan nama barang"
        aria-invalid="{{ $errors->has('nama_barang') ? 'true' : 'false' }}"
      >
      @if ($errors->has('nama_barang'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_barang') }}</p>
      @else
        <p id="nama_barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: Laptop ASUS X550Z.</p>
      @endif
    </div>

    {{-- Kode SKU --}}
    <div>
      <label for="sku" class="block text-sm font-medium text-gray-700">
        Kode SKU <span class="text-rose-600">*</span>
      </label>
      <input
        id="sku"
        name="sku"
        value="{{ old('sku', $barang->sku ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('sku') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan kode SKU"
        aria-invalid="{{ $errors->has('sku') ? 'true' : 'false' }}"
      >
      @if ($errors->has('sku'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('sku') }}</p>
      @else
        <p id="sku_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: TS-BL-M-001</p>
      @endif
    </div>

    {{-- Kategori Barang --}}
    <div>
      <label for="id_kategori_barang" class="block text-sm font-medium text-gray-700">
        Kategori Barang <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_kategori_barang"
        name="id_kategori_barang"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_kategori_barang') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_kategori_barang') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Kategori --</option>
        @foreach($kategoriBarang as $k)
          <option value="{{ $k->id_kategori_barang }}" {{ old('id_kategori_barang', $barang->id_kategori_barang?? '') == $k->id_kategori_barang ? 'selected' : '' }}>
            {{ $k->nama_kategori }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_kategori_barang'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_kategori_barang') }}</p>
      @else
        <p id="id_kategori_barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih kategori barang.</p>
      @endif
    </div>

    {{-- Supplier --}}
    <div>
      <label for="id_supplier" class="block text-sm font-medium text-gray-700">
        Supplier <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_supplier"
        name="id_supplier"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_supplier') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_supplier') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Supplier --</option>
        @foreach($supplier as $s)
          <option value="{{ $s->id_supplier }}" {{ old('id_supplier', $barang->id_supplier ?? '') == $s->id_supplier ? 'selected' : '' }}>
            {{ $s->nama_supplier }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_supplier'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_supplier') }}</p>
      @else
        <p id="id_supplier_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih supplier barang.</p>
      @endif
    </div>

    {{-- Satuan --}}
    <div>
      <label for="id_satuan" class="block text-sm font-medium text-gray-700">
        Satuan <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_satuan"
        name="id_satuan"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_satuan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_satuan') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Satuan --</option>
        @foreach($satuan as $s)
          <option value="{{ $s->id_satuan }}" {{ old('id_satuan', $barang->id_satuan ?? '') == $s->id_satuan ? 'selected' : '' }}>
            {{ $s->nama_satuan }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_satuan'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_satuan') }}</p>
      @else
        <p id="id_satuan_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih satuan barang.</p>
      @endif
    </div>

    {{-- Merk Barang --}}
    <div>
      <label for="merk_barang" class="block text-sm font-medium text-gray-700">
        Merk Barang
      </label>
      <input
        id="merk_barang"
        name="merk_barang"
        value="{{ old('merk_barang', $barang->merk_barang ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('merk_barang') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan merk barang (opsional)"
      >
      @if ($errors->has('merk_barang'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('merk_barang') }}</p>
      @else
        <p id="merk_barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: ASUS, Samsung.</p>
      @endif
    </div>

    {{-- Berat --}}
    <div>
      <label for="berat" class="block text-sm font-medium text-gray-700">Berat <span class="text-rose-600">*</span>
      </label>
      <input
        id="berat"
        name="berat"
        type="number"
        step="0.01"
        value="{{ old('berat', $barang->berat ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('berat') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan berat barang"
      >
      @if ($errors->has('berat'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('berat') }}</p>
      @else
        <p id="berat_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: 1.5 (dalam kilogram).</p>
      @endif
    </div>

    {{-- Margin --}}
    <div>
      <label for="margin" class="block text-sm font-medium text-gray-700">
        Margin (%)
      </label>
      <input
        id="margin"
        name="margin"
        type="number"
        step="0.01"
        min="0"
        max="100"
        value="{{ old('margin', $barang->margin ?? 0) }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('margin') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan margin dalam persen (contoh: 30.00)"
      >
      @if ($errors->has('margin'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('margin') }}</p>
      @else
        <p id="margin_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Margin keuntungan dalam persen (0-100). Default: 0.</p>
      @endif
    </div>
  </div>

  {{-- Tombol --}}
  <div class="flex items-center gap-3">
    <button 
      type="submit" 
      class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800"
      id="submitButton"
    >
      @if(isset($barang))
        Update
      @else
        Simpan
      @endif
    </button>
    <a href="{{ route('admin.data-barang.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>