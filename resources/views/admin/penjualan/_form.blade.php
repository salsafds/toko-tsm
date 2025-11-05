<form action="{{ $action ?? '#' }}" method="POST" class="space-y-6" id="penjualanForm" novalidate>
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- ID Penjualan --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">ID Penjualan</label>
    <input type="text" name="id_penjualan" value="{{ old('id_penjualan', $penjualan->id_penjualan ?? ($nextId ?? '')) }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
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

    @php
      $barangIndex = 0;
      $isEdit = isset($penjualan) && $penjualan->detailPenjualan->count() > 0;
    @endphp

    @if($isEdit)
      @foreach($penjualan->detailPenjualan as $detail)
        <div class="grid grid-cols-12 gap-2 mb-2 barang-row items-center">
          <div class="col-span-6">
            <select name="barang[{{ $barangIndex }}][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
              <option value="">-- Pilih Barang --</option>
              @foreach($barangs as $b)
                <option value="{{ $b->id_barang }}" data-harga="{{ $b->retail }}" {{ $detail->id_barang == $b->id_barang ? 'selected' : '' }}>
                  {{ $b->nama_barang }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-span-4">
            <input type="number" name="barang[{{ $barangIndex }}][kuantitas]" value="{{ old('barang.' . $barangIndex . '.kuantitas', $detail->kuantitas) }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
          </div>
          <div class="col-span-2 flex justify-center">
            @if($loop->last)
              <button type="button" class="add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">+</button>
            @else
              <button type="button" class="remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">-</button>
            @endif
          </div>
        </div>
        @php $barangIndex++ @endphp
      @endforeach
    @else
      <div class="grid grid-cols-12 gap-2 mb-2 barang-row items-center">
        <div class="col-span-6">
          <select name="barang[0][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
            <option value="">-- Pilih Barang --</option>
            @foreach($barangs as $b)
              <option value="{{ $b->id_barang }}" data-harga="{{ $b->retail }}">{{ $b->nama_barang }}</option>
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
    @endif

    @if ($errors->has('barang'))
      <p class="text-sm text-red-600 mt-1">{{ $errors->first('barang') }}</p>
    @else
      <p id="barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
    @endif
  </div>

  {{-- Ekspedisi --}}
  <div class="mt-6">
    <label class="flex items-center space-x-2 cursor-pointer select-none">
      <input 
        type="checkbox" 
        id="ekspedisi" 
        name="ekspedisi" 
        value="1" 
        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
        {{ old('ekspedisi') == '1' || (!empty($penjualan->pengiriman) && $penjualan->pengiriman->exists) ? 'checked' : '' }}
      >
      <span class="text-sm font-medium text-gray-700">Gunakan Ekspedisi</span>
    </label>

    <div id="ekspedisiForm" 
         class="mt-3 p-5 bg-gray-50 border border-gray-300 rounded-md transition-all duration-300 ease-in-out" 
         style="display: {{ old('ekspedisi') == '1' || (!empty($penjualan->pengiriman) && $penjualan->pengiriman->exists) ? 'block' : 'none' }};">
      <p class="text-xs font-semibold text-gray-600 mb-4 flex items-center">
        Informasi Pengiriman
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="id_agen_ekspedisi" class="block text-sm font-medium text-gray-700">Agen Ekspedisi <span class="text-rose-600">*</span></label>
          <select name="id_agen_ekspedisi" id="id_agen_ekspedisi" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300 focus:border-blue-500 focus:ring-blue-100">
            <option value="">-- Pilih Agen --</option>
            @foreach($agenEkspedisis as $ae)
              <option value="{{ $ae->id_ekspedisi }}" {{ old('id_agen_ekspedisi', $penjualan->pengiriman->id_agen_ekspedisi ?? '') == $ae->id_ekspedisi ? 'selected' : '' }}>{{ $ae->nama_ekspedisi }}</option>
            @endforeach
          </select>
          <p id="id_agen_ekspedisi_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div>
          <label for="nama_penerima" class="block text-sm font-medium text-gray-700">Nama Penerima <span class="text-rose-600">*</span></label>
          <input type="text" name="nama_penerima" id="nama_penerima" value="{{ old('nama_penerima', $penjualan->pengiriman->nama_penerima ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">
          <p id="nama_penerima_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div>
          <label for="telepon_penerima" class="block text-sm font-medium text-gray-700">Telepon Penerima <span class="text-rose-600">*</span></label>
          <input type="text" name="telepon_penerima" id="telepon_penerima" value="{{ old('telepon_penerima', $penjualan->pengiriman->telepon_penerima ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">
          <p id="telepon_penerima_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div>
          <label for="kode_pos" class="block text-sm font-medium text-gray-700">Kode Pos <span class="text-rose-600">*</span></label>
          <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos', $penjualan->pengiriman->kode_pos ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">
          <p id="kode_pos_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div class="md:col-span-2">
          <label for="alamat_penerima" class="block text-sm font-medium text-gray-700">Alamat Penerima <span class="text-rose-600">*</span></label>
          <textarea name="alamat_penerima" id="alamat_penerima" rows="2" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">{{ old('alamat_penerima', $penjualan->pengiriman->alamat_penerima ?? '') }}</textarea>
          <p id="alamat_penerima_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div>
          <label for="nomor_resi" class="block text-sm font-medium text-gray-700">Nomor Resi</label>
          <input type="text" name="nomor_resi" id="nomor_resi" value="{{ old('nomor_resi', $penjualan->pengiriman->nomor_resi ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300" placeholder="Opsional">
          <p id="nomor_resi_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div>
          <label for="biaya_pengiriman" class="block text-sm font-medium text-gray-700">Biaya Pengiriman <span class="text-rose-600">*</span></label>
          <input type="number" name="biaya_pengiriman" id="biaya_pengiriman" value="{{ old('biaya_pengiriman', $penjualan->pengiriman->biaya_pengiriman ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300" step="0.01" min="0">
          <p id="biaya_pengiriman_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>
      </div>
    </div>
  </div>

  {{-- KASIR – KONDISIONAL uang_diterima --}}
  <div class="mt-6 pt-5 border-t border-gray-300">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Kasir</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Sub Total -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Sub Total</label>
        <input type="text" id="subTotalDisplay" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed" value="Rp 0">
      </div>

      <!-- Diskon (%) -->
      <div>
        <label for="diskon_penjualan" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
        <input type="number" name="diskon_penjualan" id="diskon_penjualan" 
               value="{{ old('diskon_penjualan', $penjualan->diskon_penjualan ?? 0) }}" 
               class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
        <p id="diskon_penjualan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      </div>

      <!-- Total Bayar -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Total Bayar</label>
        <input type="text" id="totalBayarDisplay" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-blue-700 cursor-not-allowed" value="Rp 0">
      </div>

      <!-- Jenis Pembayaran -->
      <div>
        <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
        <select name="jenis_pembayaran" id="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="tunai" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'tunai' ? 'selected' : '' }}>Tunai</option>
          <option value="kredit" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'kredit' ? 'selected' : '' }}>Kredit</option>
        </select>
        <p id="jenis_pembayaran_error" class="text-sm text-red-600 mt-1 hidden"></p>
      </div>

      <!-- Uang Diterima (KONDISIONAL) -->
      <div>
        <label for="uang_diterima" class="block text-sm font-medium text-gray-700">
          Jumlah Uang Dibayarkan 
          <span id="wajib_tunai" class="text-rose-600 hidden">*</span>
        </label>
        <input type="number" name="uang_diterima" id="uang_diterima" 
               value="{{ old('uang_diterima', $penjualan->uang_diterima ?? '') }}" 
               class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" 
               step="0.01" min="0">
        <p id="uang_diterima_error" class="text-sm text-red-600 mt-1 hidden"></p>
      </div>

      <!-- Uang Kembalian -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Uang Kembalian</label>
        <input type="text" id="kembalianDisplay" readonly 
               class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold cursor-not-allowed" 
               value="Rp 0">
      </div>
    </div>
  </div>

  {{-- Catatan --}}
  <div class="mt-6">
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
    <button id="submitButton" type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800">
      @if(isset($penjualan)) Update @else Simpan @endif
    </button>
    <a href="{{ route('admin.penjualan.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const ekspedisiCheckbox = document.getElementById('ekspedisi');
  const ekspedisiForm = document.getElementById('ekspedisiForm');
  const barangContainer = document.getElementById('barangContainer');
  const diskonInput = document.getElementById('diskon_penjualan');
  const jenisPembayaranSelect = document.getElementById('jenis_pembayaran');
  const uangDiterimaInput = document.getElementById('uang_diterima');
  const wajibTunaiSpan = document.getElementById('wajib_tunai');
  const subTotalDisplay = document.getElementById('subTotalDisplay');
  const totalBayarDisplay = document.getElementById('totalBayarDisplay');
  const kembalianDisplay = document.getElementById('kembalianDisplay');

  let barangIndex = {{ $isEdit ?? false ? $penjualan->detailPenjualan->count() : 1 }};

  // --- FUNGSI KONDISI UANG DITERIMA ---
  function toggleUangDiterima() {
    const isTunai = jenisPembayaranSelect.value === 'tunai';
    
    if (isTunai) {
      uangDiterimaInput.disabled = false;
      uangDiterimaInput.removeAttribute('readonly');
      wajibTunaiSpan.classList.remove('hidden');
    } else {
      uangDiterimaInput.disabled = true;
      uangDiterimaInput.value = '';
      wajibTunaiSpan.classList.add('hidden');
      // Reset kembalian ke 0
      kembalianDisplay.value = 'Rp 0';
      kembalianDisplay.className = 'w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 cursor-not-allowed';
    }
    hitungTotal(); // Update kembalian
  }

  jenisPembayaranSelect.addEventListener('change', toggleUangDiterima);

  // --- EKSPEDISI ---
  function toggleEkspedisi() {
    const isChecked = ekspedisiCheckbox.checked;
    ekspedisiForm.style.display = isChecked ? 'block' : 'none';
    if (!isChecked) {
      ekspedisiForm.querySelectorAll('input, select, textarea').forEach(f => {
        f.disabled = true; f.value = '';
      });
    } else {
      ekspedisiForm.querySelectorAll('input, select, textarea').forEach(f => f.disabled = false);
    }
  }
  ekspedisiCheckbox.addEventListener('change', toggleEkspedisi);
  toggleEkspedisi();

  // --- BARANG DYNAMIC ---
  function updateActionButtons() {
    const rows = barangContainer.querySelectorAll('.barang-row');
    rows.forEach((row, i) => {
      const actionCell = row.querySelector('.col-span-2');
      actionCell.innerHTML = '';
      if (i === rows.length - 1) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm';
        btn.innerHTML = '+';
        btn.onclick = addNewRow;
        actionCell.appendChild(btn);
      } else {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm';
        btn.innerHTML = '-';
        btn.onclick = () => { row.remove(); updateActionButtons(); reindexBarangRows(); hitungTotal(); };
        actionCell.appendChild(btn);
      }
    });
  }

  function addNewRow() {
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-12 gap-2 mb-2 barang-row items-center';
    const options = @json($barangs).map(b => `<option value="${b.id_barang}" data-harga="${b.retail}">${b.nama_barang}</option>`).join('');
    newRow.innerHTML = `
      <div class="col-span-6">
        <select name="barang[${barangIndex}][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Barang --</option>${options}
        </select>
      </div>
      <div class="col-span-4">
        <input type="number" name="barang[${barangIndex}][kuantitas]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
      </div>
      <div class="col-span-2 flex justify-center"></div>
    `;
    barangContainer.appendChild(newRow);
    barangIndex++;
    updateActionButtons();
    attachBarangEvents(newRow);
  }

  function reindexBarangRows() {
    const rows = barangContainer.querySelectorAll('.barang-row');
    rows.forEach((row, idx) => {
      row.querySelector('select').name = `barang[${idx}][id_barang]`;
      row.querySelector('input').name = `barang[${idx}][kuantitas]`;
    });
    barangIndex = rows.length;
  }

  function attachBarangEvents(row) {
    const select = row.querySelector('select');
    const input = row.querySelector('input');
    select.addEventListener('change', hitungTotal);
    input.addEventListener('input', hitungTotal);
  }

  document.querySelectorAll('.barang-row').forEach(attachBarangEvents);
  updateActionButtons();

  // --- HITUNG TOTAL ---
  function hitungTotal() {
    let subTotal = 0;
    document.querySelectorAll('.barang-row').forEach(row => {
      const select = row.querySelector('select');
      const qty = parseFloat(row.querySelector('input').value) || 0;
      const harga = parseFloat(select.selectedOptions[0]?.dataset.harga) || 0;
      subTotal += harga * qty;
    });

    const diskonPersen = parseFloat(diskonInput.value) || 0;
    const diskon = subTotal * (diskonPersen / 100);
    const totalBayar = subTotal - diskon;

    subTotalDisplay.value = `Rp ${formatRupiah(subTotal)}`;
    totalBayarDisplay.value = `Rp ${formatRupiah(totalBayar)}`;

    // Hanya hitung kembalian jika tunai
    if (jenisPembayaranSelect.value === 'tunai') {
      const uangDiterima = parseFloat(uangDiterimaInput.value) || 0;
      const kembalian = uangDiterima - totalBayar;

      if (kembalian >= 0) {
        kembalianDisplay.value = `Rp ${formatRupiah(kembalian)}`;
        kembalianDisplay.className = 'w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-green-700 cursor-not-allowed';
      } else {
        kembalianDisplay.value = `- Rp ${formatRupiah(Math.abs(kembalian))}`;
        kembalianDisplay.className = 'w-full rounded-md border bg-red-100 px-3 py-2 text-sm font-bold text-red-700 cursor-not-allowed';
      }
    } else {
      kembalianDisplay.value = 'Rp 0';
      kembalianDisplay.className = 'w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 cursor-not-allowed';
    }
  }

  function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID').format(angka);
  }

  diskonInput.addEventListener('input', hitungTotal);
  uangDiterimaInput.addEventListener('input', hitungTotal);

  // Inisialisasi awal
  toggleUangDiterima();
  hitungTotal();

  // --- VALIDASI & SUBMIT ---
  document.getElementById('penjualanForm').addEventListener('submit', function (e) {
    e.preventDefault();
    let error = false;

    document.querySelectorAll('.text-red-600:not(.hidden)').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('input, select').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

    const p = document.getElementById('id_pelanggan').value;
    const a = document.getElementById('id_anggota').value;
    if (!p && !a) { showError('#id_pelanggan_error', 'Pilih pelanggan atau anggota.', '#id_pelanggan'); error = true; }
    if (p && a) { showError('#id_pelanggan_error', 'Hanya boleh pilih satu.', '#id_pelanggan'); error = true; }

    const rows = document.querySelectorAll('.barang-row');
    if (rows.length === 0) { showError('#barang_error', 'Minimal satu barang.'); error = true; }
    rows.forEach(r => {
      const s = r.querySelector('select').value;
      const i = r.querySelector('input').value;
      if (!s) r.querySelector('select').classList.add('border-red-500', 'bg-red-50');
      if (!i || i < 1) r.querySelector('input').classList.add('border-red-500', 'bg-red-50');
      if (!s || !i) error = true;
    });

    if (ekspedisiCheckbox.checked) {
      ['id_agen_ekspedisi', 'nama_penerima', 'telepon_penerima', 'alamat_penerima', 'kode_pos', 'biaya_pengiriman'].forEach(id => {
        const el = document.getElementById(id);
        if (!el.value) { showError(`#${id}_error`, 'Wajib diisi.', `#${id}`); error = true; }
      });
    }

    if (diskonInput.value && (diskonInput.value < 0 || diskonInput.value > 100)) {
      showError('#diskon_penjualan_error', 'Diskon maksimal 100%.', '#diskon_penjualan');
      error = true;
    }
    if (!jenisPembayaranSelect.value) {
      showError('#jenis_pembayaran_error', 'Wajib dipilih.', '#jenis_pembayaran');
      error = true;
    }

    // Validasi uang_diterima hanya jika tunai
    if (jenisPembayaranSelect.value === 'tunai') {
      if (!uangDiterimaInput.value || uangDiterimaInput.value < 0) {
        showError('#uang_diterima_error', 'Wajib diisi saat pembayaran tunai.', '#uang_diterima');
        error = true;
      }
    }

    if (!error && confirm('Simpan data penjualan?')) {
      this.submit();
    }
  });

  function showError(sel, msg, input = null) {
    const el = document.querySelector(sel);
    el.textContent = msg;
    el.classList.remove('hidden');
    if (input) document.querySelector(input).classList.add('border-red-500', 'bg-red-50');
  }
});
</script>