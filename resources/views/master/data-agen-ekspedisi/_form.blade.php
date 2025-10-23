
<form action="{{ $action ?? '#' }}" method="POST" class="space-y-6" id="agenForm"
      data-provinsis-url="{{ route('master.data-agen-ekspedisi.provinsis', ':id_negara') }}"
      data-kotas-url="{{ route('master.data-agen-ekspedisi.kotas', ':id_provinsi') }}">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID Agen --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">ID Agen Ekspedisi</label>
    <input type="text" name="id_ekspedisi" value="{{ old('id_ekspedisi', isset($agen) ? $agen->id_ekspedisi : ($nextId ?? '')) }}"
           readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">
      @if(isset($agen))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Nama Agen --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="nama_ekspedisi" class="block text-sm font-medium text-gray-700">
      Nama Agen Ekspedisi <span class="text-rose-600">*</span>
    </label>
    <input
      id="nama_ekspedisi"
      name="nama_ekspedisi"
      value="{{ old('nama_ekspedisi', $agen->nama_ekspedisi ?? '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_ekspedisi') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama agen ekspedisi"
      aria-invalid="{{ $errors->has('nama_ekspedisi') ? 'true' : 'false' }}"
    >
    @if ($errors->has('nama_ekspedisi'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_ekspedisi') }}</p>
    @else
      <p id="nama_ekspedisi_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Contoh: JNE, TIKI, POS Indonesia.</p>
    @endif
  </div>

  {{-- Negara, Provinsi, Kota --}}
  <div class="grid grid-cols-3 gap-3">
    <div class="grid grid-cols-1 gap-1">
      <label for="id_negara" class="block text-sm font-medium text-gray-700">Negara <span class="text-rose-600">*</span></label>
      <select id="id_negara" name="id_negara" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_negara') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
              data-selected="{{ old('id_negara', $agen->id_negara ?? '') }}">
        <option value="">-- Pilih Negara --</option>
        @foreach($negara as $n)
          <option value="{{ $n->id_negara }}" {{ old('id_negara', $agen->id_negara ?? '') == $n->id_negara ? 'selected' : '' }}>
            {{ $n->nama_negara }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_negara'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_negara') }}</p>
      @else
        <p id="id_negara_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih negara.</p>
      @endif
    </div>

    <div class="grid grid-cols-1 gap-1">
      <label for="id_provinsi" class="block text-sm font-medium text-gray-700">Provinsi <span class="text-rose-600">*</span></label>
      <select id="id_provinsi" name="id_provinsi" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_provinsi') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
              data-selected="{{ old('id_provinsi', $agen->id_provinsi ?? '') }}">
        <option value="">-- Pilih Provinsi --</option>
        @if(isset($provinsi))
          @foreach($provinsi as $p)
            <option value="{{ $p->id_provinsi }}" {{ old('id_provinsi', $agen->id_provinsi ?? '') == $p->id_provinsi ? 'selected' : '' }}>
              {{ $p->nama_provinsi }}
            </option>
          @endforeach
        @endif
      </select>
      @if ($errors->has('id_provinsi'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_provinsi') }}</p>
      @else
        <p id="id_provinsi_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih provinsi.</p>
      @endif
    </div>

    <div class="grid grid-cols-1 gap-1">
      <label for="id_kota" class="block text-sm font-medium text-gray-700">Kota <span class="text-rose-600">*</span></label>
      <select id="id_kota" name="id_kota" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_kota') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
              data-selected="{{ old('id_kota', $agen->id_kota ?? '') }}">
        <option value="">-- Pilih Kota --</option>
        @if(isset($kota))
          @foreach($kota as $k)
            <option value="{{ $k->id_kota }}" {{ old('id_kota', $agen->id_kota ?? '') == $k->id_kota ? 'selected' : '' }}>
              {{ $k->nama_kota }}
            </option>
          @endforeach
        @endif
      </select>
      @if ($errors->has('id_kota'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_kota') }}</p>
      @else
        <p id="id_kota_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih kota.</p>
      @endif
    </div>
  </div>

  {{-- Telepon dan Email --}}
  <div class="grid grid-cols-2 gap-3">
    <div class="grid grid-cols-1 gap-1">
      <label for="telepon_ekspedisi" class="block text-sm font-medium text-gray-700">Telepon <span class="text-rose-600">*</span></label>
      <input id="telepon_ekspedisi" name="telepon_ekspedisi" value="{{ old('telepon_ekspedisi', $agen->telepon_ekspedisi ?? '') }}"
             class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('telepon_ekspedisi') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
             placeholder="Masukkan nomor telepon">
      @if ($errors->has('telepon_ekspedisi'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('telepon_ekspedisi') }}</p>
      @else
        <p id="telepon_ekspedisi_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Gunakan format nomor yang valid (contoh: 08123456789).</p>
      @endif
    </div>

    <div>
      <label for="email_ekspedisi" class="block text-sm font-medium text-gray-700">Email</label>
      <input id="email_ekspedisi" name="email_ekspedisi" value="{{ old('email_ekspedisi', $agen->email_ekspedisi ?? '') }}"
             class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100"
             placeholder="Masukkan email (opsional)">
      <p id="email_ekspedisi_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @if ($errors->has('email_ekspedisi'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('email_ekspedisi') }}</p>
      @endif
    </div>
  </div>

  {{-- Tombol --}}
  <div class="flex items-center gap-3">
    <button id="submitButton" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 {{ isset($isEdit) && $isEdit ? 'disabled:opacity-50' : '' }}"
            {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}>
      @if(isset($agen)) Update @else Simpan @endif
    </button>
    <a href="{{ route('master.data-agen-ekspedisi.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>
