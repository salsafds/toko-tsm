_form.blade.php (pembalian
<form action="{{ isset($pembelian) ? route('admin.pembelian.update', $pembelian->id_pembelian) : route('admin.pembelian.store') }}" method="POST" class="space-y-6" id="pembelianForm">
  @csrf
  @if(isset($pembelian))
    @method('PUT')
  @endif

  {{-- ID Pembelian --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">ID Pembelian</label>
    <input type="text" name="id_pembelian" value="{{ old('id_pembelian', isset($pembelian) ? $pembelian->id_pembelian : ($nextId ?? '')) }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.</p>
  </div>

  {{-- Tanggal Pembelian --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="tanggal_pembelian" class="block text-sm font-medium text-gray-700">Tanggal Pembelian <span class="text-rose-600">*</span></label>
    <input id="tanggal_pembelian" name="tanggal_pembelian" type="date" value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('tanggal_pembelian') ? 'border-red-500 bg-red-50' : 'border-gray-200 focus:border-blue-500' }}">
    @if ($errors->has('tanggal_pembelian'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('tanggal_pembelian') }}</p>
    @endif
  </div>

  {{-- Supplier --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="id_supplier" class="block text-sm font-medium text-gray-700">Supplier <span class="text-rose-600">*</span></label>
    <select id="id_supplier" name="id_supplier" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_supplier') ? 'border-red-500 bg-red-50' : 'border-gray-200 focus:border-blue-500' }}">
      <option value="">-- Pilih Supplier --</option>
      @foreach($suppliers as $s)
        <option value="{{ $s->id_supplier }}" {{ old('id_supplier', $pembelian->id_supplier ?? '') == $s->id_supplier ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
      @endforeach
    </select>
    @if ($errors->has('id_supplier'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_supplier') }}</p>
    @endif
  </div>

  {{-- User --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="id_user" class="block text-sm font-medium text-gray-700">User <span class="text-rose-600">*</span></label>
    <select id="id_user" name="id_user" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_user') ? 'border-red-500 bg-red-50' : 'border-gray-200 focus:border-blue-500' }}">
      <option value="">-- Pilih User --</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}" {{ old('id_user', $pembelian->id_user ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
      @endforeach
    </select>
    @if ($errors->has('id_user'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_user') }}</p>
    @endif
  </div>

  {{-- Jenis Pembayaran --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
    <select id="jenis_pembayaran" name="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('jenis_pembayaran') ? 'border-red-500 bg-red-50' : 'border-gray-200 focus:border-blue-500' }}">
      <option value="">-- Pilih Jenis --</option>
      <option value="cash" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'cash' ? 'selected' : '' }}>Cash/Tunai</option>
      <option value="kredit" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'kredit' ? 'selected' : '' }}>Kredit</option>
    </select>
    @if ($errors->has('jenis_pembayaran'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('jenis_pembayaran') }}</p>
    @endif
  </div>

  {{-- Jumlah Bayar --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700">Jumlah Bayar <span class="text-rose-600">*</span></label>
    <input id="jumlah_bayar" name="jumlah_bayar" type="number" step="0.01" value="{{ old('jumlah_bayar', $pembelian->jumlah_bayar ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('jumlah_bayar') ? 'border-red-500 bg-red-50' : 'border-gray-200 focus:border-blue-500' }}">
    @if ($errors->has('jumlah_bayar'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('jumlah_bayar') }}</p>
    @endif
  </div>

  {{-- Tanggal Terima --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="tanggal_terima" class="block text-sm font-medium text-gray-700">Tanggal Terima</label>
    <input id="tanggal_terima" name="tanggal_terima" type="date" value="{{ old('tanggal_terima', $pembelian->tanggal_terima ?? '') }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">Tanggal terima akan diisi otomatis saat selesai.</p>
  </div>

  {{-- Checkbox untuk Detail --}}
  <div class="flex items-center">
    <input id="show_details" type="checkbox" class="rounded border-gray-300">
    <label for="show_details" class="ml-2 text-sm text-gray-700">Tambahkan Detail Barang</label>
  </div>

{{-- Section Detail Barang --}}
<div id="detail_section" class="hidden space-y-4">
  <h3 class="text-lg font-medium text-gray-800">Detail Barang</h3>
  <div id="detail_container">
    {{-- Baris detail awal --}}
    <div class="detail_row grid grid-cols-3 gap-3 items-end">
      <div>
        <label class="block text-sm font-medium text-gray-700">Barang</label>
        <select name="details[0][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
          <option value="">-- Pilih Barang --</option>
          @foreach($barangs as $b)
            <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Kuantitas</label>
        <input name="details[0][kuantitas]" type="number" min="1" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
      </div>
      <div class="flex items-center gap-2">
        <button type="button" class="add_detail inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Tambah</button>
        <button type="button" class="remove_detail items-center px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 hidden">Hapus</button>
        </div>
    </div>
  </div>
</div>

{{-- Tombol --}}
<div class="flex items-center gap-3">
  <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800" id="submitButton">
    @if(isset($pembelian))
      Update
    @else
      Simpan
    @endif
  </button>
  <a href="{{ route('admin.pembelian.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#pembelianForm');
  const showDetailsCheckbox = document.querySelector('#show_details');
  const detailSection = document.querySelector('#detail_section');
  const detailContainer = document.querySelector('#detail_container');
  const submitButton = document.querySelector('#submitButton');

  // Toggle detail section
  showDetailsCheckbox.addEventListener('change', function () {
    if (this.checked) {
      detailSection.classList.remove('hidden');
    } else {
      detailSection.classList.add('hidden');
    }
  });

  // Add detail row
  detailContainer.addEventListener('click', function (e) {
    if (e.target.classList.contains('add_detail')) {
      const row = e.target.closest('.detail_row');
      const newRow = row.cloneNode(true);
      const index = detailContainer.children.length;
      newRow.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\$\d+\$/, `[${index}]`);
        el.value = '';
      });
      newRow.querySelector('.remove_detail').classList.remove('hidden');
      detailContainer.appendChild(newRow);
    }

    if (e.target.classList.contains('remove_detail')) {
      e.target.closest('.detail_row').remove();
    }
  });

  // Form validation
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    let hasError = false;

    // Reset errors
    document.querySelectorAll('.text-red-600').forEach(el => el.remove());
    document.querySelectorAll('input, select').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

    // Validate required fields
    const requiredFields = ['tanggal_pembelian', 'id_supplier', 'id_user', 'jenis_pembayaran', 'jumlah_bayar'];
    requiredFields.forEach(field => {
      const el = document.querySelector(`[name="${field}"]`);
      if (!el.value) {
        el.classList.add('border-red-500', 'bg-red-50');
        el.insertAdjacentHTML('afterend', '<p class="text-sm text-red-600 mt-1">Field ini wajib diisi.</p>');
        hasError = true;
      }
    });

    if (hasError) return;

    if (confirm('Apakah Anda yakin ingin menyimpan data pembelian ini?')) {
      form.submit();
    }
  });
});
</script>