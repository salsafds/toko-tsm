<form action="{{ $action ?? '#' }}" method="POST" class="space-y-6" id="userForm">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID User --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">ID User</label>
    <input type="text" name="id_user" value="{{ old('id_user', isset($user) ? $user->id_user : ($nextId ?? '')) }}"
           readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">
      @if(isset($user))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Nama Lengkap --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-rose-600">*</span></label>
    <input id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}"
      class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_lengkap') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama lengkap" aria-invalid="{{ $errors->has('nama_lengkap') ? 'true' : 'false' }}">
    @if($errors->has('nama_lengkap'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_lengkap') }}</p>
    @else
      <p id="nama_lengkap_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Contoh: Budi Santoso.</p>
    @endif
  </div>

  {{-- Alamat dan Telepon --}}
  <div class="grid grid-cols-2 gap-3">
    <div class="grid grid-cols-1 gap-1">
        <label for="alamat_user" class="block text-sm font-medium text-gray-700">Alamat</label>
        <textarea id="alamat_user" name="alamat_user" rows="2"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('alamat_user') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan alamat (opsional)">{{ old('alamat_user', $user->alamat_user ?? '') }}</textarea>
        @if($errors->has('alamat_user'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('alamat_user') }}</p>
        @else
            <p class="text-xs text-gray-500">Alamat lengkap (opsional).</p>
        @endif
    </div>
    
    <div class="grid grid-cols-1 gap-1">
      <label for="telepon" class="block text-sm font-medium text-gray-700">Telepon</label>
      <input id="telepon" name="telepon" value="{{ old('telepon', $user->telepon ?? '') }}"
             class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('telepon') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
             placeholder="Masukkan nomor telepon (opsional)">
      @if($errors->has('telepon'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('telepon') }}</p>
      @else
        <p id="telepon_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: 08123456789 (opsional).</p>
      @endif
    </div>
  </div>

  {{-- Username dan Password --}}
<div class="grid grid-cols-2 gap-3">
  <div class="grid grid-cols-1 gap-1">
    <label for="username_input" class="block text-sm font-medium text-gray-700">Username <span class="text-rose-600">*</span></label>
   <input id="username_input" name="username" value="{{ old('username', $user->username ?? '') }}"
       class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('username') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
       placeholder="Masukkan username"
       autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
      @if($errors->has('username'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('username') }}</p>
      @else
        <p id="username_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
  </div>

  <div class="grid grid-cols-1 gap-1">
    <label for="password_input" class="block text-sm font-medium text-gray-700">
      Password <span class="text-rose-600">*</span> @if(isset($user)) <span class="text-xs text-gray-500"> (biarkan kosong jika tidak ingin mengganti)</span> @endif
    </label>
    <input id="password_input" name="password" type="password"
           class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('password') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
           placeholder="@if(isset($user)) Masukkan password baru (opsional) @else Masukkan password @endif"
           autocomplete="new-password">
    @if($errors->has('password'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('password') }}</p>
    @else
      <p id="password_error" class="text-sm text-red-600 mt-1 hidden"></p>
    @endif
  </div>
</div>

  {{-- Jenis Kelamin dan Status --}}
  <div class="grid grid-cols-2 gap-3">
    <div class="grid grid-cols-1 gap-1">
        <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-rose-600">*</span></label>
        <select id="status" name="status" class="w-full rounded-md border px-3 py-2 text-sm">
            @php $st = old('status', $user->status ?? 'aktif') @endphp
            <option value="aktif" {{ $st === 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ $st === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            <option value="cuti" {{ $st === 'cuti' ? 'selected' : '' }}>Cuti</option>
        </select>
        @if ($errors->has('status'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('status') }}</p>
        @else
            <p id="status_error" class="text-sm text-red-600 mt-1 hidden"></p>
        @endif
    </div>
    
    <div class="grid grid-cols-1 gap-1">
        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin <span class="text-rose-600">*</span></label>
        <div class="flex items-center gap-5">
            @php $jk = old('jenis_kelamin', $user->jenis_kelamin ?? '') @endphp
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="jenis_kelamin" value="laki-laki" {{ $jk === 'laki-laki' ? 'checked' : '' }} class="form-radio">
                <span class="text-sm text-gray-700">Laki-laki</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="jenis_kelamin" value="perempuan" {{ $jk === 'perempuan' ? 'checked' : '' }} class="form-radio">
                <span class="text-sm text-gray-700">Perempuan</span>
            </label>
        </div>
        @if ($errors->has('jenis_kelamin'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('jenis_kelamin') }}</p>
        @else
            <p id="jenis_kelamin_error" class="text-sm text-red-600 mt-1 hidden"></p>
        @endif
    </div>
  </div>
  
  {{-- Tanggal Masuk / Keluar --}}
  <div class="grid grid-cols-2 gap-3">
    <div>
      <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700">Tanggal Masuk <span class="text-rose-600">*</span></label>
      <input id="tanggal_masuk" name="tanggal_masuk" type="date" value="{{ old('tanggal_masuk', isset($user) ? $user->tanggal_masuk : '') }}"
             class="w-full rounded-md border px-3 py-2 text-sm">
      @if ($errors->has('tanggal_masuk'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('tanggal_masuk') }}</p>
      @else
        <p id="tanggal_masuk_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>

    <div>
      <label for="tanggal_keluar" class="block text-sm font-medium text-gray-700">Tanggal Keluar</label>
      <input id="tanggal_keluar" name="tanggal_keluar" type="date" value="{{ old('tanggal_keluar', isset($user) ? $user->tanggal_keluar : '') }}"
             class="w-full rounded-md border px-3 py-2 text-sm">
      @if ($errors->has('tanggal_keluar'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('tanggal_keluar') }}</p>
      @else
        <p id="tanggal_keluar_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>
  </div>

  {{-- Role, Jabatan, Pendidikan --}}
  <div class="grid grid-cols-3 gap-3">
    <div>
      <label for="id_role" class="block text-sm font-medium text-gray-700">Role <span class="text-rose-600">*</span></label>
      <select id="id_role" name="id_role" class="w-full rounded-md border px-3 py-2 text-sm">
        <option value="">-- Pilih Role --</option>
        @foreach($roles as $r)
          <option value="{{ $r->id_role }}" {{ old('id_role', $user->id_role ?? '') == $r->id_role ? 'selected' : '' }}>
            {{ $r->nama_role }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_role'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_role') }}</p>
      @else
        <p id="id_role_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>

    <div>
      <label for="id_jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
      <select id="id_jabatan" name="id_jabatan" class="w-full rounded-md border px-3 py-2 text-sm">
        <option value="">-- Pilih Jabatan (opsional) --</option>
        @foreach($jabatans as $j)
          <option value="{{ $j->id_jabatan }}" {{ old('id_jabatan', $user->id_jabatan ?? '') == $j->id_jabatan ? 'selected' : '' }}>
            {{ $j->nama_jabatan }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_jabatan'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_jabatan') }}</p>
      @else
        <p id="id_jabatan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>

    <div>
      <label for="id_pendidikan" class="block text-sm font-medium text-gray-700">Pendidikan</label>
      <select id="id_pendidikan" name="id_pendidikan" class="w-full rounded-md border px-3 py-2 text-sm">
        <option value="">-- Pilih Pendidikan (opsional) --</option>
        @foreach($pendidikans as $p)
          <option value="{{ $p->id_pendidikan }}" {{ old('id_pendidikan', $user->id_pendidikan ?? '') == $p->id_pendidikan ? 'selected' : '' }}>
            {{ $p->tingkat_pendidikan }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_pendidikan'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_pendidikan') }}</p>
      @else
        <p id="id_pendidikan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>
  </div>


  {{-- Tombol --}}
  <div class="flex items-center gap-3">
    <button id="submitButton" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800">
      @if(isset($user)) Update @else Simpan @endif
    </button>
    <a href="{{ route('master.data-user.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>
