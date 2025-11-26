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
                    <input type="number" name="details[{{ $barangIndex }}][harga_beli]"
                           value="{{ old('details.' . $barangIndex . '.harga_beli', $detail->harga_beli) }}"
                           class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" step="0.01" min="0"
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
                <input type="number" name="details[0][harga_beli]"
                       class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" step="0.01" min="0"
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
        <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (%)</label>  <!-- Diubah: Hapus * karena tidak wajib -->
        <input type="number" name="diskon" id="diskon"
               value="{{ old('diskon', $pembelian->diskon ?? 0) }}"
               class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
        <p id="diskon_error" class="text-sm text-red-600 mt-1 hidden"></p>
    </div>
        <!-- PPN (%) -->
        <div>
          <label for="ppn" class="block text-sm font-medium text-gray-700">PPN (%)</label>  <!-- Diubah: Hapus * karena tidak wajib -->
          <input type="number" name="ppn" id="ppn"
                value="{{ old('ppn', $pembelian->ppn ?? 0) }}"
                class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
          <p id="ppn_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
        <!-- Biaya Pengiriman -->
        <div>
            <label for="biaya_pengiriman" class="block text-sm font-medium text-gray-700">Biaya Pengiriman</label>  <!-- Diubah: Hapus * karena tidak wajib -->
            <input type="number" name="biaya_pengiriman" id="biaya_pengiriman"
                  value="{{ old('biaya_pengiriman', $pembelian->biaya_pengiriman ?? 0) }}"
                  class="w-full rounded-md border px-3 py-2 text-sm border-gray-300" step="0.01" min="0"
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
    <button type="submit" id="submitButton" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 {{ isset($pembelian) ? 'opacity-50' : '' }}" {{ isset($pembelian) ? 'disabled' : '' }}>
      {{ isset($pembelian) ? 'Update' : 'Simpan' }}
    </button>
    <a href="{{ route('admin.pembelian.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">Batal</a>
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
  const tambahBarangCheckbox = document.getElementById('tambah_barang');
  const formTambahBarang = document.getElementById('formTambahBarang');
  const simpanBarangBtn = document.getElementById('simpanBarangBtn');
  const barangContainer = document.getElementById('barangContainer');
  const pembelianForm = document.getElementById('pembelianForm');
  let barangIndex = {{ $isEdit ?? false ? $pembelian->detailPembelian->count() : 1 }};

  function resetTambahBarangErrors() {
    ['nama_barang', 'id_kategori_barang', 'id_supplier_barang', 'id_satuan', 'berat', 'margin'].forEach(id => {
      const el = formTambahBarang?.querySelector(`[name="${id}"]`);
      const errorEl = formTambahBarang?.querySelector(`[data-error="${id}"]`);
      if (errorEl) {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
      }
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });
  }

  function showError(id, message) {
    const errorEl = document.querySelector(`#${id}_error`);
    const inputEl = document.getElementById(id);
    if (errorEl) {
      errorEl.textContent = message;
      errorEl.classList.remove('hidden');
    }
    if (inputEl) inputEl.classList.add('border-red-500', 'bg-red-50');
  }

  function showRowError(row, errorKey, message) {
    const errorEl = row.querySelector(`[data-error="${errorKey}"]`);
    if (errorEl) {
      if (errorKey.startsWith('id_barang_')) {
        // Diubah: Untuk id_barang, hanya warnai merah tanpa tampilkan pesan
        const input = row.querySelector(`select[name*="[id_barang]"]`);
        if (input) {
          input.classList.add('border-red-500', 'bg-red-50');
        }
      } else {
        // Untuk harga_beli dan kuantitas, tetap tampilkan pesan
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
        const input = row.querySelector(`[name*="[${errorKey.split('_')[1] || errorKey.split('_')[0]}]"]`);
        if (input) input.classList.add('border-red-500', 'bg-red-50');
      }
    }
  }

  function resetAllErrors() {
    document.querySelectorAll('[data-error], [id$="_error"]').forEach(el => {
      el.textContent = '';
      el.classList.add('hidden');
    });
    document.querySelectorAll('input, select').forEach(el => {
      el.classList.remove('border-red-500', 'bg-red-50');
    });
  }

  if (tambahBarangCheckbox && formTambahBarang) {
    tambahBarangCheckbox.addEventListener('change', function () {
      formTambahBarang.classList.toggle('hidden', !this.checked);
      if (!this.checked) {
        formTambahBarang.reset();
        resetTambahBarangErrors();
      }
    });
  }

  if (simpanBarangBtn) {
    simpanBarangBtn.addEventListener('click', function (e) {
      e.preventDefault();
      resetTambahBarangErrors();
      let hasError = false;

      const required = [
        { name: 'nama_barang', msg: 'Nama barang wajib diisi.' },
        { name: 'id_kategori_barang', msg: 'Kategori wajib dipilih.' },
        { name: 'id_supplier_barang', msg: 'Supplier wajib dipilih.' },
        { name: 'id_satuan', msg: 'Satuan wajib dipilih.' },
      ];

      required.forEach(f => {
        const el = formTambahBarang.querySelector(`[name="${f.name}"]`);
        const err = formTambahBarang.querySelector(`[data-error="${f.name}"]`);
        if (!el?.value.trim()) {
          err.textContent = f.msg;
          err.classList.remove('hidden');
          el.classList.add('border-red-500', 'bg-red-50');
          hasError = true;
        }
      });

      const berat = parseFloat(formTambahBarang.querySelector('[name="berat"]')?.value);
      if (isNaN(berat) || berat <= 0) {
        const err = formTambahBarang.querySelector('[data-error="berat"]');
        err.textContent = 'Berat harus lebih dari 0.';
        err.classList.remove('hidden');
        formTambahBarang.querySelector('[name="berat"]').classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      const margin = parseFloat(formTambahBarang.querySelector('[name="margin"]')?.value);
      if (margin && (isNaN(margin) || margin < 0 || margin > 100)) {
        const err = formTambahBarang.querySelector('[data-error="margin"]');
        err.textContent = 'Margin harus 0-100%.';
        err.classList.remove('hidden');
        formTambahBarang.querySelector('[name="margin"]').classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      if (hasError) return;

      if (!confirm('Yakin simpan barang baru?')) return;

      fetch("{{ route('admin.pembelian.storeBarang') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: new FormData(formTambahBarang)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          window.location.reload();
        } else {
          alert(data.message || 'Gagal menyimpan barang.');
        }
      })
      .catch(() => alert('Terjadi kesalahan jaringan.'));
    });
  }

  function attachBarangEvents(row) {
    row.querySelector('select')?.addEventListener('change', calculateTotals);
    row.querySelectorAll('input').forEach(input => {
      input.addEventListener('input', calculateTotals);
    });
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
        <input type="number" name="details[${barangIndex}][harga_beli]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" step="0.01" min="0" placeholder="Harga">
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
    attachBarangEvents(row);
    calculateTotals();
  }

  function removeRow(row) {
    if (barangContainer.querySelectorAll('.barang-row').length > 1) {
      row.remove();
      updateActionButtons();
      calculateTotals();
    } else {
      alert('Minimal 1 barang.');
    }
  }

  document.addEventListener('click', e => {
    if (e.target.classList.contains('add-barang-btn')) addNewRow();
    if (e.target.classList.contains('remove-barang-btn')) removeRow(e.target.closest('.barang-row'));
  });

  document.querySelectorAll('.barang-row').forEach(attachBarangEvents);
  updateActionButtons();

  function calculateTotals() {
    let subTotal = 0;
    document.querySelectorAll('.barang-row').forEach(row => {
      const harga = parseFloat(row.querySelector('input[name*="[harga_beli]"]')?.value) || 0;
      const qty = parseInt(row.querySelector('input[name*="[kuantitas]"]')?.value) || 0;
      subTotal += harga * qty;
    });

    const diskon = parseFloat(document.getElementById('diskon')?.value) || 0;
    const ppn = parseFloat(document.getElementById('ppn')?.value) || 0;
    const biaya = parseFloat(document.getElementById('biaya_pengiriman')?.value) || 0;

    const diskonNilai = (diskon / 100) * subTotal;
    const setelahDiskon = subTotal - diskonNilai;
    const ppnNilai = (ppn / 100) * setelahDiskon;
    const totalBayar = setelahDiskon + ppnNilai + biaya;

    document.getElementById('subTotalDisplay').value = 'Rp ' + subTotal.toLocaleString('id-ID');
    document.getElementById('totalBayarDisplay').value = 'Rp ' + totalBayar.toLocaleString('id-ID');
  }

  ['diskon', 'ppn', 'biaya_pengiriman'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', calculateTotals);
    document.getElementById(id)?.addEventListener('change', calculateTotals);
  });


  if (pembelianForm) {
    pembelianForm.addEventListener('submit', function (e) {
      e.preventDefault();
      resetAllErrors();
      let hasError = false;

      // Supplier
      if (!document.getElementById('id_supplier')?.value) {
        document.getElementById('id_supplier').classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      // Jenis Pembayaran
      if (!document.getElementById('jenis_pembayaran')?.value) {
        showError('jenis_pembayaran', 'Jenis pembayaran wajib dipilih.');
        hasError = true;
      }

      // Diskon
      const diskon = parseFloat(document.getElementById('diskon')?.value) || 0;
      if (diskon < 0 || diskon > 100) {
        showError('diskon', 'Diskon harus 0-100%.');
        hasError = true;
      }

      // PPN
      const ppn = parseFloat(document.getElementById('ppn')?.value) || 0;
      if (ppn < 0 || ppn > 100) {
        showError('ppn', 'PPN harus 0-100%.');
        hasError = true;
      }

      // Biaya Pengiriman
      const biaya = parseFloat(document.getElementById('biaya_pengiriman')?.value) || 0;
      if (biaya < 0) {
        showError('biaya_pengiriman', 'Biaya tidak boleh negatif.');
        hasError = true;
      }

      // Detail Barang
      const rows = document.querySelectorAll('.barang-row');
      if (rows.length === 0) {
        document.querySelector('#details_error')?.classList.remove('hidden');
        hasError = true;
      }

      rows.forEach((row, i) => {
      const idBarang = row.querySelector('select[name$="[id_barang]"]');
      const harga = row.querySelector('input[name$="[harga_beli]"]');
      const qty = row.querySelector('input[name$="[kuantitas]"]');

      if (!idBarang?.value) {
        idBarang.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }
      if (!harga?.value || parseFloat(harga.value) <= 0) {
        harga.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }
      if (!qty?.value || parseInt(qty.value) <= 0) {
        qty.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }
    });

      if (hasError) {
        alert('Periksa kembali isian Anda.');
        return;
      }

      if (!confirm('Simpan data pembelian?')) return;

      this.submit();
    });
  }

  calculateTotals();
});
</script>