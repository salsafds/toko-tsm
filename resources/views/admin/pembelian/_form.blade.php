<form action="{{ isset($pembelian) ? route('admin.pembelian.update', $pembelian->id_pembelian) : route('admin.pembelian.store') }}" 
      method="POST" class="space-y-6" id="pembelianForm">
  @csrf
  @if(isset($pembelian))
    @method('PUT')
  @endif

  {{-- ID Pembelian --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">ID Pembelian</label>
    <input type="text" name="id_pembelian" 
           value="{{ old('id_pembelian', isset($pembelian) ? $pembelian->id_pembelian : ($nextId ?? '')) }}" 
           readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.</p>
  </div>

  {{-- Tanggal Pembelian --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Tanggal Pembelian <span class="text-rose-600">*</span></label>
    <input id="tanggal_pembelian" name="tanggal_pembelian" type="date"
           value="{{ now()->format('Y-m-d') }}" readonly
           class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">Tanggal diisi otomatis dengan waktu sekarang.</p>
  </div>

  {{-- Supplier --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Supplier <span class="text-rose-600">*</span></label>
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
                <div class="col-span-2 flex justify-center">
                    @if($loop->last)
                        <button type="button"
                                class="add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">
                            +
                        </button>
                    @else
                        <button type="button"
                                class="remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">
                            -
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
            <div class="col-span-2 flex justify-center">
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
            <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (%) <span
                    class="text-rose-600">*</span></label>
            <input type="number" name="diskon" id="diskon"
                   value="{{ old('diskon', $pembelian->diskon ?? 0) }}"
                   class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
            <p id="diskon_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
        <!-- PPN (%) -->
        <div>
            <label for="ppn" class="block text-sm font-medium text-gray-700">PPN (%) <span
                    class="text-rose-600">*</span></label>
            <input type="number" name="ppn" id="ppn"
                   value="{{ old('ppn', $pembelian->ppn ?? 0) }}"
                   class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
            <p id="ppn_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
        <!-- Biaya Pengiriman -->
        <div>
            <label for="biaya_pengiriman" class="block text-sm font-medium text-gray-700">Biaya Pengiriman <span
                    class="text-rose-600">*</span></label>
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
      {{ isset($pembelian) ? 'Update' : 'Simpan Pembelian' }}
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
    <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Simpan Barang</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tambahBarangCheckbox = document.getElementById('tambah_barang');
  const formTambahBarang = document.getElementById('formTambahBarang');
  const barangContainer = document.getElementById('barangContainer');
  const pembelianForm = document.getElementById('pembelianForm');

  function calculateTotals() {
    const barangRows = document.querySelectorAll('.barang-row');
    let totalSubTotal = 0;

    barangRows.forEach(row => {
      const hargaInput = row.querySelector('input[name*="[harga_beli]"]');
      const qtyInput = row.querySelector('input[name*="[kuantitas]"]');
      const harga = parseFloat(hargaInput.value) || 0;
      const qty = parseInt(qtyInput.value) || 0;
      const subTotal = harga * qty;
      totalSubTotal += subTotal;
    });

    const diskon = parseFloat(document.getElementById('diskon').value) || 0;
    const ppn = parseFloat(document.getElementById('ppn').value) || 0;
    const biayaPengiriman = parseFloat(document.getElementById('biaya_pengiriman').value) || 0;

    const nilaiDiskon = (diskon / 100) * totalSubTotal;
    const setelahDiskon = totalSubTotal - nilaiDiskon;
    const nilaiPpn = (ppn / 100) * setelahDiskon;
    const totalSetelahPpn = setelahDiskon + nilaiPpn;
    const totalBayar = totalSetelahPpn + biayaPengiriman;

    document.getElementById('subTotalDisplay').value = 'Rp ' + totalSubTotal.toLocaleString('id-ID');
    document.getElementById('totalBayarDisplay').value = 'Rp ' + totalBayar.toLocaleString('id-ID');
  }

  if (tambahBarangCheckbox && formTambahBarang) {
    tambahBarangCheckbox.addEventListener('change', function () {
      formTambahBarang.classList.toggle('hidden', !this.checked);
    });
  }

  function resetTambahBarangErrors() {
    ['nama_barang', 'id_kategori_barang', 'id_supplier_barang', 'id_satuan'].forEach(id => {
      const errorEl = document.querySelector(`[data-error="${id}"]`);
      if (errorEl) {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
      }
      const el = document.querySelector(`[name="${id}"]`);
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });
  }

  if (formTambahBarang) {
    formTambahBarang.addEventListener('submit', function (e) {
      e.preventDefault();
      resetTambahBarangErrors();

      let hasError = false;

      const namaBarang = this.querySelector('[name="nama_barang"]');
      if (!namaBarang.value.trim()) {
        const errorEl = this.querySelector('[data-error="nama_barang"]');
        if (errorEl) {
          errorEl.textContent = 'Nama barang wajib diisi.';
          errorEl.classList.remove('hidden');
        }
        namaBarang.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      const kategoriBarang = this.querySelector('[name="id_kategori_barang"]');
      if (!kategoriBarang.value) {
        const errorEl = this.querySelector('[data-error="id_kategori_barang"]');
        if (errorEl) {
          errorEl.textContent = 'Kategori barang wajib dipilih.';
          errorEl.classList.remove('hidden');
        }
        kategoriBarang.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      const supplierBarang = this.querySelector('[name="id_supplier_barang"]');
      if (!supplierBarang.value) {
        const errorEl = this.querySelector('[data-error="id_supplier_barang"]');
        if (errorEl) {
          errorEl.textContent = 'Supplier wajib dipilih.';
          errorEl.classList.remove('hidden');
        }
        supplierBarang.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      const satuan = this.querySelector('[name="id_satuan"]');
      if (!satuan.value) {
        const errorEl = this.querySelector('[data-error="id_satuan"]');
        if (errorEl) {
          errorEl.textContent = 'Satuan wajib dipilih.';
          errorEl.classList.remove('hidden');
        }
        satuan.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      if (hasError) return;

      fetch("{{ route('admin.pembelian.storeBarang') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: new FormData(this)
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          this.reset();
          formTambahBarang.classList.add('hidden');
          tambahBarangCheckbox.checked = false;
          document.querySelectorAll('select[name^="details["][name$="][id_barang]"]').forEach(sel => {
            const opt = new Option(data.barang.nama_barang, data.barang.id_barang, false, false);
            sel.add(opt);
          });
        } else {
          alert(data.message || 'Gagal menambah barang.');
        }
      })
      .catch(() => alert('Terjadi kesalahan jaringan.'));
    });
  }

  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-barang-btn')) {
      const rows = barangContainer.querySelectorAll('.barang-row');
      const idx = rows.length;
      const clone = rows[rows.length - 1].cloneNode(true);

      clone.querySelectorAll('input, select').forEach(el => {
        el.value = '';
        el.name = el.name.replace(/\d+/, idx);
      });

      clone.querySelectorAll('[data-error]').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
      });

      const addBtn = clone.querySelector('.add-barang-btn');
      addBtn.className = 'remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm';
      addBtn.textContent = '-';

      barangContainer.appendChild(clone);
      calculateTotals();
    }

    if (e.target.classList.contains('remove-barang-btn')) {
      const row = e.target.closest('.barang-row');
      if (barangContainer.querySelectorAll('.barang-row').length > 1) {
        row.remove();
        calculateTotals();
      }
    }
  });

  // Reset errors sebelum validasi
  function resetErrors() {
    ['id_supplier', 'diskon', 'ppn', 'biaya_pengiriman', 'jenis_pembayaran'].forEach(id => {
      const errorEl = document.querySelector(`#${id}_error`);
      if (errorEl) {
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
      }
      const el = document.getElementById(id);
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });
    // Reset error detail
    document.querySelectorAll('[data-error]').forEach(el => {
      el.textContent = '';
      el.classList.add('hidden');
    });
  }

  if (pembelianForm) {
    pembelianForm.addEventListener('submit', function (e) {
      e.preventDefault();
      resetErrors();

      let hasError = false;

      // Validasi Supplier
      const supplierSelect = document.getElementById('id_supplier');
      if (!supplierSelect.value) {
        const errorEl = document.querySelector('#id_supplier_error');
        if (errorEl) {
          errorEl.textContent = 'Supplier wajib dipilih.';
          errorEl.classList.remove('hidden');
        }
        supplierSelect.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      // Validasi Jenis Pembayaran
      const jenisSelect = document.getElementById('jenis_pembayaran');
      if (!jenisSelect.value) {
        const errorEl = document.querySelector('#jenis_pembayaran_error');
        if (errorEl) {
          errorEl.textContent = 'Jenis pembayaran wajib dipilih.';
          errorEl.classList.remove('hidden');
        }
        jenisSelect.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      // Validasi Diskon
      const diskonInput = document.getElementById('diskon');
      const diskon = parseFloat(diskonInput.value);
      if (isNaN(diskon) || diskon < 0 || diskon > 100) {
        const errorEl = document.querySelector('#diskon_error');
        if (errorEl) {
          errorEl.textContent = 'Diskon harus antara 0 hingga 100%.';
          errorEl.classList.remove('hidden');
        }
        diskonInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      // Validasi PPN
      const ppnInput = document.getElementById('ppn');
      const ppn = parseFloat(ppnInput.value);
      if (isNaN(ppn) || ppn < 0 || ppn > 100) {
        const errorEl = document.querySelector('#ppn_error');
        if (errorEl) {
          errorEl.textContent = 'PPN harus antara 0 hingga 100%.';
          errorEl.classList.remove('hidden');
        }
        ppnInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      // Validasi Biaya Pengiriman
      const biayaPengirimanInput = document.getElementById('biaya_pengiriman');
      const biayaPengiriman = parseFloat(biayaPengirimanInput.value);
      if (isNaN(biayaPengiriman) || biayaPengiriman < 0) {
        const errorEl = document.querySelector('#biaya_pengiriman_error');
        if (errorEl) {
          errorEl.textContent = 'Biaya pengiriman tidak boleh kurang dari 0.';
          errorEl.classList.remove('hidden');
        }
        biayaPengirimanInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      // Validasi Detail Barang
      const barangRows = document.querySelectorAll('.barang-row');
      if (barangRows.length === 0) {
        alert('Harus ada setidaknya satu detail barang.');
        hasError = true;
      }

      barangRows.forEach((row, index) => {
        const idBarang = row.querySelector('select[name$="[id_barang]"]');
        const hargaBeli = row.querySelector('input[name$="[harga_beli]"]');
        const kuantitas = row.querySelector('input[name$="[kuantitas]"]');

        // Validasi Barang
        if (!idBarang.value) {
          const errorEl = row.querySelector(`[data-error="id_barang_${index}"]`);
          if (errorEl) {
            errorEl.textContent = 'Barang wajib dipilih.';
            errorEl.classList.remove('hidden');
          }
          idBarang.classList.add('border-red-500', 'bg-red-50');
          hasError = true;
        }

        // Validasi Harga Beli
        const harga = parseFloat(hargaBeli.value);
        if (!hargaBeli.value || isNaN(harga) || harga <= 0) {
          const errorEl = row.querySelector(`[data-error="harga_beli_${index}"]`);
          if (errorEl) {
            errorEl.textContent = 'Harga beli harus lebih dari 0.';
            errorEl.classList.remove('hidden');
          }
          hargaBeli.classList.add('border-red-500', 'bg-red-50');
          hasError = true;
        }

        // Validasi Kuantitas
        const qty = parseInt(kuantitas.value);
        if (!kuantitas.value || isNaN(qty) || qty <= 0) {
          const errorEl = row.querySelector(`[data-error="kuantitas_${index}"]`);
          if (errorEl) {
            errorEl.textContent = 'Kuantitas harus lebih dari 0.';
            errorEl.classList.remove('hidden');
          }
          kuantitas.classList.add('border-red-500', 'bg-red-50');
          hasError = true;
        }
      });

      if (hasError) return;

      if (confirm('Simpan data pembelian?')) {
        this.submit();
      }
    });
  }

  document.addEventListener('input', function (e) {
    if (
      e.target.name.includes('[harga_beli]') ||
      e.target.name.includes('[kuantitas]') ||
      e.target.id === 'diskon' ||
      e.target.id === 'ppn' ||
      e.target.id === 'biaya_pengiriman'
    ) {
      calculateTotals();
    }
  });

  calculateTotals();
});
</script>