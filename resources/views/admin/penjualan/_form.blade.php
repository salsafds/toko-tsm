<form action="{{ $action ?? '#' }}" method="POST" class="space-y-6" id="penjualanForm" novalidate>
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID Penjualan --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">ID Penjualan</label>
    <input type="text" name="id_penjualan" value="{{ old('id_penjualan', isset($penjualan) ? $penjualan->id_penjualan : ($nextId ?? '')) }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
    <p class="text-xs text-gray-500">
      @if(isset($penjualan))
        ID tidak dapat diubah.
      @else
        ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
      @endif
    </p>
  </div>

  {{-- Pelanggan atau Anggota --}}
  <div class="grid grid-cols-2 gap-3">
    <div class="grid grid-cols-1 gap-1">
      <label for="id_pelanggan" class="block text-sm font-medium text-gray-700">Pelanggan</label>
      <select id="id_pelanggan" name="id_pelanggan" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_pelanggan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}">
        <option value="">-- Pilih Pelanggan --</option>
        @foreach($pelanggans as $p)
             <option value="{{ $p->id_pelanggan }}" {{ old('id_pelanggan', $penjualan->id_pelanggan ?? '') == $p->id_pelanggan ? 'selected' : '' }}>{{ $p->nama_pelanggan }}</option>
        @endforeach
      </select>
      @if ($errors->has('id_pelanggan'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_pelanggan') }}</p>
      @else
        <p id="id_pelanggan_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih pelanggan jika pembeli adalah pelanggan.</p>
      @endif
    </div>

    <div class="grid grid-cols-1 gap-1">
      <label for="id_anggota" class="block text-sm font-medium text-gray-700">Anggota</label>
      <select id="id_anggota" name="id_anggota" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_anggota') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}">
        <option value="">-- Pilih Anggota --</option>
        @foreach($anggotas as $a)
          <option value="{{ $a->id_anggota }}" {{ old('id_anggota', $penjualan->id_anggota ?? '') == $a->id_anggota ? 'selected' : '' }}>{{ $a->nama_anggota }}</option>
        @endforeach
      </select>
      @if ($errors->has('id_anggota'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_anggota') }}</p>
      @else
        <p id="id_anggota_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih anggota jika pembeli adalah anggota.</p>
      @endif
    </div>
  </div>

  {{-- Barang --}}
  <div id="barangContainer">
    <label class="block text-sm font-medium text-gray-700 mb-1">Barang <span class="text-rose-600">*</span></label>
    <p class="text-xs text-gray-500 mb-3">Pilih barang dan kuantitas yang akan dijual.</p>

    {{-- Baris default pertama (kosong) --}}
    <div class="grid grid-cols-12 gap-2 mb-2 barang-row items-center">
        <div class="col-span-6">
        <select name="barang[0][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
            <option value="">-- Pilih Barang --</option>
            @foreach($barangs as $b)
            <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
            @endforeach
        </select>
        </div>
        <div class="col-span-4">
        <input type="number" name="barang[0][kuantitas]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
        </div>
        <div class="col-span-2 flex justify-center">
        <button type="button" class="add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">+</button>
        </div>
    </div>

    {{-- Baris dari data edit (jika ada) --}}
    @if(isset($penjualan) && $penjualan->detailPenjualan->count() > 0)
        @foreach($penjualan->detailPenjualan as $index => $detail)
        @if($index > 0) {{-- skip index 0 karena sudah ada default kosong --}}
            <div class="grid grid-cols-12 gap-2 mb-2 barang-row items-center">
            <div class="col-span-6">
                <select name="barang[{{ $index }}][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
                @foreach($barangs as $b)
                    <option value="{{ $b->id_barang }}" {{ $detail->id_barang == $b->id_barang ? 'selected' : '' }}>{{ $b->nama_barang }}</option>
                @endforeach
                </select>
            </div>
            <div class="col-span-4">
                <input type="number" name="barang[{{ $index }}][kuantitas]" value="{{ $detail->kuantitas }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1">
            </div>
            <div class="col-span-2 flex justify-center">
                <button type="button" class="remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">−</button>
            </div>
            </div>
        @endif
        @endforeach
    @endif

    @if ($errors->has('barang'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('barang') }}</p>
    @else
        <p id="barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
    @endif
  </div>

  {{-- Ekspedisi --}}
  <div>
    <label><input type="checkbox" id="ekspedisi" name="ekspedisi" value="1" {{ old('ekspedisi', isset($penjualan->pengiriman) ? 'checked' : '') }}> Ekspedisi</label>
    <div id="ekspedisiForm" style="display: {{ old('ekspedisi') || isset($penjualan->pengiriman) ? 'block' : 'none' }};">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="grid grid-cols-1 gap-1">
          <label for="id_agen_ekspedisi" class="block text-sm font-medium text-gray-700">Agen Ekspedisi <span class="text-rose-600">*</span></label>
          <select name="id_agen_ekspedisi" id="id_agen_ekspedisi" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_agen_ekspedisi') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
            <option value="">-- Pilih Agen Ekspedisi --</option>
            @foreach($agenEkspedisis as $ae)
              <option value="{{ $ae->id_ekspedisi }}" {{ old('id_agen_ekspedisi', $penjualan->pengiriman->id_agen_ekspedisi ?? '') == $ae->id_ekspedisi ? 'selected' : '' }}>{{ $ae->nama_ekspedisi }}</option>
            @endforeach
          </select>
          @if ($errors->has('id_agen_ekspedisi'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_agen_ekspedisi') }}</p>
          @else
            <p id="id_agen_ekspedisi_error" class="text-sm text-red-600 mt-1 hidden"></p>
          @endif
        </div>
        <div class="grid grid-cols-1 gap-1">
          <label for="nama_penerima" class="block text-sm font-medium text-gray-700">Nama Penerima <span class="text-rose-600">*</span></label>
          <input type="text" name="nama_penerima" id="nama_penerima" value="{{ old('nama_penerima', $penjualan->pengiriman->nama_penerima ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_penerima') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
          @if ($errors->has('nama_penerima'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_penerima') }}</p>
          @else
            <p id="nama_penerima_error" class="text-sm text-red-600 mt-1 hidden"></p>
          @endif
        </div>
        <div class="grid grid-cols-1 gap-1">
          <label for="telepon_penerima" class="block text-sm font-medium text-gray-700">Telepon Penerima <span class="text-rose-600">*</span></label>
          <input type="text" name="telepon_penerima" id="telepon_penerima" value="{{ old('telepon_penerima', $penjualan->pengiriman->telepon_penerima ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('telepon_penerima') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
          @if ($errors->has('telepon_penerima'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('telepon_penerima') }}</p>
          @else
            <p id="telepon_penerima_error" class="text-sm text-red-600 mt-1 hidden"></p>
          @endif
        </div>
        <div class="grid grid-cols-1 gap-1">
          <label for="kode_pos" class="block text-sm font-medium text-gray-700">Kode Pos <span class="text-rose-600">*</span></label>
          <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos', $penjualan->pengiriman->kode_pos ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('kode_pos') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
          @if ($errors->has('kode_pos'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('kode_pos') }}</p>
          @else
            <p id="kode_pos_error" class="text-sm text-red-600 mt-1 hidden"></p>
          @endif
        </div>
        <div class="md:col-span-2 grid grid-cols-1 gap-1">
          <label for="alamat_penerima" class="block text-sm font-medium text-gray-700">Alamat Penerima <span class="text-rose-600">*</span></label>
          <textarea name="alamat_penerima" id="alamat_penerima" rows="3" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('alamat_penerima') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">{{ old('alamat_penerima', $penjualan->pengiriman->alamat_penerima ?? '') }}</textarea>
          @if ($errors->has('alamat_penerima'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('alamat_penerima') }}</p>
          @else
            <p id="alamat_penerima_error" class="text-sm text-red-600 mt-1 hidden"></p>
          @endif
        </div>
        <div class="grid grid-cols-1 gap-1">
          <label for="biaya_pengiriman" class="block text-sm font-medium text-gray-700">Biaya Pengiriman <span class="text-rose-600">*</span></label>
          <input type="number" name="biaya_pengiriman" id="biaya_pengiriman" value="{{ old('biaya_pengiriman', $penjualan->pengiriman->biaya_pengiriman ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('biaya_pengiriman') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}" step="0.01" min="0">
          @if ($errors->has('biaya_pengiriman'))
            <p class="text-sm text-red-600 mt-1">{{ $errors->first('biaya_pengiriman') }}</p>
          @else
            <p id="biaya_pengiriman_error" class="text-sm text-red-600 mt-1 hidden"></p>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Kasir --}}
  <div class="grid grid-cols-2 gap-3">
    <div class="grid grid-cols-1 gap-1">
      <label for="diskon_penjualan" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
      <input type="number" name="diskon_penjualan" id="diskon_penjualan" value="{{ old('diskon_penjualan', $penjualan->diskon_penjualan ?? 0) }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('diskon_penjualan') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}" min="0" max="100">
      @if ($errors->has('diskon_penjualan'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('diskon_penjualan') }}</p>
      @else
        <p id="diskon_penjualan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>
    <div class="grid grid-cols-1 gap-1">
      <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
      <select name="jenis_pembayaran" id="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('jenis_pembayaran') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
        <option value="tunai" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'tunai' ? 'selected' : '' }}>Tunai</option>
        <option value="kredit" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'kredit' ? 'selected' : '' }}>Kredit</option>
      </select>
      @if ($errors->has('jenis_pembayaran'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('jenis_pembayaran') }}</p>
      @else
        <p id="jenis_pembayaran_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>
    <div class="grid grid-cols-1 gap-1">
      <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700">Jumlah Bayar <span class="text-rose-600">*</span></label>
      <input type="number" name="jumlah_bayar" id="jumlah_bayar" value="{{ old('jumlah_bayar') }}" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('jumlah_bayar') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}" step="0.01" min="0">
      @if ($errors->has('jumlah_bayar'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('jumlah_bayar') }}</p>
      @else
        <p id="jumlah_bayar_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>
  </div>

  {{-- Catatan --}}
  <div class="grid grid-cols-1 gap-1">
    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
    <textarea name="catatan" id="catatan" rows="3" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('catatan') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}" placeholder="Tambahkan catatan jika diperlukan">{{ old('catatan', $penjualan->catatan ?? '') }}</textarea>
    @if ($errors->has('catatan'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('catatan') }}</p>
    @else
      <p id="catatan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      <p class="text-xs text-gray-500">Opsional, tambahkan catatan untuk penjualan ini.</p>
    @endif
  </div>

  {{-- Tombol --}}
  <div class="flex items-center gap-3">
    <button id="submitButton" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 {{ isset($isEdit) && $isEdit ? 'disabled:opacity-50' : '' }}"
            {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}>
      @if(isset($penjualan)) Update @else Simpan @endif
    </button>
    <a href="{{ route('admin.penjualan.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>

{{-- JavaScript untuk Toggle Ekspedisi dan Tambah Barang --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#penjualanForm');
  const ekspedisiCheckbox = document.querySelector('#ekspedisi');
  const ekspedisiForm = document.querySelector('#ekspedisiForm');
  const barangContainer = document.querySelector('#barangContainer');
  const submitButton = document.querySelector('#submitButton');

  // Toggle ekspedisi form
  ekspedisiCheckbox.addEventListener('change', function() {
    ekspedisiForm.style.display = this.checked ? 'block' : 'none';
  });

  // --- BARANG DYNAMIC ROWS ---
  let barangIndex = {{ isset($penjualan) ? $penjualan->detailPenjualan->count() : 1 }};

  function updateActionButtons() {
    const rows = barangContainer.querySelectorAll('.barang-row');
    rows.forEach((row, index) => {
      const actionCell = row.querySelector('.col-span-2');
      const isLast = index === rows.length - 1;

      actionCell.innerHTML = '';

      if (isLast) {
        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm';
        addBtn.innerHTML = '+';
        addBtn.addEventListener('click', addNewRow);
        actionCell.appendChild(addBtn);
      } else {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm';
        removeBtn.innerHTML = '−';
        removeBtn.addEventListener('click', function() {
          row.remove();
          updateActionButtons();
          reindexBarangRows();
        });
        actionCell.appendChild(removeBtn);
      }
    });
  }

  function addNewRow() {
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-12 gap-2 mb-2 barang-row items-center';

    const barangOptions = @json($barangs);
    let options = '<option value="">-- Pilih Barang --</option>';
    barangOptions.forEach(b => {
      options += `<option value="${b.id_barang}">${b.nama_barang}</option>`;
    });

    newRow.innerHTML = `
      <div class="col-span-6">
        <select name="barang[${barangIndex}][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          ${options}
        </select>
      </div>
      <div class="col-span-4">
        <input type="number" name="barang[${barangIndex}][kuantitas]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
      </div>
      <div class="col-span-2 flex justify-center">
        <!-- Tombol + akan ditambahkan oleh updateActionButtons -->
      </div>
    `;

    barangContainer.appendChild(newRow);
    barangIndex++;
    updateActionButtons();
  }

  function reindexBarangRows() {
    const rows = barangContainer.querySelectorAll('.barang-row');
    rows.forEach((row, idx) => {
      const select = row.querySelector('select');
      const input = row.querySelector('input');
      if (select) select.name = `barang[${idx}][id_barang]`;
      if (input) input.name = `barang[${idx}][kuantitas]`;
    });
    barangIndex = rows.length;
  }

  // Inisialisasi
  updateActionButtons();

  // Form validation
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset errors
    document.querySelectorAll('.text-red-600').forEach(el => {
      if (!el.classList.contains('hidden')) el.classList.add('hidden');
    });
    document.querySelectorAll('input, select, textarea').forEach(el => {
      el.classList.remove('border-red-500', 'bg-red-50');
    });

    let hasError = false;

    // Validate pelanggan/anggota
    const idPelanggan = document.querySelector('#id_pelanggan').value;
    const idAnggota = document.querySelector('#id_anggota').value;
    if (!idPelanggan && !idAnggota) {
      showError('#id_pelanggan_error', 'Pilih pelanggan atau anggota.', '#id_pelanggan');
      hasError = true;
    }
    if (idPelanggan && idAnggota) {
      showError('#id_pelanggan_error', 'Hanya boleh pilih satu: pelanggan atau anggota.', '#id_pelanggan');
      hasError = true;
    }

    // Validate barang
    const barangRows = barangContainer.querySelectorAll('.barang-row');
    if (barangRows.length === 0) {
      showError('#barang_error', 'Minimal satu barang harus dipilih.');
      hasError = true;
    } else {
      barangRows.forEach(row => {
        const select = row.querySelector('select');
        const input = row.querySelector('input');
        if (!select.value) {
          select.classList.add('border-red-500', 'bg-red-50');
          hasError = true;
        }
        if (!input.value || input.value < 1) {
          input.classList.add('border-red-500', 'bg-red-50');
          hasError = true;
        }
      });
    }

    // Validate ekspedisi
    if (ekspedisiCheckbox.checked) {
      const fields = ['id_agen_ekspedisi', 'nama_penerima', 'telepon_penerima', 'alamat_penerima', 'kode_pos', 'biaya_pengiriman'];
      fields.forEach(field => {
        const el = document.querySelector(`#${field}`);
        if (!el.value) {
          showError(`#${field}_error`, 'Field ini wajib diisi.', `#${field}`);
          hasError = true;
        }
      });
    }

    // Validate lainnya
    const diskon = document.querySelector('#diskon_penjualan').value;
    if (diskon !== '' && (diskon < 0 || diskon > 100)) {
      showError('#diskon_penjualan_error', 'Diskon maksimal 100%.', '#diskon_penjualan');
      hasError = true;
    }

    const jenisPembayaran = document.querySelector('#jenis_pembayaran').value;
    if (!jenisPembayaran) {
      showError('#jenis_pembayaran_error', 'Jenis pembayaran wajib dipilih.', '#jenis_pembayaran');
      hasError = true;
    }

    const jumlahBayar = document.querySelector('#jumlah_bayar').value;
    if (!jumlahBayar || jumlahBayar < 0) {
      showError('#jumlah_bayar_error', 'Jumlah bayar wajib diisi.', '#jumlah_bayar');
      hasError = true;
    }

    if (hasError) return;

    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit ? 'Apakah Anda yakin ingin mengedit data penjualan ini?' : 'Apakah Anda yakin ingin menyimpan data penjualan ini?';
    if (confirm(message)) {
      form.submit();
    }
  });

  function showError(errorSelector, message, inputSelector = null) {
    const errorEl = document.querySelector(errorSelector);
    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
    if (inputSelector) {
      document.querySelector(inputSelector).classList.add('border-red-500', 'bg-red-50');
    }
  }

  // Edit mode: disable submit if no changes
  @if(isset($isEdit) && $isEdit)
  const initial = {
    id_pelanggan: document.querySelector('#id_pelanggan').value,
    id_anggota: document.querySelector('#id_anggota').value,
  };

  function checkChanges() {
    const current = {
      id_pelanggan: document.querySelector('#id_pelanggan').value,
      id_anggota: document.querySelector('#id_anggota').value,
    };
    const same = Object.keys(initial).every(k => initial[k] === current[k]);
    submitButton.disabled = same;
    submitButton.classList.toggle('opacity-50', same);
  }

  document.querySelectorAll('input, select, textarea').forEach(el => {
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });
  checkChanges(); // Initial check
  @endif
});
</script>