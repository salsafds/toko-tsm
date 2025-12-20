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
      <div>
        <label for="id_pelanggan" class="block text-sm font-medium text-gray-700">Pelanggan</label>
        <p class="text-xs text-gray-500 -mt-1 mb-1">Pilih pelanggan jika pembeli adalah pelanggan.</p>
      </div>
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
      @endif
    </div>

    <div class="grid grid-cols-1 gap-1">
      <div>
        <label for="id_anggota" class="block text-sm font-medium text-gray-700">Anggota</label>
        <p class="text-xs text-gray-500 -mt-1 mb-1">Pilih anggota jika pembeli adalah anggota.</p>
      </div>
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
                <option value="{{ $b->id_barang }}"
                        data-harga="{{ $b->retail }}"
                        data-stok="{{ $b->stok_tersedia ?? $b->stok }}"
                        data-kenappn="{{ strtolower($b->kena_ppn) }}"
                        {{ $detail->id_barang == $b->id_barang ? 'selected' : '' }}>
                  {{ $b->nama_barang }} (Tersedia: {{ $b->stok_tersedia ?? $b->stok }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-span-2">
            <input type="text" readonly class="harga-retail w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed text-right"
                  value="Rp {{ number_format($detail->barang->retail, 0, ',', '.') }}">
          </div>
          <div class="col-span-2">
            <input type="number" name="barang[{{ $barangIndex }}][kuantitas]"
                  value="{{ old('barang.' . $barangIndex . '.kuantitas', $detail->kuantitas) }}"
                  class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
          </div>
          <div class="col-span-1 flex justify-center">
            @if($loop->last)
              <button type="button" class="add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">+</button>
            @else
              <button type="button" class="remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">-</button>
            @endif
          </div>
          <div class="col-span-1"></div>
        </div>
        @php $barangIndex++ @endphp
      @endforeach
    @else
      <div class="grid grid-cols-12 gap-2 mb-2 barang-row items-center">
        <div class="col-span-6">
          <select name="barang[0][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
            <option value="">-- Pilih Barang --</option>
            @foreach($barangs as $b)
              <option value="{{ $b->id_barang }}"
                      data-harga="{{ $b->retail }}"
                      data-stok="{{ $b->stok_tersedia ?? $b->stok }}"
                      data-kenappn="{{ strtolower($b->kena_ppn) }}">
                {{ $b->nama_barang }} (Tersedia: {{ $b->stok_tersedia ?? $b->stok }})
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-span-2">
          <input type="text" readonly class="harga-retail w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed text-right" value="">
        </div>
        <div class="col-span-2">
          <input type="number" name="barang[0][kuantitas]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
        </div>
        <div class="col-span-1 flex justify-center">
          <button type="button" class="add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm">+</button>
        </div>
        <div class="col-span-1"></div>
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
          <label for="biaya_pengiriman" class="block text-sm font-medium text-gray-700">Biaya Pengiriman</label>
          <input type="number" name="biaya_pengiriman" id="biaya_pengiriman" value="{{ old('biaya_pengiriman', $penjualan->pengiriman->biaya_pengiriman ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300" step="0.01" min="0" placeholder="0">
          <p class="text-xs text-gray-500">Opsional, jika tidak diisi = Rp 0</p>
        </div>
      </div>
    </div>
  </div>

  {{-- KASIR --}}
  <div class="mt-6 pt-5 border-t border-gray-300">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Kasir</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Sub Total</label>
        <input type="text" id="subTotalDisplay" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed" value="Rp 0">
      </div>

      <div>
        <label for="diskon_penjualan" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
        <input type="number" name="diskon_penjualan" id="diskon_penjualan" 
               value="{{ old('diskon_penjualan', $penjualan->diskon_penjualan ?? 0) }}" 
               class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
        <p id="diskon_penjualan_error" class="text-sm text-red-600 mt-1 hidden"></p>
      </div>
      <div>
      <label for="tarif_ppn" class="block text-sm font-medium text-gray-700">
        Tarif PPN (%) <span class="text-rose-600">*</span>
      </label>
      <input 
        type="number"
        name="tarif_ppn"
        id="tarif_ppn"
        value="{{ old('tarif_ppn', $penjualan->tarif_ppn ?? '0') }}"
        class="w-full rounded-md border-2 px-3 py-2 text-sm transition-colors focus:outline-none
               {{ $errors->has('tarif_ppn') 
                   ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-4 focus:ring-red-500/20' 
                   : 'border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20' }}"
        min="0"
        max="100"
        step="0.01"
        required
      >
      @error('tarif_ppn')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Total Bayar</label>
      <input type="text" id="totalBayarDisplay" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-blue-700 cursor-not-allowed" value="Rp 0">
    </div>

    {{-- Jenis Pembayaran --}}
    <div class="md:col-span-2">
      <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
      <select name="jenis_pembayaran" id="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
        <option value="">-- Pilih Pembayaran --</option>
        <option value="tunai" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'tunai' ? 'selected' : '' }}>Tunai</option>
        <option value="kredit" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'kredit' ? 'selected' : '' }}>Kredit</option>
      </select>
      <p id="jenis_pembayaran_error" class="text-sm text-red-600 mt-1 hidden"></p>
    </div>
    
    {{-- Uang Diterima dan Kembalian --}}
    <div>
      <label for="uang_diterima" class="block text-sm font-medium text-gray-700">
        Jumlah Uang Dibayarkan 
        <span id="wajib_tunai" class="text-rose-600 hidden">*</span>
      </label>
      <input 
        type="number" 
        name="uang_diterima" 
        id="uang_diterima" 
        value="{{ old('uang_diterima', $penjualan->uang_diterima ?? '') }}" 
        class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 bg-gray-100 cursor-not-allowed" 
        step="0.01" 
        min="0"
        readonly
      >
      <p id="uang_diterima_error" class="text-sm text-red-600 mt-1 hidden"></p>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Uang Kembalian</label>
      <input type="text" id="kembalianDisplay" readonly 
             class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 cursor-not-allowed" 
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
   <button id="submitButton" type="submit" 
        class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800 
               {{ isset($penjualan) ? 'opacity-50 cursor-not-allowed' : '' }}"
        {{ isset($penjualan) ? 'disabled' : '' }}>
    @if(isset($penjualan)) Update @else Simpan @endif
    </button>
    <a href="{{ route('admin.penjualan.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form= document.getElementById('penjualanForm');
  const submitButton= document.getElementById('submitButton');
  const barangContainer= document.getElementById('barangContainer');
  const ekspedisiCheckbox= document.getElementById('ekspedisi');
  const ekspedisiForm= document.getElementById('ekspedisiForm');
  const biayaPengirimanInput= document.getElementById('biaya_pengiriman');
  const diskonInput= document.getElementById('diskon_penjualan');
  const jenisPembayaranSelect= document.getElementById('jenis_pembayaran');
  const uangDiterimaInput= document.getElementById('uang_diterima');
  const wajibTunaiSpan= document.getElementById('wajib_tunai');
  const subTotalDisplay= document.getElementById('subTotalDisplay');
  const totalBayarDisplay= document.getElementById('totalBayarDisplay');
  const kembalianDisplay= document.getElementById('kembalianDisplay');
  const pelangganSelect= document.getElementById('id_pelanggan');
  const anggotaSelect= document.getElementById('id_anggota');

  const isEditMode = {{ isset($penjualan) ? 'true' : 'false' }};
  if (!isEditMode) {
    submitButton.disabled = false;
    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
  }

  let initialState = null;
  if (isEditMode) {
    initialState = {
      pelanggan:        pelangganSelect?.value || '',
      anggota:          anggotaSelect?.value || '',
      ekspedisi:        ekspedisiCheckbox?.checked || false,
      agen:             document.getElementById('id_agen_ekspedisi')?.value || '',
      nama_penerima:    document.getElementById('nama_penerima')?.value || '',
      telepon_penerima: document.getElementById('telepon_penerima')?.value || '',
      kode_pos:         document.getElementById('kode_pos')?.value || '',
      alamat_penerima:  document.getElementById('alamat_penerima')?.value || '',
      nomor_resi:       document.getElementById('nomor_resi')?.value || '',
      biaya_pengiriman: biayaPengirimanInput?.value || '',
      diskon:           diskonInput?.value || '0',
      tarif_ppn:        document.getElementById('tarif_ppn')?.value || '0',
      jenis_pembayaran: jenisPembayaranSelect?.value || '',
      uang_diterima:    uangDiterimaInput?.value || '0',
      catatan:          document.getElementById('catatan')?.value || '',
      barang:           getCurrentBarang()
    };
  }

  function getCurrentBarang() {
    const items = [];
    document.querySelectorAll('.barang-row').forEach(row => {
      const id  = row.querySelector('select[name$="[id_barang]"]')?.value || '';
      const qty = row.querySelector('input[name$="[kuantitas]"]')?.value || '';
      items.push({id, qty});
    });
    return items;
  }

  function hasChanges() {
    if (!isEditMode) return true;
    const current = {
      pelanggan:        pelangganSelect?.value || '',
      anggota:          anggotaSelect?.value || '',
      ekspedisi:        ekspedisiCheckbox?.checked || false,
      agen:             document.getElementById('id_agen_ekspedisi')?.value || '',
      nama_penerima:    document.getElementById('nama_penerima')?.value || '',
      telepon_penerima: document.getElementById('telepon_penerima')?.value || '',
      kode_pos:         document.getElementById('kode_pos')?.value || '',
      alamat_penerima:  document.getElementById('alamat_penerima')?.value || '',
      nomor_resi:       document.getElementById('nomor_resi')?.value || '',
      biaya_pengiriman: biayaPengirimanInput?.value || '',
      diskon:           diskonInput?.value || '0',
      tarif_ppn:        document.getElementById('tarif_ppn')?.value || '0',
      jenis_pembayaran: jenisPembayaranSelect?.value || '',
      uang_diterima:    uangDiterimaInput?.value || '0',
      catatan:          document.getElementById('catatan')?.value || '',
      barang:           getCurrentBarang()
    };

    const mainChanged = 
      current.pelanggan !== initialState.pelanggan ||
      current.anggota   !== initialState.anggota ||
      current.ekspedisi !== initialState.ekspedisi ||
      current.diskon    !== initialState.diskon ||
      current.tarif_ppn !== initialState.tarif_ppn || 
      current.jenis_pembayaran !== initialState.jenis_pembayaran ||
      current.uang_diterima    !== initialState.uang_diterima ||
      current.catatan          !== initialState.catatan;

    const ekspedisiChanged = current.ekspedisi && (
      current.agen          !== initialState.agen ||
      current.nama_penerima !== initialState.nama_penerima ||
      current.telepon_penerima !== initialState.telepon_penerima ||
      current.kode_pos      !== initialState.kode_pos ||
      current.alamat_penerima !== initialState.alamat_penerima ||
      current.nomor_resi    !== initialState.nomor_resi ||
      current.biaya_pengiriman !== initialState.biaya_pengiriman
    );

    const barangChanged = 
      current.barang.length !== initialState.barang.length ||
      current.barang.some((item, i) => {
        const init = initialState.barang[i] || {id:'', qty:''};
        return item.id !== init.id || item.qty !== init.qty;
      });

    return mainChanged || ekspedisiChanged || barangChanged;
  }

  function updateButtonState() {
    const changed = hasChanges();
    submitButton.disabled = !changed;
    if (changed) {
      submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
      submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
  }

  const watchIds = ['id_pelanggan','id_anggota','ekspedisi','id_agen_ekspedisi','nama_penerima','telepon_penerima','kode_pos','alamat_penerima','nomor_resi','biaya_pengiriman','diskon_penjualan','tarif_ppn','jenis_pembayaran','uang_diterima','catatan'];
  watchIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', updateButtonState);
      el.addEventListener('change', updateButtonState);
    }
  });

  const observer = new MutationObserver(updateButtonState);
  observer.observe(barangContainer, { childList: true, subtree: true });
  barangContainer.addEventListener('input', updateButtonState);
  barangContainer.addEventListener('change', updateButtonState);

  let barangIndex = {{ $isEdit ?? false ? $penjualan->detailPenjualan->count() : 1 }};

  function togglePembeliLock() {
    const pVal = pelangganSelect.value;
    const aVal = anggotaSelect.value;
    anggotaSelect.disabled = !!pVal;
    pelangganSelect.disabled = !!aVal;
    anggotaSelect.classList.toggle('bg-gray-100', !!pVal);
    anggotaSelect.classList.toggle('cursor-not-allowed', !!pVal);
    pelangganSelect.classList.toggle('bg-gray-100', !!aVal);
    pelangganSelect.classList.toggle('cursor-not-allowed', !!aVal);
  }
  pelangganSelect.addEventListener('change', togglePembeliLock);
  anggotaSelect.addEventListener('change', togglePembeliLock);
  togglePembeliLock();

  function toggleEkspedisi() {
    const checked = ekspedisiCheckbox.checked;
    ekspedisiForm.style.display = checked ? 'block' : 'none';
    ekspedisiForm.querySelectorAll('input, select, textarea').forEach(el => {
      el.disabled = !checked;
      if (!checked) el.value = '';
    });
    hitungTotal();
    updateButtonState();
  }
  ekspedisiCheckbox.addEventListener('change', toggleEkspedisi);
  toggleEkspedisi();

  function toggleUangDiterima() {
    const isTunai = jenisPembayaranSelect.value === 'tunai';

    if (isTunai) {
      uangDiterimaInput.removeAttribute('readonly');
      uangDiterimaInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
      wajibTunaiSpan.classList.remove('hidden');
    } else {
      uangDiterimaInput.setAttribute('readonly', 'readonly');
      uangDiterimaInput.classList.add('bg-gray-100', 'cursor-not-allowed');
      uangDiterimaInput.value = '0';  
      wajibTunaiSpan.classList.add('hidden');

      kembalianDisplay.value = 'Rp 0';
      kembalianDisplay.className = 'w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 cursor-not-allowed';
    }

    hitungTotal();
    updateButtonState();
  }

  jenisPembayaranSelect.addEventListener('change', toggleUangDiterima);

  function addNewRow() {
    const newRow = document.createElement('div');
    newRow.className = 'grid grid-cols-12 gap-2 mb-2 barang-row items-center';
    const options = @json($barangs).map(b => `
  <option value="${b.id_barang}" 
          data-harga="${b.retail}" 
          data-stok="${b.stok_tersedia ?? b.stok}"
          data-kenappn="${b.kena_ppn ? b.kena_ppn.toLowerCase() : 'tidak'}">
    ${b.nama_barang} (Tersedia: ${b.stok_tersedia ?? b.stok})
  </option>`).join('');
    newRow.innerHTML = `
      <div class="col-span-6">
        <select name="barang[${barangIndex}][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Barang --</option>${options}
        </select>
      </div>
      <div class="col-span-2">
        <input type="text" readonly class="harga-retail w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed text-right" value="">
      </div>
      <div class="col-span-2">
        <input type="number" name="barang[${barangIndex}][kuantitas]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="1" placeholder="Qty">
      </div>
      <div class="col-span-1 flex justify-center"></div>
      <div class="col-span-1"></div>
    `;
    barangContainer.appendChild(newRow);
    barangIndex++;
    updateActionButtons();
    attachBarangEvents(newRow);
    hitungTotal();
    updateButtonState();
  }

  function updateActionButtons() {
    const rows = barangContainer.querySelectorAll('.barang-row');
    rows.forEach((row, i) => {
      const cell = row.querySelector('.col-span-1:nth-child(4)');
      cell.innerHTML = '';
      if (i === rows.length - 1) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'add-barang-btn bg-blue-500 hover:bg-blue-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm';
        btn.innerHTML = '+';
        btn.onclick = addNewRow;
        cell.appendChild(btn);
      } else {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'remove-barang-btn bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full text-xs font-bold flex items-center justify-center shadow-sm';
        btn.innerHTML = '-';
        btn.onclick = () => {
          row.remove();
          reindexBarangRows();
          hitungTotal();
          updateButtonState();
        };
        cell.appendChild(btn);
      }
    });
  }

  function reindexBarangRows() {
    const rows = barangContainer.querySelectorAll('.barang-row');
    rows.forEach((row, idx) => {
      row.querySelector('select').name = `barang[${idx}][id_barang]`;
      row.querySelector('input[type="number"]').name = `barang[${idx}][kuantitas]`;
    });
    barangIndex = rows.length;
  }

  function attachBarangEvents(row) {
    const select    = row.querySelector('select');
    const qtyInput  = row.querySelector('input[type="number"]');
    const hargaInput= row.querySelector('.harga-retail');

    select.addEventListener('change', function () {
      const opt   = this.selectedOptions[0];
      const stok  = opt ? parseInt(opt.dataset.stok) || 0 : 0;
      const harga = opt ? parseFloat(opt.dataset.harga) || 0 : 0;
      qtyInput.max = stok;
      qtyInput.dataset.maxStok = stok;
      hargaInput.value = harga > 0 ? 'Rp ' + formatRupiah(harga) : '';
      if (parseInt(qtyInput.value) > stok) qtyInput.value = stok > 0 ? stok : '';
      hitungTotal();
      updateButtonState();
    });

    qtyInput.addEventListener('input', function () {
      const max = parseInt(this.dataset.maxStok) || 0;
      let val   = parseInt(this.value) || 0;
      if (val > max && max > 0) this.value = max;
      if (val < 1 && this.value !== '') this.value = 1;
      hitungTotal();
      updateButtonState();
    });

    if (select.value) {
      const opt = select.selectedOptions[0];
      qtyInput.max = opt.dataset.stok;
      qtyInput.dataset.maxStok = opt.dataset.stok;
      hargaInput.value = 'Rp ' + formatRupiah(opt.dataset.harga);
    }
  }

  document.querySelectorAll('.barang-row').forEach(attachBarangEvents);
  updateActionButtons();

function hitungTotal() {
    let totalDpp = 0;           
    let totalNonPpn = 0;        
    let subTotalBarang = 0;

    document.querySelectorAll('.barang-row').forEach(row => {
        const select = row.querySelector('select');
        const qtyInput = row.querySelector('input[type="number"]');
        if (!select || !qtyInput) return;

        const option = select.selectedOptions[0];
        if (!option || !option.value) return;

        const harga = parseFloat(option.dataset.harga) || 0;
        const qty = parseFloat(qtyInput.value) || 0;
        const subTotalItem = harga * qty;

        subTotalBarang += subTotalItem;
        const kenaPpn = option.dataset.kenappn === 'ya';
        
        if (kenaPpn) {
            totalDpp += subTotalItem;
        } else {
            totalNonPpn += subTotalItem;
        }
    });

    const biayaKirim = ekspedisiCheckbox.checked ? (parseFloat(biayaPengirimanInput.value) || 0) : 0;
    const subTotalKeseluruhan = subTotalBarang + biayaKirim;

    const diskonPersen = parseFloat(diskonInput.value) || 0;
    const diskonNilai = subTotalKeseluruhan * (diskonPersen / 100);

    const dppSetelahDiskon = totalDpp - (totalDpp * (diskonPersen / 100));

    const tarifPPN = parseFloat(document.getElementById('tarif_ppn').value) || 0;
    const nilaiPPN = Math.round(dppSetelahDiskon * (tarifPPN / 100));

    const totalBayar = subTotalKeseluruhan - diskonNilai + nilaiPPN;

    subTotalDisplay.value = `Rp ${formatRupiah(Math.round(subTotalKeseluruhan))}`;
    totalBayarDisplay.value = `Rp ${formatRupiah(Math.round(totalBayar))}`;

    if (jenisPembayaranSelect.value === 'tunai') {
        const dibayar = parseFloat(uangDiterimaInput.value) || 0;
        const kembali = dibayar - totalBayar;
        if (kembali >= 0) {
            kembalianDisplay.value = `Rp ${formatRupiah(Math.round(kembali))}`;
            kembalianDisplay.className = 'w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-green-700 cursor-not-allowed';
        } else {
            kembalianDisplay.value = `- Rp ${formatRupiah(Math.round(Math.abs(kembali)))}`;
            kembalianDisplay.className = 'w-full rounded-md border bg-red-100 px-3 py-2 text-sm font-bold text-red-700 cursor-not-allowed';
        }
    } else {
        kembalianDisplay.value = 'Rp 0';
        kembalianDisplay.className = 'w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 cursor-not-allowed';
    }
}


  function formatRupiah(angka) {
    angka = Math.round(angka);
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
  
  const tarifPPNInput = document.getElementById('tarif_ppn');
if (tarifPPNInput) {
    tarifPPNInput.addEventListener('input', hitungTotal);
    tarifPPNInput.addEventListener('change', hitungTotal);
}
  diskonInput.addEventListener('input', hitungTotal);
  biayaPengirimanInput.addEventListener('input', hitungTotal);
  uangDiterimaInput.addEventListener('input', hitungTotal);

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    let hasError = false;

    document.querySelectorAll('.text-red-600:not(.hidden)').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('input, select, textarea').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

    const p = pelangganSelect.value;
    const a = anggotaSelect.value;
    if (!p && !a) {
      document.getElementById('id_pelanggan_error').classList.remove('hidden');
      document.getElementById('id_anggota_error').classList.remove('hidden');
      document.getElementById('id_pelanggan_error').textContent = 'Pilih salah satu.';
      hasError = true;
    } else if (p && a) {
      document.getElementById('id_pelanggan_error').classList.remove('hidden');
      document.getElementById('id_pelanggan_error').textContent = 'Hanya boleh pilih salah satu.';
      hasError = true;
    }

    const rows = document.querySelectorAll('.barang-row');
    if (rows.length === 0) {
      document.getElementById('barang_error').classList.remove('hidden');
      hasError = true;
    }
    rows.forEach(row => {
      const sel = row.querySelector('select');
      const qty = row.querySelector('input[type="number"]');
      if (!sel.value) { sel.classList.add('border-red-500','bg-red-50'); hasError = true; }
      if (!qty.value || qty.value < 1) { qty.classList.add('border-red-500','bg-red-50'); hasError = true; }
    });

    if (ekspedisiCheckbox.checked) {
      ['id_agen_ekspedisi','nama_penerima','telepon_penerima','alamat_penerima','kode_pos'].forEach(id => {
        const el = document.getElementById(id);
        if (!el.value.trim()) {
          el.classList.add('border-red-500','bg-red-50');
          document.getElementById(id+'_error').classList.remove('hidden');
          document.getElementById(id+'_error').textContent = 'Wajib diisi.';
          hasError = true;
        }
      });
    }

    if (!jenisPembayaranSelect.value) {
      document.getElementById('jenis_pembayaran_error').classList.remove('hidden');
      jenisPembayaranSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else {
      jenisPembayaranSelect.classList.remove('border-red-500', 'bg-red-50');
    }

    if (jenisPembayaranSelect.value === 'tunai') {
      const total = parseFloat(totalBayarDisplay.value.replace(/[^\d.-]/g,'')) || 0;
      const bayar = parseFloat(uangDiterimaInput.value) || 0;
      if (bayar < total) {
        uangDiterimaInput.classList.add('border-red-500','bg-red-50');
        document.getElementById('uang_diterima_error').classList.remove('hidden');
        document.getElementById('uang_diterima_error').textContent = 'Uang kurang.';
        hasError = true;
      }
    }

    if (hasError) {
      alert('Periksa kembali isian Anda.');
      return;
    }

    if (confirm('Simpan perubahan penjualan?')) {
      form.submit();
    }
  });

  toggleUangDiterima(); 
  hitungTotal();
  updateButtonState();
});
</script>