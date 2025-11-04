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

  {{-- Tanggal Terima --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Tanggal Terima</label>
    <input id="tanggal_terima" name="tanggal_terima" type="date"
           value="{{ old('tanggal_terima', $pembelian->tanggal_terima ?? '') }}"
           readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">Tanggal terima diisi otomatis saat selesai.</p>
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

  {{-- Jenis Pembayaran --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
    <select id="jenis_pembayaran" name="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
      <option value="">-- Pilih Jenis --</option>
      <option value="Cash" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'Cash' ? 'selected' : '' }}>Cash/Tunai</option>
      <option value="Kredit" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
    </select>
    <p id="jenis_pembayaran_error" class="text-xs text-rose-600 mt-1 hidden"></p>
    <p class="text-xs text-gray-500">Pilih jenis pembayaran.</p>
  </div>

  {{-- Jumlah Bayar --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Jumlah Bayar <span class="text-rose-600">*</span></label>
    <input id="jumlah_bayar" name="jumlah_bayar" type="number" step="0.01"
           value="{{ old('jumlah_bayar', $pembelian->jumlah_bayar ?? '') }}"
           class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
    <p id="jumlah_bayar_error" class="text-xs text-rose-600 mt-1 hidden"></p>
    <p class="text-xs text-gray-500">Masukkan jumlah bayar manual.</p>
  </div>

  {{-- Section Detail Barang --}}
  <div id="detail_section" class="space-y-4">
    <h3 class="text-lg font-medium text-gray-800">Detail Barang</h3>
    <div id="detail_container">
      @if(isset($pembelian) && $pembelian->detailPembelian->count() > 0)
        @foreach($pembelian->detailPembelian as $index => $detail)
          <div class="detail_row grid grid-cols-5 gap-3 items-end mb-3">
            <div>
              <label class="block text-sm font-medium text-gray-700">Barang <span class="text-rose-600">*</span></label>
              <select name="details[{{ $index }}][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
                <option value="">-- Pilih Barang --</option>
                @foreach($barangs as $b)
                  <option value="{{ $b->id_barang }}" {{ $detail->id_barang == $b->id_barang ? 'selected' : '' }}>
                    {{ $b->nama_barang }}
                  </option>
                @endforeach
              </select>
              <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_barang_{{ $index }}"></p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Harga Beli <span class="text-rose-600">*</span></label>
              <input name="details[{{ $index }}][harga_beli]" type="number" step="0.01" min="0" value="{{ $detail->harga_beli }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
              <p class="text-xs text-rose-600 mt-1 hidden" data-error="harga_beli_{{ $index }}"></p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Kuantitas <span class="text-rose-600">*</span></label>
              <input name="details[{{ $index }}][kuantitas]" type="number" min="1" value="{{ $detail->kuantitas }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
              <p class="text-xs text-rose-600 mt-1 hidden" data-error="kuantitas_{{ $index }}"></p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Sub Total</label>
              <input type="number" step="0.01" value="{{ $detail->sub_total }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
            </div>
            <div class="flex items-center gap-2">
              <button type="button" class="add_detail inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Tambah</button>
              @if($index > 0)
                <button type="button" class="remove_detail inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">Hapus</button>
              @else
                <button type="button" class="remove_detail hidden">Hapus</button>
              @endif
            </div>
          </div>
        @endforeach
      @else
        <div class="detail_row grid grid-cols-5 gap-3 items-end mb-3">
          <div>
            <label class="block text-sm font-medium text-gray-700">Barang <span class="text-rose-600">*</span></label>
            <select name="details[0][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
              <option value="">-- Pilih Barang --</option>
              @foreach($barangs as $b)
                <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
              @endforeach
            </select>
            <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_barang_0"></p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Harga Beli <span class="text-rose-600">*</span></label>
            <input name="details[0][harga_beli]" type="number" step="0.01" min="0" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
            <p class="text-xs text-rose-600 mt-1 hidden" data-error="harga_beli_0"></p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Kuantitas <span class="text-rose-600">*</span></label>
            <input name="details[0][kuantitas]" type="number" min="1" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
            <p class="text-xs text-rose-600 mt-1 hidden" data-error="kuantitas_0"></p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Sub Total</label>
            <input type="number" step="0.01" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="add_detail inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Tambah</button>
            <button type="button" class="remove_detail hidden">Hapus</button>
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- Tombol Simpan / Update & Batal --}}
  <div class="flex items-center gap-3">
    <button type="submit" id="submitButton" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 {{ isset($pembelian) ? 'opacity-50' : '' }}" {{ isset($pembelian) ? 'disabled' : '' }}>
      {{ isset($pembelian) ? 'Update' : 'Simpan Pembelian' }}
    </button>
    <a href="{{ route('admin.pembelian.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-50">Batal</a>
  </div>
</form>

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
        <label class="block text-sm font-medium text-gray-700">Berat (kg)</label>
        <input type="number" step="0.01" name="berat" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
      </div>
    </div>

    {{-- Margin --}}
    <div class="grid grid-cols-1 gap-1">
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
        <p class="text-xs text-gray-500">Margin keuntungan dalam persen (0-100)</p>
      @endif
    </div>

    <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Simpan Barang</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tambahBarangCheckbox = document.getElementById('tambah_barang');
  const formTambahBarang = document.getElementById('formTambahBarang');
  const detailContainer = document.getElementById('detail_container');
  const pembelianForm = document.getElementById('pembelianForm');

  if (tambahBarangCheckbox && formTambahBarang) {
    tambahBarangCheckbox.addEventListener('change', function () {
      formTambahBarang.classList.toggle('hidden', !this.checked);
    });
  }

  // Fungsi untuk reset error di form tambah barang
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

      // Validasi Nama Barang
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

      // Validasi Kategori Barang
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

      // Validasi Supplier Barang
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

      // Validasi Satuan
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

      // Jika valid, submit via fetch
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
          alert(data.message); // Tetap alert untuk success, atau ubah jika perlu
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
    if (e.target.classList.contains('add_detail')) {
      const rows = detailContainer.querySelectorAll('.detail_row');
      const idx = rows.length;
      const clone = rows[rows.length - 1].cloneNode(true);

      clone.querySelectorAll('input, select').forEach(el => {
        el.value = '';
        // Perbaiki regex dari /\$\d+\$/ ke /\$\d+\$/ untuk mengganti [angka] dengan benar
        el.name = el.name.replace(/\$\d+\$/, '[' + idx + ']');
      });

      // Reset error elements in clone
      clone.querySelectorAll('[data-error]').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
      });

      const removeBtn = clone.querySelector('.remove_detail');
      removeBtn.classList.remove('hidden');
      removeBtn.className = 'remove_detail inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700';

      detailContainer.appendChild(clone);
    }

    if (e.target.classList.contains('remove_detail')) {
      const row = e.target.closest('.detail_row');
      if (detailContainer.querySelectorAll('.detail_row').length > 1) {
        row.remove();
      }
    }
  });

  // Reset errors sebelum validasi
  function resetErrors() {
    // Reset field utama
    ['id_supplier', 'jenis_pembayaran', 'jumlah_bayar'].forEach(id => {
      const errorEl = document.querySelector('#' + id + '_error');
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
      resetErrors(); // Reset error sebelum validasi

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

      // Validasi Jumlah Bayar
      const jumlahInput = document.getElementById('jumlah_bayar');
      const jumlah = parseFloat(jumlahInput.value);
      if (!jumlahInput.value || isNaN(jumlah) || jumlah <= 0) {
        const errorEl = document.querySelector('#jumlah_bayar_error');
        if (errorEl) {
          errorEl.textContent = 'Jumlah bayar harus lebih dari 0.';
          errorEl.classList.remove('hidden');
        }
        jumlahInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }

      // Validasi Detail Barang
      const detailRows = document.querySelectorAll('.detail_row');
      detailRows.forEach((row, index) => {
        const idBarang = row.querySelector('select[name$="[id_barang]"]');
        const hargaBeli = row.querySelector('input[name$="[harga_beli]"]');
        const kuantitas = row.querySelector('input[name$="[kuantitas]"]');

        // Validasi Barang
        if (!idBarang.value) {
          const errorEl = row.querySelector('[data-error="id_barang_' + index + '"]');
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
          const errorEl = row.querySelector('[data-error="harga_beli_' + index + '"]');
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
          const errorEl = row.querySelector('[data-error="kuantitas_' + index + '"]');
          if (errorEl) {
            errorEl.textContent = 'Kuantitas harus lebih dari 0.';
            errorEl.classList.remove('hidden');
          }
          kuantitas.classList.add('border-red-500', 'bg-red-50');
          hasError = true;
        }
      });

      // Jika ada error, hentikan submit
      if (hasError) return;

      // Jika valid, baru confirm
      if (confirm('Simpan data pembelian?')) {
        this.submit(); 
      }
    });
  }
  document.addEventListener('input', function(e) {
    if (e.target.name && (e.target.name.includes('[harga_beli]') || e.target.name.includes('[kuantitas]'))) {
      const row = e.target.closest('.detail_row');
      const hargaInput = row.querySelector('input[name*="[harga_beli]"]');
      const qtyInput = row.querySelector('input[name*="[kuantitas]"]');
      const subTotalInput = row.querySelector('input[readonly]'); 
      const harga = parseFloat(hargaInput.value) || 0;
      const qty = parseInt(qtyInput.value) || 0;
      const subTotal = harga * qty;
      subTotalInput.value = subTotal.toFixed(2);
    }
  });
});
</script>