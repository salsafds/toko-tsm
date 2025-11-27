<form action="{{ isset($pembelian) ? route('admin.pembelian.update', $pembelian->id_pembelian) : route('admin.pembelian.store') }}"
      method="POST" class="space-y-6" id="pembelianForm">
  @csrf
  @if(isset($pembelian))
    @method('PUT')
  @endif
  {{-- ID Pembelian --}}
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">ID Pembelian</label>
    <input type="text" name="id_pembelian"
           value="{{ old('id_pembelian', isset($pembelian) ? $pembelian->id_pembelian : ($nextId ?? '')) }}"
           readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.</p>
  </div>
  {{-- Supplier --}}
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier <span class="text-rose-600">*</span></label>
    <select id="id_supplier" name="id_supplier" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
      <option value="">-- Pilih Supplier --</option>
      @foreach($suppliers as $s)
        <option value="{{ $s->id_supplier }}" {{ old('id_supplier', $pembelian->id_supplier ?? '') == $s->id_supplier ? 'selected' : '' }}>
          {{ $s->nama_supplier }}
        </option>
      @endforeach
    </select>
    <p id="id_supplier_error" class="text-xs text-rose-600 mt-1 hidden"></p>
    <p class="text-xs text-gray-500">Pilih supplier.</p>
  </div>
  {{-- User --}}
  <input type="hidden" name="id_user" value="{{ auth()->id() }}">
  {{-- Section Detail Barang --}}
  <div id="barangContainer">
    <label class="block text-sm font-medium text-gray-700 mb-1">Barang <span class="text-rose-600">*</span></label>
    <p class="text-xs text-gray-500 mb-3">Pilih barang, harga beli, dan kuantitas untuk pembelian.</p>
    @php
        $barangIndex = 0;
        $isEdit = isset($pembelian) && $pembelian->detailPembelian->count() > 0;
    @endphp
    @if($isEdit)
        @foreach($pembelian->detailPembelian as $detail)
            <div class="grid grid-cols-12 gap-2 mb-2 barang-row items-center">
                <div class="col-span-4">
                    <select name="details[{{ $barangIndex }}][id_barang]"
                            class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($barangs as $b)
                            <option value="{{ $b->id_barang }}" data-harga="{{ $b->harga_beli }}"
                                    {{ $detail->id_barang == $b->id_barang ? 'selected' : '' }}>
                                {{ $b->nama_barang }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-red-600 mt-1 hidden" data-error="id_barang_{{ $barangIndex }}"></p>
                </div>
                <div class="col-span-3">
                  <input type="text" 
                      name="details[{{ $barangIndex }}][harga_beli]"
                      value="{{ old('details.' . $barangIndex . '.harga_beli', intval($detail->harga_beli)) }}"
                      class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 harga-beli-input"
                      placeholder="Harga Beli">
                  <p class="text-sm text-red-600 mt-1 hidden" data-error="harga_beli_{{ $barangIndex }}"></p>
                </div>
                <div class="col-span-3">
                    <input type="number" name="details[{{ $barangIndex }}][kuantitas]"
                           value="{{ old('details.' . $barangIndex . '.kuantitas', $detail->kuantitas) }}"
                           class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1"
                           placeholder="Qty">
                    <p class="text-sm text-red-600 mt-1 hidden" data-error="kuantitas_{{ $barangIndex }}"></p>
                </div>
                <div class="col-span-2 flex items-center gap-4 max-w-[6rem]">
                    <button type="button"
                            class="remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">
                        -
                    </button>
                    @if($loop->last)
                        <button type="button"
                                class="add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">
                            +
                        </button>
                    @endif
                </div>
                @if($detail->id_detail_pembelian)
                    <input type="hidden" name="details[{{ $barangIndex }}][id_detail_pembelian]"
                           value="{{ $detail->id_detail_pembelian }}">
                @endif
            </div>
            @php $barangIndex++ @endphp
        @endforeach
    @else
        <div class="grid grid-cols-12 gap-2 mb-2 barang-row items-center">
            <div class="col-span-4">
                <select name="details[0][id_barang]"
                        class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $b)
                        <option value="{{ $b->id_barang }}" data-harga="{{ $b->harga_beli }}">
                            {{ $b->nama_barang }}
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-red-600 mt-1 hidden" data-error="id_barang_0"></p>
            </div>
            <div class="col-span-3">
              <input type="text" name="details[0][harga_beli]"
                    class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 harga-beli-input"
                    placeholder="Harga Beli">
              <p class="text-sm text-red-600 mt-1 hidden" data-error="harga_beli_0"></p>
            </div>
            <div class="col-span-3">
                <input type="number" name="details[0][kuantitas]"
                       class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1"
                       placeholder="Qty">
                <p class="text-sm text-red-600 mt-1 hidden" data-error="kuantitas_0"></p>
            </div>
            <div class="col-span-2 flex items-center gap-4 max-w-[6rem]">
                <button type="button"
                        class="remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">
                    -
                </button>
                <button type="button"
                        class="add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">
                    +
                </button>
            </div>
        </div>
    @endif
    @if ($errors->has('details'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('details') }}</p>
    @else
        <p id="details_error" class="text-sm text-red-600 mt-1 hidden"></p>
    @endif
  </div>
  {{-- Section Pembayaran --}}
  <div class="mt-6 pt-5 border-t border-gray-300">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pembayaran</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Sub Total -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Sub Total</label>
            <input type="text" id="subTotalDisplay" readonly
                   class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed"
                   value="Rp 0">
        </div>
        <!-- Diskon (%) -->
       <div>
          <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
          <input type="number" name="diskon" id="diskon"
                value="{{ old('diskon', rtrim(rtrim(number_format($pembelian->diskon ?? 0, 2), '0'), '.')) }}"
                class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
          <p id="diskon_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
        <!-- PPN (%) -->
        <div>
          <label for="ppn" class="block text-sm font-medium text-gray-700">PPN (%)</label>
          <input type="number" name="ppn" id="ppn"
                value="{{ old('ppn', rtrim(rtrim(number_format($pembelian->ppn ?? 0, 2), '0'), '.')) }}"
                class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
          <p id="ppn_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
        <!-- Biaya Pengiriman -->
        <div>
          <label for="biaya_pengiriman" class="block text-sm font-medium text-gray-700">Biaya Pengiriman</label>
          <input type="text" 
              name="biaya_pengiriman" 
              id="biaya_pengiriman"
              value="{{ old('biaya_pengiriman', $pembelian->biaya_pengiriman ?? 0) }}"
              class="w-full rounded-md border px-3 py-2 text-sm border-gray-300 harga-beli-input"
              placeholder="0">
          <p id="biaya_pengiriman_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
        <!-- Total Bayar -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Total Bayar</label>
            <input type="text" id="totalBayarDisplay" readonly
                   class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-blue-700 cursor-not-allowed"
                   value="Rp 0">
        </div>
        <!-- Jenis Pembayaran -->
        <div>
            <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span
                    class="text-rose-600">*</span></label>
            <select name="jenis_pembayaran" id="jenis_pembayaran"
                    class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
                <option value="">-- Pilih Jenis --</option>
                <option value="Cash"
                        {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'Cash' ? 'selected' : '' }}>
                    Tunai
                </option>
                <option value="Kredit"
                        {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'Kredit' ? 'selected' : '' }}>
                    Kredit
                </option>
            </select>
            <p id="jenis_pembayaran_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
    </div>
  </div>
  {{-- Catatan --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Catatan</label>
    <textarea id="catatan" name="catatan" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500"
              rows="4">{{ old('catatan', $pembelian->catatan ?? '') }}</textarea>
    <p class="text-xs text-gray-500">Masukkan catatan atau keterangan (opsional).</p>
  </div>
  {{-- Tombol Simpan / Update & Batal --}}
  <div class="flex items-center gap-3">
    <button type="submit" id="submitButton"
        class="inline-flex items-center px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-medium text-sm rounded-lg transition-all
              {{ isset($pembelian) ? 'opacity-50 cursor-not-allowed' : '' }}"
        {{ isset($pembelian) ? 'disabled' : '' }}>
        {{ isset($pembelian) ? 'Update' : 'Simpan' }}
    </button>

    <a href="{{ route('admin.pembelian.index') }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">
        Batal
    </a>
</div>
</form>
{{-- Form Tambah Barang Baru --}}
<div class="border-t pt-6 mt-10">
  <div class="flex items-center mb-4">
    <input id="tambah_barang" type="checkbox" name="tambah_barang" class="rounded border-gray-300">
    <label for="tambah_barang" class="ml-2 text-sm text-gray-700">Tambah Barang Baru</label>
  </div>
  <form id="formTambahBarang" class="hidden space-y-4 border rounded-lg p-6 bg-gray-50">
    @csrf
    <h3 class="text-lg font-semibold text-gray-800 mb-2">Form Tambah Barang</h3>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">ID Barang</label>
        <input type="text" name="id_barang" value="{{ $nextIdBarang ?? '' }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-rose-600">*</span></label>
        <input type="text" name="nama_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="nama_barang"></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Kategori Barang <span class="text-rose-600">*</span></label>
        <select name="id_kategori_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Kategori --</option>
          @foreach($kategoriBarang as $k)
            <option value="{{ $k->id_kategori_barang }}">{{ $k->nama_kategori }}</option>
          @endforeach
        </select>
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_kategori_barang"></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier <span class="text-rose-600">*</span></label>
        <select name="id_supplier_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Supplier --</option>
          @foreach($suppliers as $s)
            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
          @endforeach
        </select>
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_supplier_barang"></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Satuan <span class="text-rose-600">*</span></label>
        <select name="id_satuan" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Satuan --</option>
          @foreach($satuan as $s)
            <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
          @endforeach
        </select>
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_satuan"></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Merk</label>
        <input type="text" name="merk_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Berat</label>
        <input type="number" step="0.01" name="berat" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
      </div>
      <div>
        <label for="margin" class="block text-sm font-medium text-gray-700">Margin (%)</label>
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
          <p class="text-xs text-gray-500">Margin keuntungan dalam persen</p>
        @endif
      </div>
    </div>
    <button type="button" id="simpanBarangBtn" class="mt-4 px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Simpan Barang</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const pembelianForm   = document.getElementById('pembelianForm');
  const submitButton    = document.getElementById('submitButton');
  const barangContainer = document.getElementById('barangContainer');
  const tambahBarangCheckbox = document.getElementById('tambah_barang');
  const formTambahBarang = document.getElementById('formTambahBarang');
  const simpanBarangBtn = document.getElementById('simpanBarangBtn');

  const isEditMode = {{ isset($pembelian) ? 'true' : 'false' }};
  let barangIndex = {{ $isEdit ?? false ? $pembelian->detailPembelian->count() : 1 }};
  let initialState = null;

  function formatRupiah(angka) {
    if (angka === '' || angka == null || isNaN(angka)) return '';
    let number = parseFloat(angka);
    if (isNaN(number)) return '';
    if (number % 1 === 0) {
      return number.toLocaleString('id-ID');
    } else {
      return number.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
  }

  function unformatRupiah(value) {
    if (!value) return '';
    return value.replace(/\./g, '').replace(/[^0-9]/g, '');
  }

  function formatAllHargaBeli() {
    document.querySelectorAll('.harga-beli-input').forEach(input => {
      if (input.value && input.value.trim() !== '' && input.value !== '0') {
        let clean = unformatRupiah(input.value);
        if (!isNaN(clean)) input.value = formatRupiah(clean);
      }
    });
  }

  function calculateTotals() {
    let subTotal = 0;
    document.querySelectorAll('.barang-row').forEach(row => {
      const harga = unformatRupiah(row.querySelector('input[name*="[harga_beli]"]')?.value) || 0;
      const qty   = parseInt(row.querySelector('input[name*="[kuantitas]"]')?.value) || 0;
      subTotal += parseFloat(harga) * qty;
    });

    const diskon = parseFloat(document.getElementById('diskon')?.value || 0);
    const ppn    = parseFloat(document.getElementById('ppn')?.value || 0);
    const biaya  = parseFloat(unformatRupiah(document.getElementById('biaya_pengiriman')?.value) || 0);

    const diskonNilai   = (diskon / 100) * subTotal;
    setelahDiskon  = subTotal - diskonNilai;
    ppnNilai       = (ppn / 100) * setelahDiskon;
    totalBayar     = setelahDiskon + ppnNilai + biaya;

    document.getElementById('subTotalDisplay').value   = 'Rp ' + formatRupiah(subTotal);
    document.getElementById('totalBayarDisplay').value = 'Rp ' + formatRupiah(totalBayar);
  }

  function saveInitialState() {
    if (!isEditMode) return;

    initialState = {
      supplier: document.getElementById('id_supplier')?.value || '',
      diskon: document.getElementById('diskon')?.value || '0',
      ppn: document.getElementById('ppn')?.value || '0',
      biaya_pengiriman: document.getElementById('biaya_pengiriman')?.value || '0',
      jenis_pembayaran: document.getElementById('jenis_pembayaran')?.value || '',
      catatan: document.getElementById('catatan')?.value || '',
      details: []
    };

    document.querySelectorAll('.barang-row').forEach(row => {
      initialState.details.push({
        id_barang: row.querySelector('select[name$="[id_barang]"]')?.value || '',
        harga_beli: row.querySelector('input[name*="[harga_beli]"]')?.value || '',
        kuantitas: row.querySelector('input[name*="[kuantitas]"]')?.value || ''
      });
    });

    submitButton.disabled = true;
    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
  }

  function hasChanges() {
    if (!isEditMode || !initialState) return true;

    const current = {
      supplier: document.getElementById('id_supplier')?.value || '',
      diskon: document.getElementById('diskon')?.value || '0',
      ppn: document.getElementById('ppn')?.value || '0',
      biaya_pengiriman: document.getElementById('biaya_pengiriman')?.value || '0',
      jenis_pembayaran: document.getElementById('jenis_pembayaran')?.value || '',
      catatan: document.getElementById('catatan')?.value || '',
      details: []
    };

    document.querySelectorAll('.barang-row').forEach(row => {
      current.details.push({
        id_barang: row.querySelector('select[name$="[id_barang]"]')?.value || '',
        harga_beli: row.querySelector('input[name*="[harga_beli]"]')?.value || '',
        kuantitas: row.querySelector('input[name*="[kuantitas]"]')?.value || ''
      });
    });

    if (current.supplier !== initialState.supplier) return true;
    if (current.diskon !== initialState.diskon) return true;
    if (current.ppn !== initialState.ppn) return true;
    if (current.biaya_pengiriman !== initialState.biaya_pengiriman) return true;
    if (current.jenis_pembayaran !== initialState.jenis_pembayaran) return true;
    if (current.catatan !== initialState.catatan) return true;

    if (current.details.length !== initialState.details.length) return true;
    for (let i = 0; i < current.details.length; i++) {
      if (current.details[i].id_barang  !== initialState.details[i].id_barang)  return true;
      if (current.details[i].harga_beli !== initialState.details[i].harga_beli) return true;
      if (current.details[i].kuantitas  !== initialState.details[i].kuantitas)  return true;
    }

    return false;
  }

  function updateButtonState() {
    if (!isEditMode) return;
    const changed = hasChanges();
    submitButton.disabled = !changed;
    if (changed) {
      submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
      submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
  }

  function updateActionButtons() {
    const rows = barangContainer.querySelectorAll('.barang-row');
    rows.forEach((row, i) => {
      const cell = row.querySelector('.col-span-2');
      cell.innerHTML = '';
      const del = document.createElement('button');
      del.type = 'button';
      del.className = 'remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold';
      del.textContent = '-';
      cell.appendChild(del);

      if (i === rows.length - 1 && rows.length < 10) {
        const add = document.createElement('button');
        add.type = 'button';
        add.className = 'add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold ml-2';
        add.textContent = '+';
        cell.appendChild(add);
      }
    });
  }

  function addNewRow() {
    if (barangContainer.querySelectorAll('.barang-row').length >= 10) {
      alert('Maksimum 10 barang.');
      return;
    }
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 mb-2 barang-row items-center';
    const options = @json($barangs).map(b => `<option value="${b.id_barang}">${b.nama_barang}</option>`).join('');
    row.innerHTML = `
      <div class="col-span-4">
        <select name="details[${barangIndex}][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Barang --</option>${options}
        </select>
        <p class="text-sm text-red-600 mt-1 hidden" data-error="id_barang_${barangIndex}"></p>
      </div>
      <div class="col-span-3">
        <input type="text" name="details[${barangIndex}][harga_beli]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 harga-beli-input" placeholder="Harga">
        <p class="text-sm text-red-600 mt-1 hidden" data-error="harga_beli_${barangIndex}"></p>
      </div>
      <div class="col-span-3">
        <input type="number" name="details[${barangIndex}][kuantitas]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
        <p class="text-sm text-red-600 mt-1 hidden" data-error="kuantitas_${barangIndex}"></p>
      </div>
      <div class="col-span-2"></div>
    `;
    barangContainer.appendChild(row);
    barangIndex++;
    updateActionButtons();
    calculateTotals();
    if (isEditMode) updateButtonState();
  }

  function removeRow(row) {
    if (barangContainer.querySelectorAll('.barang-row').length <= 1) {
      alert('Minimal 1 barang.');
      return;
    }
    row.remove();
    updateActionButtons();
    calculateTotals();
    if (isEditMode) updateButtonState();
  }

  document.addEventListener('click', e => {
    if (e.target.classList.contains('add-barang-btn')) addNewRow();
    if (e.target.classList.contains('remove-barang-btn')) removeRow(e.target.closest('.barang-row'));
  });

  if (tambahBarangCheckbox && formTambahBarang) {
    tambahBarangCheckbox.addEventListener('change', function () {
      formTambahBarang.classList.toggle('hidden', !this.checked);
      if (!this.checked) {
        formTambahBarang.reset();
        formTambahBarang.querySelectorAll('[data-error]').forEach(el => {
          el.textContent = ''; el.classList.add('hidden');
        });
      }
    });
  }

  if (simpanBarangBtn) {
    simpanBarangBtn.addEventListener('click', function (e) {
      e.preventDefault();
      let hasError = false;
      const required = ['nama_barang','id_kategori_barang','id_supplier_barang','id_satuan'];
      required.forEach(name => {
        const el = formTambahBarang.querySelector(`[name="${name}"]`);
        const err = formTambahBarang.querySelector(`[data-error="${name}"]`);
        if (!el?.value.trim()) {
          err.textContent = 'Wajib diisi.';
          err.classList.remove('hidden');
          el.classList.add('border-red-500','bg-red-50');
          hasError = true;
        } else {
          err.classList.add('hidden');
          el.classList.remove('border-red-500','bg-red-50');
        }
      });
      if (hasError) return;
      if (!confirm('Simpan barang baru?')) return;

      fetch("{{ route('admin.pembelian.storeBarang') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
        body: new FormData(formTambahBarang)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Gagal.');
      })
      .catch(() => alert('Error jaringan.'));
    });
  }

  document.addEventListener('input', function (e) {
    if (e.target.classList.contains('harga-beli-input')) {
      let val = e.target.value.replace(/\D/g, '');
      e.target.value = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
    }
  });

  document.addEventListener('blur', function (e) {
    if (e.target.classList.contains('harga-beli-input')) {
      let clean = unformatRupiah(e.target.value);
      if (clean) e.target.value = formatRupiah(clean);
    }
  }, true);

  pembelianForm.addEventListener('submit', function (e) {
    e.preventDefault();
    let hasError = false;

    document.querySelectorAll('.border-red-500, .bg-red-50, [id$="_error"]:not(.hidden), [data-error]:not(.hidden)').forEach(el => {
      el.classList.remove('border-red-500', 'bg-red-50');
      if (el.tagName === 'P') el.classList.add('hidden');
    });

    // Validasi supplier & jenis pembayaran
    if (!document.getElementById('id_supplier')?.value) {
      document.getElementById('id_supplier').classList.add('border-red-500','bg-red-50'); hasError = true;
    }
    if (!document.getElementById('jenis_pembayaran')?.value) {
      document.getElementById('jenis_pembayaran_error').classList.remove('hidden');
      document.getElementById('jenis_pembayaran').classList.add('border-red-500','bg-red-50');
      hasError = true;
    }

    // Validasi detail barang
    document.querySelectorAll('.barang-row').forEach(row => {
      const select = row.querySelector('select[name$="[id_barang]"]');
      const harga  = row.querySelector('input[name*="[harga_beli]"]');
      const qty    = row.querySelector('input[name*="[kuantitas]"]');
      if (!select?.value) { select.classList.add('border-red-500','bg-red-50'); hasError = true; }
      if (!harga?.value || parseFloat(unformatRupiah(harga.value)) <= 0) { harga.classList.add('border-red-500','bg-red-50'); hasError = true; }
      if (!qty?.value || parseInt(qty.value) <= 0) { qty.classList.add('border-red-500','bg-red-50'); hasError = true; }
    });

    if (hasError) {
      alert('Periksa kembali data yang diinput!');
      return;
    }

    if (!confirm('Yakin simpan data pembelian?')) return;

    document.querySelectorAll('.harga-beli-input').forEach(input => {
      let clean = unformatRupiah(input.value);
      input.value = clean && !isNaN(clean) ? parseFloat(clean) : 0;
    });

    this.submit();
  });

  formatAllHargaBeli();
  calculateTotals();
  saveInitialState();
  const triggers = '#diskon, #ppn, #biaya_pengiriman, #id_supplier, #jenis_pembayaran, #catatan, .barang-row input, .barang-row select';
  document.querySelectorAll(triggers).forEach(el => {
    el.addEventListener('input', () => { calculateTotals(); if(isEditMode) updateButtonState(); });
    el.addEventListener('change', () => { calculateTotals(); if(isEditMode) updateButtonState(); });
  });

  new MutationObserver(() => {
    calculateTotals();
    if (isEditMode) updateButtonState();
  }).observe(barangContainer, { childList: true, subtree: true });

  updateActionButtons();
});
</script>