@php
  // $satuan (nullable) - model saat edit
  // $action - URL form action
  // $method - 'POST' or 'PUT'
@endphp

<form action="{{ $action ?? '#' }}" method="POST" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID (disabled) --}}
  <div class="grid grid-cols-1 gap-1">
    <label class="block text-sm font-medium text-gray-700">ID Satuan</label>
    <input
      type="text"
      name="id"
      value="{{ old('id', isset($satuan) ? $satuan->id : 'Auto-generated') }}"
      disabled
      class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
      aria-disabled="true"
    >
    <p class="text-xs text-gray-500">ID dibuat otomatis saat data disimpan.</p>
  </div>

  {{-- Nama Satuan --}}
  @php
    $hasNameError = $errors->has('nama');
  @endphp

  <div class="grid grid-cols-1 gap-1">
    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Satuan</label>
    <input
      id="nama"
      name="nama"
      value="{{ old('nama', $satuan->nama ?? '') }}"
      class="w-full rounded-md px-3 py-2 text-sm
        {{ $hasNameError ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
      placeholder="Masukkan nama satuan"
      aria-invalid="{{ $hasNameError ? 'true' : 'false' }}"
      autofocus
    >
    @if($hasNameError)
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama') }}</p>
    @else
      <p class="text-xs text-gray-500">Contoh: kg, liter, buah, pack.</p>
    @endif
  </div>

  {{-- Submit / Cancel --}}
  <div class="flex items-center gap-3">
    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800">
      Simpan
    </button>
    <a href="{{ url()->previous() ?? (route('master.dataSatuan.index') ?? '#') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>
