<form action="{{ $action ?? '#' }}" method="POST" class="space-y-6" id="pelangganForm"
      novalidate
      data-provinsis-url="{{ route('master.data-pelanggan.provinsis', ':id_negara') }}"
      data-kotas-url="{{ route('master.data-pelanggan.kotas', ':id_provinsi') }}">

    @csrf
    @if(isset($method) && strtoupper($method) === 'PUT')
        @method('PUT')
    @endif

    {{-- ID Pelanggan (readonly) --}}
    <div class="grid grid-cols-1 gap-1">
        <label class="block text-sm font-medium text-gray-700">ID Pelanggan</label>
        <input
            type="text"
            name="id_pelanggan"
            value="{{ old('id_pelanggan', isset($pelanggan) ? $pelanggan->id_pelanggan : ($nextId ?? '')) }}"
            readonly
            class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
            aria-readonly="true"
        >
        <p class="text-xs text-gray-500">
            @if(isset($pelanggan))
                ID tidak dapat diubah.
            @else
                ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
            @endif
        </p>
    </div>

    {{-- Kategori Pelanggan --}}
    <div class="grid grid-cols-1 gap-1">
        <label for="kategori_pelanggan" class="block text-sm font-medium text-gray-700">
            Kategori Pelanggan <span class="text-rose-600"></span>
        </label>
        <select
            name="kategori_pelanggan"
            id="kategori_pelanggan"
            class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('kategori_pelanggan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
            required
            aria-invalid="{{ $errors->has('kategori_pelanggan') ? 'true' : 'false' }}"
            data-selected="{{ old('kategori_pelanggan', $pelanggan->kategori_pelanggan ?? '') }}"
        >
            <option value="">-- Pilih Kategori --</option>
            @foreach($kategoriList as $value => $label)
                <option value="{{ $value }}"
                        {{ old('kategori_pelanggan', $pelanggan->kategori_pelanggan ?? '') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @if ($errors->has('kategori_pelanggan'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('kategori_pelanggan') }}</p>
        @else
            <p id="kategori_pelanggan_error" class="text-sm text-red-600 mt-1 hidden"></p>
            <p class="text-xs text-gray-500">Pilih kategori pelanggan.</p>
        @endif
    </div>

    {{-- Nama Pelanggan --}}
    <div class="grid grid-cols-1 gap-1">
        <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700">
            Nama Pelanggan <span class="text-rose-600">*</span>
        </label>
        <input
            id="nama_pelanggan"
            name="nama_pelanggan"
            value="{{ old('nama_pelanggan', isset($pelanggan) ? $pelanggan->nama_pelanggan : '') }}"
            class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_pelanggan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
            placeholder="Masukkan nama pelanggan"
            aria-invalid="{{ $errors->has('nama_pelanggan') ? 'true' : 'false' }}"
            autofocus
        >
        @if ($errors->has('nama_pelanggan'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_pelanggan') }}</p>
        @else
            <p id="nama_pelanggan_error" class="text-sm text-red-600 mt-1 hidden"></p>
            <p class="text-xs text-gray-500">Contoh: Budi Santoso atau PT Maju Jaya.</p>
        @endif
    </div>

    {{-- Nomor Telepon --}}
    <div class="grid grid-cols-1 gap-1">
        <label for="nomor_telepon" class="block text-sm font-medium text-gray-700">
            Nomor Telepon <span class="text-rose-600">*</span>
        </label>
        <input
            id="nomor_telepon"
            name="nomor_telepon"
            value="{{ old('nomor_telepon', isset($pelanggan) ? $pelanggan->nomor_telepon : '') }}"
            class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nomor_telepon') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
            placeholder="Masukkan nomor telepon"
            aria-invalid="{{ $errors->has('nomor_telepon') ? 'true' : 'false' }}"
        >
        @if ($errors->has('nomor_telepon'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('nomor_telepon') }}</p>
        @else
            <p id="nomor_telepon_error" class="text-sm text-red-600 mt-1 hidden"></p>
            <p class="text-xs text-gray-500">Gunakan format nomor yang valid (contoh: 08123456789).</p>
        @endif
    </div>

    {{-- Email Pelanggan --}}
    <div class="grid grid-cols-1 gap-1">
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email <span class="text-rose-600"></span></label>
        <input
            id="email_pelanggan"
            name="email_pelanggan"
            type="email"
            value="{{ old('email_pelanggan', isset($pelanggan) ? $pelanggan->email_pelanggan : '') }}"
            class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('email_pelanggan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
            placeholder="Masukkan email pelanggan (opsional)"
            aria-invalid="{{ $errors->has('email_pelanggan') ? 'true' : 'false' }}"
        >
        @if ($errors->has('email_pelanggan'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('email_pelanggan') }}</p>
        @else
            <p id="email_pelanggan_error" class="text-sm text-red-600 mt-1 hidden"></p>
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
                aria-invalid="{{ $errors->has('id_negara') ? 'true' : 'false' }}"
                data-selected="{{ old('id_negara', $pelanggan->id_negara ?? '') }}"
            >
                <option value="">-- Pilih Negara --</option>
                @if(isset($negara))
                    @foreach($negara as $n)
                        <option value="{{ $n->id_negara }}"
                                {{ old('id_negara', $pelanggan->id_negara ?? '') == $n->id_negara ? 'selected' : '' }}>
                            {{ $n->nama_negara }}
                        </option>
                    @endforeach
                @endif
            </select>
            @if ($errors->has('id_negara'))
                <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_negara') }}</p>
            @else
                <p id="id_negara_error" class="text-sm text-red-600 mt-1 hidden"></p>
                <p class="text-xs text-gray-500">Pilih negara untuk pelanggan ini.</p>
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
                aria-invalid="{{ $errors->has('id_provinsi') ? 'true' : 'false' }}"
                data-selected="{{ old('id_provinsi', $pelanggan->id_provinsi ?? '') }}"
            >
                <option value="">-- Pilih Provinsi --</option>
                @if(isset($provinsi))
                    @foreach($provinsi as $p)
                        <option value="{{ $p->id_provinsi }}"
                                {{ old('id_provinsi', $pelanggan->id_provinsi ?? '') == $p->id_provinsi ? 'selected' : '' }}>
                            {{ $p->nama_provinsi }}
                        </option>
                    @endforeach
                @endif
            </select>
            @if ($errors->has('id_provinsi'))
                <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_provinsi') }}</p>
            @else
                <p id="id_provinsi_error" class="text-sm text-red-600 mt-1 hidden"></p>
                <p class="text-xs text-gray-500">Pilih provinsi untuk pelanggan ini.</p>
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
                aria-invalid="{{ $errors->has('id_kota') ? 'true' : 'false' }}"
                data-selected="{{ old('id_kota', $pelanggan->id_kota ?? '') }}"
            >
                <option value="">-- Pilih Kota --</option>
                @if(isset($kota))
                    @foreach($kota as $k)
                        <option value="{{ $k->id_kota }}"
                                {{ old('id_kota', $pelanggan->id_kota ?? '') == $k->id_kota ? 'selected' : '' }}>
                            {{ $k->nama_kota }}
                        </option>
                    @endforeach
                @endif
            </select>
            @if ($errors->has('id_kota'))
                <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_kota') }}</p>
            @else
                <p id="id_kota_error" class="text-sm text-red-600 mt-1 hidden"></p>
                <p class="text-xs text-gray-500">Pilih kota untuk pelanggan ini.</p>
            @endif
        </div>
    </div>

    {{-- Alamat Pelanggan --}}
    <div class="grid grid-cols-1 gap-1">
        <label for="alamat_pelanggan" class="block text-sm font-medium text-gray-700">
            Alamat <span class="text-rose-600">*</span>
        </label>
        <textarea
            id="alamat_pelanggan"
            name="alamat_pelanggan"
            class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('alamat_pelanggan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
            placeholder="Masukkan alamat pelanggan"
            aria-invalid="{{ $errors->has('alamat_pelanggan') ? 'true' : 'false' }}"
        >{{ old('alamat_pelanggan', isset($pelanggan) ? $pelanggan->alamat_pelanggan : '') }}</textarea>
        @if ($errors->has('alamat_pelanggan'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('alamat_pelanggan') }}</p>
        @else
            <p id="alamat_pelanggan_error" class="text-sm text-red-600 mt-1 hidden"></p>
            <p class="text-xs text-gray-500">Masukkan alamat lengkap pelanggan.</p>
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
            @if(isset($pelanggan))
                Update
            @else
                Simpan
            @endif
        </button>
        <a href="{{ route('master.data-pelanggan.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
            Batal
        </a>
    </div>
</form>
