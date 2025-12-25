<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    
    /* Modal Animation */
    .modal-enter { opacity: 0; transform: scale(0.95); }
    .modal-enter-active { opacity: 1; transform: scale(1); transition: opacity 0.2s, transform 0.2s; }
    .modal-exit { opacity: 0; }
</style>

<form action="{{ $action ?? '#' }}" method="POST" class="space-y-6" id="penjualanForm" novalidate>
  @csrf
  @if(isset($method) && strtoupper($method) === 'PUT')
    @method('PUT')
  @endif

  {{-- SECTION 1: HEADER INFO --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- ID Penjualan --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">ID Penjualan</label>
        <input type="text" name="id_penjualan" value="{{ old('id_penjualan', $penjualan->id_penjualan ?? ($nextId ?? '')) }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
        <p class="text-xs text-gray-500 mt-1">ID dibuat otomatis.</p>
      </div>

      {{-- Pelanggan atau Anggota --}}
      <div class="grid grid-cols-2 gap-3">
        <div>
            <label for="id_pelanggan" class="block text-sm font-medium text-gray-700">Pelanggan</label>
            <select id="id_pelanggan" name="id_pelanggan" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_pelanggan') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
            <option value="">-- Pilih Pelanggan --</option>
            @foreach($pelanggans as $p)
                <option value="{{ $p->id_pelanggan }}" {{ old('id_pelanggan', $penjualan->id_pelanggan ?? '') == $p->id_pelanggan ? 'selected' : '' }}>{{ $p->nama_pelanggan }}</option>
            @endforeach
            </select>
        </div>

        <div>
            <label for="id_anggota" class="block text-sm font-medium text-gray-700">Anggota</label>
            <select id="id_anggota" name="id_anggota" class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_anggota') ? 'border-red-500 bg-red-50' : 'border-gray-200' }}">
            <option value="">-- Pilih Anggota --</option>
            @foreach($anggotas as $a)
                <option value="{{ $a->id_anggota }}" {{ old('id_anggota', $penjualan->id_anggota ?? '') == $a->id_anggota ? 'selected' : '' }}>{{ $a->nama_anggota }}</option>
            @endforeach
            </select>
        </div>
      </div>
  </div>

  {{-- SECTION 2: POS INTERFACE --}}
  <div id="posContainer" class="space-y-6 pt-4 border-t border-gray-200">
    
    {{-- CARD A: PRODUCT CATALOG --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Cari Barang
            </h3>
        </div>
        <div class="p-4">
            <div class="relative mb-4">
                <input type="text" id="productSearch" 
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm pl-4 pr-10 py-2 transition" 
                    placeholder="Ketik nama barang" autocomplete="off">
                <!-- <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-400 text-xs">🔍</span>
                </div> -->
            </div>

            <div id="searchResults" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-80 overflow-y-auto custom-scrollbar p-1">
                <div class="col-span-full text-center text-gray-400 py-8 text-sm italic">
                    Mulai ketik nama barang untuk menampilkan hasil...
                </div>
            </div>
        </div>
    </div>

    {{-- CARD B: SHOPPING CART --}}
    <div class="bg-white rounded-lg border border-blue-200 shadow-md overflow-hidden">
        <div class="bg-blue-50 px-4 py-3 border-b border-blue-200 flex justify-between items-center">
            <h3 class="text-sm font-bold text-blue-800 uppercase tracking-wider flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                Keranjang Belanja
            </h3>
            <span id="cartCountBadge" class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm">0 Item</span>
        </div>
        
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 140px;">Qty</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody" class="bg-white divide-y divide-gray-200">
                        <tr id="emptyCartMessage">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm italic">Keranjang masih kosong.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="hiddenInputsContainer"></div>
        <div id="barang_error" class="p-3 bg-red-50 text-red-600 text-sm border-t border-red-100 hidden"></div>
        @if ($errors->has('barang'))
             <div class="p-3 bg-red-50 text-red-600 text-sm border-t border-red-100">{{ $errors->first('barang') }}</div>
        @endif
    </div>
  </div>
  
  {{-- SECTION 3: EKSPEDISI --}}
  <div class="mt-6 pt-5 border-t border-gray-200">
    <label class="flex items-center space-x-2 cursor-pointer select-none mb-3">
      <input type="checkbox" id="ekspedisi" name="ekspedisi" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" {{ old('ekspedisi') == '1' || (!empty($penjualan->pengiriman) && $penjualan->pengiriman->exists) ? 'checked' : '' }}>
      <span class="text-sm font-bold text-gray-700">Gunakan Pengiriman Ekspedisi</span>
    </label>

    <div id="ekspedisiForm" class="p-5 bg-gray-50 border border-gray-300 rounded-md transition-all duration-300 ease-in-out" style="display: {{ old('ekspedisi') == '1' || (!empty($penjualan->pengiriman) && $penjualan->pengiriman->exists) ? 'block' : 'none' }};">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Agen Ekspedisi <span class="text-rose-600">*</span></label>
          <select name="id_agen_ekspedisi" id="id_agen_ekspedisi" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300 focus:border-blue-500">
            <option value="">-- Pilih Agen --</option>
            @foreach($agenEkspedisis as $ae)
              <option value="{{ $ae->id_ekspedisi }}" {{ old('id_agen_ekspedisi', $penjualan->pengiriman->id_agen_ekspedisi ?? '') == $ae->id_ekspedisi ? 'selected' : '' }}>{{ $ae->nama_ekspedisi }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Nama Penerima <span class="text-rose-600">*</span></label>
          <input type="text" name="nama_penerima" id="nama_penerima" value="{{ old('nama_penerima', $penjualan->pengiriman->nama_penerima ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Telepon Penerima <span class="text-rose-600">*</span></label>
          <input type="text" name="telepon_penerima" id="telepon_penerima" value="{{ old('telepon_penerima', $penjualan->pengiriman->telepon_penerima ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Kode Pos <span class="text-rose-600">*</span></label>
          <input type="text" name="kode_pos" id="kode_pos" value="{{ old('kode_pos', $penjualan->pengiriman->kode_pos ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Alamat Penerima <span class="text-rose-600">*</span></label>
          <textarea name="alamat_penerima" id="alamat_penerima" rows="2" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300">{{ old('alamat_penerima', $penjualan->pengiriman->alamat_penerima ?? '') }}</textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Nomor Resi</label>
          <input type="text" name="nomor_resi" id="nomor_resi" value="{{ old('nomor_resi', $penjualan->pengiriman->nomor_resi ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300" placeholder="Opsional">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Biaya Pengiriman</label>
          <input type="number" name="biaya_pengiriman" id="biaya_pengiriman" value="{{ old('biaya_pengiriman', $penjualan->pengiriman->biaya_pengiriman ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-300" step="0.01" min="0" placeholder="0">
        </div>
      </div>
    </div>
  </div>

  {{-- SECTION 4: KASIR --}}
  <div class="mt-6 pt-5 border-t border-gray-300">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
        Kasir & Pembayaran
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Sub Total</label>
        <input type="text" id="subTotalDisplay" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed font-medium" value="Rp 0">
      </div>

      <div>
        <label for="diskon_penjualan" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
        <input type="number" name="diskon_penjualan" id="diskon_penjualan" value="{{ old('diskon_penjualan', $penjualan->diskon_penjualan ?? 0) }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
      </div>

      <div>
        <label for="tarif_ppn" class="block text-sm font-medium text-gray-700">Tarif PPN (%) <span class="text-rose-600">*</span></label>
        <input type="number" name="tarif_ppn" id="tarif_ppn" value="{{ old('tarif_ppn', $penjualan->tarif_ppn ?? '0') }}" class="w-full rounded-md border-2 px-3 py-2 text-sm {{ $errors->has('tarif_ppn') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}" min="0" max="100" step="0.01" required>
      </div>

      <div>
        <label class="block text-sm font-bold text-gray-800">TOTAL BAYAR</label>
        <input type="text" id="totalBayarDisplay" readonly class="w-full rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-lg font-bold text-blue-700 cursor-not-allowed" value="Rp 0">
      </div>

      <div class="md:col-span-2">
        <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
        <select name="jenis_pembayaran" id="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
            <option value="">-- Pilih Pembayaran --</option>
            <option value="tunai" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="kredit" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran ?? '') == 'kredit' ? 'selected' : '' }}>Kredit</option>
        </select>
      </div>
    
      <div>
        <label for="uang_diterima" class="block text-sm font-medium text-gray-700">Jumlah Uang Dibayarkan <span id="wajib_tunai" class="text-rose-600 hidden">*</span></label>
        <input type="number" name="uang_diterima" id="uang_diterima" value="{{ old('uang_diterima', $penjualan->uang_diterima ?? '') }}" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 bg-gray-100 cursor-not-allowed" step="0.01" min="0" readonly>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Uang Kembalian</label>
        <input type="text" id="kembalianDisplay" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 cursor-not-allowed" value="Rp 0">
      </div>
    </div>
  </div>

  <div class="mt-6">
    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
    <textarea name="catatan" id="catatan" rows="2" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" placeholder="Opsional">{{ old('catatan', $penjualan->catatan ?? '') }}</textarea>
  </div>

  <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
   <button id="submitButton" type="submit" class="inline-flex items-center px-6 py-2 bg-blue-700 text-white text-sm font-bold rounded-md hover:bg-blue-800 shadow-sm transition-all opacity-50 cursor-not-allowed" disabled>
    @if(isset($penjualan)) Update Penjualan @else Simpan Penjualan @endif
    </button>
    <a href="{{ route('admin.penjualan.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>

{{-- === CUSTOM MODAL COMPONENT === --}}
<div id="customModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="customModalContent">
    <div class="p-5 text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" id="modalIconContainer">
        {{-- Icon injected via JS --}}
      </div>
      <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Judul</h3>
      <div class="mt-2">
        <p class="text-sm text-gray-500" id="modalMessage">Pesan</p>
      </div>
    </div>
    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2" id="modalButtons">
       {{-- Buttons injected via JS --}}
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. DOM ELEMENTS ---
    const form = document.getElementById('penjualanForm');
    const submitButton = document.getElementById('submitButton');
    const ekspedisiCheckbox = document.getElementById('ekspedisi');
    const ekspedisiForm = document.getElementById('ekspedisiForm');
    const biayaPengirimanInput = document.getElementById('biaya_pengiriman');
    const diskonInput = document.getElementById('diskon_penjualan');
    const jenisPembayaranSelect = document.getElementById('jenis_pembayaran');
    const uangDiterimaInput = document.getElementById('uang_diterima');
    const wajibTunaiSpan = document.getElementById('wajib_tunai');
    const pelangganSelect = document.getElementById('id_pelanggan');
    const anggotaSelect = document.getElementById('id_anggota');
    const searchInput = document.getElementById('productSearch');
    const resultsContainer = document.getElementById('searchResults');
    const cartTableBody = document.getElementById('cartTableBody');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const cartCountBadge = document.getElementById('cartCountBadge');
    
    // --- 2. MODAL SYSTEM ---
    const modal = document.getElementById('customModal');
    const modalContent = document.getElementById('customModalContent');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalIconContainer = document.getElementById('modalIconContainer');
    const modalButtons = document.getElementById('modalButtons');

    function openModal(title, message, type, onConfirm = null) {
      // 1. Set Content
      modalTitle.textContent = title;
      modalMessage.textContent = message;

      // 2. Set Icon & Colors
      let iconHtml = '';
      let iconColorClass = '';
      if(type === 'error') {
        iconColorClass = 'bg-red-100';
        iconHtml = `<svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
      } else if (type === 'success') {
        iconColorClass = 'bg-green-100';
        iconHtml = `<svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>`;
      } else if (type === 'warning') {
        iconColorClass = 'bg-yellow-100';
        iconHtml = `<svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
      }
      modalIconContainer.className = `mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4 ${iconColorClass}`;
      modalIconContainer.innerHTML = iconHtml;

      // 3. Set Buttons
      modalButtons.innerHTML = '';
      if (type === 'warning' || onConfirm) {
        // Confirm & Cancel
        const btnConfirm = document.createElement('button');
        btnConfirm.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm";
        btnConfirm.textContent = 'Ya, Lanjutkan';
        btnConfirm.onclick = () => { closeModal(); if(onConfirm) onConfirm(); };

        const btnCancel = document.createElement('button');
        btnCancel.className = "mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm";
        btnCancel.textContent = 'Batal';
        btnCancel.onclick = closeModal;

        modalButtons.appendChild(btnConfirm);
        modalButtons.appendChild(btnCancel);
      } else {
        // Just OK
        const btnOk = document.createElement('button');
        btnOk.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm";
        btnOk.textContent = 'OK';
        btnOk.onclick = closeModal;
        modalButtons.appendChild(btnOk);
      }

      // 4. Show
      modal.classList.remove('hidden');
      setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
      }, 10);
    }

    function closeModal() {
      modalContent.classList.remove('scale-100', 'opacity-100');
      modalContent.classList.add('scale-95', 'opacity-0');
      setTimeout(() => {
        modal.classList.add('hidden');
      }, 300);
    }

    function showNotification(msg, type = 'error') {
      openModal(type === 'error' ? 'Oops!' : 'Berhasil', msg, type);
    }

    function showConfirm(msg, callback) {
      openModal('Konfirmasi', msg, 'warning', callback);
    }

    // --- 3. DATA INITIALIZATION ---
    const allProducts = @json($barangs); 
    const existingDetails = @json(isset($penjualan) ? $penjualan->detailPenjualan : []);
    let cart = [];

    // --- 4. CORE LOGIC ---
    function initCart() {
        if (existingDetails.length > 0) {
            existingDetails.forEach(detail => {
                const product = allProducts.find(p => p.id_barang == detail.id_barang);
                if (product) {
                    cart.push({
                        id: product.id_barang,
                        name: product.nama_barang,
                        price: parseFloat(product.retail),
                        stock: parseInt(product.stok_tersedia ?? product.stok),
                        qty: parseInt(detail.kuantitas),
                        kena_ppn: product.kena_ppn ? product.kena_ppn.toLowerCase() : 'tidak'
                    });
                }
            });
            renderCart();
        }
    }

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        resultsContainer.innerHTML = '';
        if (query.length < 1) {
            resultsContainer.innerHTML = '<div class="col-span-full text-center text-gray-400 py-8 text-sm italic">Mulai ketik nama barang untuk menampilkan hasil...</div>';
            return;
        }
        const filtered = allProducts.filter(p => 
            p.nama_barang.toLowerCase().includes(query) || (p.kode_barang && p.kode_barang.toLowerCase().includes(query))
        );
        if (filtered.length === 0) {
            resultsContainer.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8 text-sm">Barang tidak ditemukan.</div>';
            return;
        }
        filtered.slice(0, 12).forEach(p => { 
            const stock = parseInt(p.stok_tersedia ?? p.stok);
            const price = parseFloat(p.retail);
            const isOOS = stock <= 0;
            const card = document.createElement('div');
            card.className = `border rounded-md p-3 flex flex-col justify-between transition hover:shadow-md ${isOOS ? 'bg-gray-100 opacity-75' : 'bg-white'}`;
            card.innerHTML = `
                <div>
                    <div class="font-bold text-sm text-gray-800 truncate" title="${p.nama_barang}">${p.nama_barang}</div>
                    <div class="text-xs text-gray-500 mt-1">Stok: <span class="${stock < 5 ? 'text-red-500 font-bold' : 'text-green-600'}">${stock}</span></div>
                    <div class="text-sm font-semibold text-blue-600 mt-1">Rp ${formatRupiah(price)}</div>
                </div>
                <button type="button" onclick="addToCart('${p.id_barang}')"
                    class="mt-3 w-full py-1.5 rounded text-xs font-bold uppercase tracking-wide flex items-center justify-center gap-1 transition-colors ${isOOS ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm active:bg-blue-800'}" ${isOOS ? 'disabled' : ''}>
                    ${isOOS ? 'Habis' : '<span class="text-lg leading-none">+</span> Tambah'}
                </button>
            `;
            resultsContainer.appendChild(card);
        });
    });

    window.addToCart = function(id) {
        const product = allProducts.find(p => p.id_barang == id);
        if (!product) return;
        const existingItem = cart.find(item => item.id == id);
        const stock = parseInt(product.stok_tersedia ?? product.stok);

        if (existingItem) {
            if (existingItem.qty >= stock) {
                showNotification('Stok tidak mencukupi untuk menambah jumlah!', 'error');
                return;
            }
            existingItem.qty++;
        } else {
            if (stock <= 0) {
                showNotification('Stok Habis!', 'error');
                return;
            }
            cart.push({
                id: product.id_barang,
                name: product.nama_barang,
                price: parseFloat(product.retail),
                stock: stock,
                qty: 1,
                kena_ppn: product.kena_ppn ? product.kena_ppn.toLowerCase() : 'tidak'
            });
        }
        renderCart();
    }

    window.updateCartQty = function(id, change) {
        const itemIndex = cart.findIndex(item => item.id == id);
        if (itemIndex === -1) return;
        const item = cart[itemIndex];

        // DELETE CONFIRMATION WITH NEW MODAL
        if (change === -1 && item.qty === 1) {
            showConfirm(`Hapus "${item.name}" dari keranjang?`, () => {
                cart.splice(itemIndex, 1);
                renderCart();
            });
            return; 
        }

        if (change === 1) {
            if (item.qty >= item.stock) {
                showNotification('Mencapai batas stok tersedia!', 'error');
                return;
            }
            item.qty++;
        } else if (change === -1) {
            item.qty--;
        }
        renderCart();
    }

    window.removeFromCart = function(id) {
        showConfirm('Hapus item ini dari keranjang?', () => {
            const itemIndex = cart.findIndex(item => item.id == id);
            if (itemIndex > -1) {
                cart.splice(itemIndex, 1);
                renderCart();
            }
        });
    }

    function renderCart() {
        cartTableBody.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';

        if (cart.length === 0) {
            cartTableBody.innerHTML = `<tr id="emptyCartMessage"><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm italic">Keranjang kosong.</td></tr>`;
            cartCountBadge.innerText = '0 Item';
            hitungTotal();
            return;
        }

        cart.forEach((item, index) => {
            const subtotal = item.price * item.qty;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${item.name}</div>
                    <div class="text-xs text-gray-500">Stok: ${item.stock}</div>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap text-sm text-gray-700">Rp ${formatRupiah(item.price)}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center justify-center border border-gray-300 rounded-md w-max mx-auto shadow-sm">
                        <button type="button" onclick="updateCartQty('${item.id}', -1)" class="px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-600 border-r border-gray-300 rounded-l-md font-bold transition">-</button>
                        <input type="text" readonly value="${item.qty}" class="w-10 text-center text-sm border-none focus:ring-0 p-1 text-gray-700 bg-white">
                        <button type="button" onclick="updateCartQty('${item.id}', 1)" class="px-2 py-1 bg-gray-50 hover:bg-gray-100 text-blue-600 border-l border-gray-300 rounded-r-md font-bold transition">+</button>
                    </div>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap text-sm font-semibold text-gray-900">Rp ${formatRupiah(subtotal)}</td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                    <button type="button" onclick="removeFromCart('${item.id}')" class="text-red-400 hover:text-red-600 transition p-1 rounded-full hover:bg-red-50">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </td>
            `;
            cartTableBody.appendChild(row);
            
            const inputId = document.createElement('input'); inputId.type = 'hidden'; inputId.name = `barang[${index}][id_barang]`; inputId.value = item.id;
            const inputQty = document.createElement('input'); inputQty.type = 'hidden'; inputQty.name = `barang[${index}][kuantitas]`; inputQty.value = item.qty;
            hiddenInputsContainer.appendChild(inputId); hiddenInputsContainer.appendChild(inputQty);
        });

        cartCountBadge.innerText = `${cart.length} Item`;
        hitungTotal();
    }

    function hitungTotal() {
        let totalDpp = 0; let totalNonPpn = 0; let subTotalBarang = 0;
        cart.forEach(item => {
            const subTotalItem = item.price * item.qty;
            subTotalBarang += subTotalItem;
            if (item.kena_ppn === 'ya') totalDpp += subTotalItem; else totalNonPpn += subTotalItem;
        });

        const biayaKirim = (ekspedisiCheckbox && ekspedisiCheckbox.checked) ? (parseFloat(biayaPengirimanInput.value) || 0) : 0;
        const subTotalKeseluruhan = subTotalBarang + biayaKirim;
        const diskonPersen = parseFloat(diskonInput.value) || 0;
        const diskonNilai = subTotalKeseluruhan * (diskonPersen / 100);
        const dppSetelahDiskon = totalDpp - (totalDpp * (diskonPersen / 100));
        const tarifPPN = parseFloat(document.getElementById('tarif_ppn').value) || 0;
        const nilaiPPN = Math.round(dppSetelahDiskon * (tarifPPN / 100));
        const totalBayar = subTotalKeseluruhan - diskonNilai + nilaiPPN;

        document.getElementById('subTotalDisplay').value = `Rp ${formatRupiah(Math.round(subTotalKeseluruhan))}`;
        document.getElementById('totalBayarDisplay').value = `Rp ${formatRupiah(Math.round(totalBayar))}`;

        const kembalianDisplay = document.getElementById('kembalianDisplay');
        if (jenisPembayaranSelect.value === 'tunai') {
            const dibayar = parseFloat(uangDiterimaInput.value) || 0;
            const kembali = dibayar - totalBayar;
            if (kembali >= 0) {
                kembalianDisplay.value = `Rp ${formatRupiah(Math.round(kembali))}`;
                kembalianDisplay.className = 'w-full rounded-md border bg-green-50 px-3 py-2 text-sm font-bold text-green-700 cursor-not-allowed';
            } else {
                kembalianDisplay.value = `- Rp ${formatRupiah(Math.round(Math.abs(kembali)))}`;
                kembalianDisplay.className = 'w-full rounded-md border bg-red-50 px-3 py-2 text-sm font-bold text-red-700 cursor-not-allowed';
            }
        } else {
            kembalianDisplay.value = 'Rp 0';
            kembalianDisplay.className = 'w-full rounded-md border bg-gray-100 px-3 py-2 text-sm font-bold text-gray-700 cursor-not-allowed';
        }
        checkFormValidity();
    }

    function formatRupiah(angka) { angka = Math.round(angka); return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }

    function checkFormValidity() {
        let isValid = true;
        if (cart.length === 0) isValid = false;
        if (!pelangganSelect.value && !anggotaSelect.value) isValid = false;
        if (!jenisPembayaranSelect.value) isValid = false;
        if (ekspedisiCheckbox.checked) {
            ['id_agen_ekspedisi','nama_penerima','telepon_penerima','kode_pos','alamat_penerima'].forEach(id => {
                if (!document.getElementById(id).value) isValid = false;
            });
        }
        submitButton.disabled = !isValid;
        if(isValid) submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        else submitButton.classList.add('opacity-50', 'cursor-not-allowed');
    }

    const watchIds = ['id_pelanggan','id_anggota','ekspedisi','id_agen_ekspedisi','nama_penerima','telepon_penerima','kode_pos','alamat_penerima','biaya_pengiriman','diskon_penjualan','tarif_ppn','jenis_pembayaran','uang_diterima'];
    watchIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.addEventListener('input', hitungTotal); el.addEventListener('change', hitungTotal); }
    });

    function togglePembeliLock() {
        const pVal = pelangganSelect.value; const aVal = anggotaSelect.value;
        anggotaSelect.disabled = !!pVal; pelangganSelect.disabled = !!aVal;
        anggotaSelect.classList.toggle('bg-gray-100', !!pVal); pelangganSelect.classList.toggle('bg-gray-100', !!aVal);
        checkFormValidity();
    }
    pelangganSelect.addEventListener('change', togglePembeliLock); anggotaSelect.addEventListener('change', togglePembeliLock);

    function toggleEkspedisi() {
        const checked = ekspedisiCheckbox.checked;
        ekspedisiForm.style.display = checked ? 'block' : 'none';
        ekspedisiForm.querySelectorAll('input, select, textarea').forEach(el => { el.disabled = !checked; if (!checked) el.value = ''; });
        hitungTotal();
    }
    ekspedisiCheckbox.addEventListener('change', toggleEkspedisi);

    function toggleUangDiterima() {
        const isTunai = jenisPembayaranSelect.value === 'tunai';
        if (isTunai) {
            uangDiterimaInput.removeAttribute('readonly'); uangDiterimaInput.classList.remove('bg-gray-100', 'cursor-not-allowed'); wajibTunaiSpan.classList.remove('hidden');
        } else {
            uangDiterimaInput.setAttribute('readonly', 'readonly'); uangDiterimaInput.classList.add('bg-gray-100', 'cursor-not-allowed'); uangDiterimaInput.value = '0'; wajibTunaiSpan.classList.add('hidden');
        }
        hitungTotal();
    }
    jenisPembayaranSelect.addEventListener('change', toggleUangDiterima);

    // FORM SUBMIT WITH CUSTOM CONFIRM
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (cart.length === 0) {
            document.getElementById('barang_error').classList.remove('hidden'); document.getElementById('barang_error').innerText = 'Keranjang belanja tidak boleh kosong!'; return;
        }
        if (jenisPembayaranSelect.value === 'tunai') {
            const total = parseFloat(document.getElementById('totalBayarDisplay').value.replace(/[^\d.-]/g,'')) || 0;
            const bayar = parseFloat(uangDiterimaInput.value) || 0;
            if (bayar < total) { showNotification('Uang pembayaran kurang!', 'error'); return; }
        }

        showConfirm('Pastikan semua data sudah benar. Simpan transaksi ini?', () => {
            form.submit();
        });
    });

    togglePembeliLock(); toggleEkspedisi(); toggleUangDiterima(); initCart();
});
</script>