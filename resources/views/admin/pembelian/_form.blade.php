<style>
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
    
    /* Modal Animation */
    .transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 300ms; }
    .scale-95 { transform: scale(0.95); }
    .scale-100 { transform: scale(1); }
    .opacity-0 { opacity: 0; }
    .opacity-100 { opacity: 1; }
</style>

<form action="{{ isset($pembelian) ? route('admin.pembelian.update', $pembelian->id_pembelian) : route('admin.pembelian.store') }}"
      method="POST" class="space-y-6" id="pembelianForm">
  @csrf
  @if(isset($pembelian))
    @method('PUT')
  @endif

  {{-- SECTION 1: HEADER INFO --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- ID Pembelian --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">ID Pembelian</label>
        <input type="text" name="id_pembelian" 
               value="{{ old('id_pembelian', isset($pembelian) ? $pembelian->id_pembelian : ($nextId ?? '')) }}" 
               readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
        <p class="text-xs text-gray-500 mt-1">ID dibuat otomatis.</p>
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
        <p id="supplier_error" class="text-xs text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500 mt-1">Pilih supplier yang sudah ada</p>
      </div>
  </div>

  <input type="hidden" name="id_user" value="{{ auth()->id() }}">

  {{-- SECTION 2: POS INTERFACE --}}
  <div id="posContainer" class="space-y-6 pt-4 border-t border-gray-200">
    
    {{-- CARD A: PRODUCT CATALOG --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Cari Barang
            </h3>
        </div>
        <div class="p-4">
            <div class="relative mb-4">
            <input type="text" id="productSearch"
                class="block w-full rounded-lg border-2 border-gray-300 bg-white shadow-sm 
                      focus:ring-blue-500 focus:border-blue-500 focus:outline-none 
                      sm:text-sm pl-4 pr-12 py-3 transition-all duration-200"
                placeholder="Ketik nama barang atau kode..." autocomplete="off">
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
                Pembelian Barang
            </h3>
            <span id="cartCountBadge" class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm">0 Item</span>
        </div>
        
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Barang</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 140px;">Qty</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" style="width: 50px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody" class="bg-white divide-y divide-gray-200">
                        <tr id="emptyCartMessage">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm italic">Keranjang kosong.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Hidden Container for Form Submission --}}
        <div id="hiddenInputsContainer"></div>
        <div id="barang_error" class="p-3 bg-red-50 text-red-600 text-sm border-t border-red-100 hidden"></div>
    </div>
  </div>

  {{-- SECTION 3: PEMBAYARAN --}}
  <div class="mt-6 pt-5 border-t border-gray-300">
    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        Rincian Pembayaran
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Sub Total</label>
        <input type="text" id="subTotalDisplay" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed font-medium" value="Rp 0">
      </div>

      <div>
        <label for="diskon" class="block text-sm font-medium text-gray-700">Diskon (%)</label>
        <input type="number" name="diskon" id="diskon" 
               value="{{ old('diskon', rtrim(rtrim(number_format($pembelian->diskon ?? 0, 2), '0'), '.')) }}" 
               class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
      </div>

      <div>
        <label for="ppn" class="block text-sm font-medium text-gray-700">PPN (%)</label>
        <input type="number" name="ppn" id="ppn" 
               value="{{ old('ppn', rtrim(rtrim(number_format($pembelian->ppn ?? 0, 2), '0'), '.')) }}" 
               class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" min="0" max="100" step="0.01">
      </div>

      <div>
    <label for="biaya_pengiriman" class="block text-sm font-medium text-gray-700">Biaya Pengiriman</label>
    <input type="text" name="biaya_pengiriman" id="biaya_pengiriman" 
           value="{{ old('biaya_pengiriman', isset($pembelian) && $pembelian->biaya_pengiriman > 0 ? number_format($pembelian->biaya_pengiriman, 0, '', '.') : 0) }}" 
           class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 harga-format" placeholder="0">
    </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Total Bayar</label>
        <input type="text" id="totalBayarDisplay" readonly 
              class="w-full rounded-md border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-bold text-blue-700 cursor-not-allowed" 
              value="Rp 0">
      </div>

      <div>
        <label for="jenis_pembayaran" class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
        <select name="jenis_pembayaran" id="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
            <option value="">-- Pilih Jenis --</option>
            <option value="Cash" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'Cash' ? 'selected' : '' }}>Tunai</option>
            <option value="Kredit" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'Kredit' ? 'selected' : '' }}>Kredit</option>
        </select>
        <p id="jenis_pembayaran_error" class="text-xs text-red-600 mt-1 hidden"></p>
      </div>
    </div>
  </div>

  <div class="mt-6">
    <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan</label>
    <textarea name="catatan" id="catatan" rows="3" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200" placeholder="Keterangan pembelian...">{{ old('catatan', $pembelian->catatan ?? '') }}</textarea>
  </div>

  {{-- Tombol --}}
  <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
    <button id="submitButton" type="submit" 
            class="inline-flex items-center px-6 py-2 bg-blue-700 text-white text-sm font-bold rounded-md hover:bg-blue-800 shadow-sm transition-all 
            {{ isset($pembelian) ? 'opacity-50 cursor-not-allowed' : '' }}"
            {{ isset($pembelian) ? 'disabled' : '' }}>
        {{ isset($pembelian) ? 'Update' : 'Simpan' }}
    </button>
    <a href="{{ route('admin.pembelian.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">Batal</a>
  </div>
</form>

{{-- SECTION 4: TAMBAH BARANG BARU (COLLAPSIBLE) --}}
<div class="border-t pt-6 mt-10">
  <div class="flex items-center mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100 transition" onclick="document.getElementById('tambah_barang').click()">
    <input id="tambah_barang" type="checkbox" name="tambah_barang" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 pointer-events-none">
    <label for="tambah_barang" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer select-none">Tambah Barang Baru (Jika tidak ada di pencarian)</label>
  </div>
  
  <form id="formTambahBarang" class="hidden space-y-6 border rounded-lg p-6 bg-white shadow-sm ring-1 ring-gray-900/5">
    @csrf
    <div class="flex justify-between items-center border-b pb-2 mb-4">
        <h3 class="text-lg font-semibold text-gray-800">Form Barang Baru</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- ID Barang --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">ID Barang</label>
        <input type="text" name="id_barang" value="{{ $nextIdBarang ?? '' }}" readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
        <p class="text-xs text-gray-500 mt-1">Otomatis.</p>
      </div>

      {{-- Nama Barang --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-rose-600">*</span></label>
        <input type="text" name="nama_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100" placeholder="Contoh: Laptop ASUS">
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="nama_barang"></p>
      </div>

      {{-- Kode SKU --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Kode SKU <span class="text-rose-600">*</span></label>
        <input type="text" name="sku" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100" placeholder="Contoh: TS-BL-M-001">
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="sku"></p>
      </div>

      {{-- Kategori --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Kategori <span class="text-rose-600">*</span></label>
        <select name="id_kategori_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100">
          <option value="">-- Pilih Kategori --</option>
          @foreach($kategoriBarang as $k)
            <option value="{{ $k->id_kategori_barang }}">{{ $k->nama_kategori }}</option>
          @endforeach
        </select>
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_kategori_barang"></p>
      </div>

      {{-- Supplier --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier <span class="text-rose-600">*</span></label>
        <select name="id_supplier" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100">
          <option value="">-- Pilih Supplier --</option>
          @foreach($suppliers as $s)
            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
          @endforeach
        </select>
         <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_supplier"></p>
      </div>

      {{-- Satuan --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Satuan <span class="text-rose-600">*</span></label>
        <select name="id_satuan" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100">
          <option value="">-- Pilih Satuan --</option>
          @foreach($satuan as $s)
            <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
          @endforeach
        </select>
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="id_satuan"></p>
      </div>

      {{-- Merk --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Merk</label>
        <input type="text" name="merk_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100" placeholder="Opsional">
      </div>

      {{-- Berat --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Berat (kg) <span class="text-rose-600">*</span></label>
        <input type="number" step="0.01" name="berat" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500 focus:ring-blue-100" placeholder="0.00">
        <p class="text-xs text-rose-600 mt-1 hidden" data-error="berat"></p>
      </div>

      {{-- Margin (%) --}}
    <div>
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
        value="{{ old('margin', isset($barang) ? ($barang->margin == 0 ? 0 : $barang->margin) : 0) }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('margin') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="0"
      >
      @if ($errors->has('margin'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('margin') }}</p>
      @else
        <p id="margin_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Margin keuntungan dalam persen (0-100)</p>
      @endif
    </div>

    {{-- Kena PPN? -- DIPINDAHKAN KE SAMPING KANAN MARGIN --}}
    <div>
      <label class="block text-sm font-medium text-gray-700">
        Kena PPN? <span class="text-rose-600">*</span>
      </label>
      <div class="mt-2 space-x-6">
        <label class="inline-flex items-center">
          <input type="radio" name="kena_ppn" value="Ya"
                 {{ old('kena_ppn', $barang->kena_ppn ?? '') === 'Ya' ? 'checked' : '' }}
                 class="form-radio text-blue-600 focus:ring-blue-500">
          <span class="ml-2 text-sm text-gray-700">Ya</span>
        </label>
        <label class="inline-flex items-center">
          <input type="radio" name="kena_ppn" value="Tidak"
                 {{ old('kena_ppn', $barang->kena_ppn ?? '') === 'Tidak' ? 'checked' : '' }}
                 class="form-radio text-blue-600 focus:ring-blue-500">
          <span class="ml-2 text-sm text-gray-700">Tidak</span>
        </label>
      </div>
      @if ($errors->has('kena_ppn'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('kena_ppn') }}</p>
      @else
        <p id="kena_ppn_error" class="text-sm text-red-600 mt-1 hidden"></p>
      @endif
    </div>
  </div>

    <div class="flex justify-end pt-3 border-t">
        <button type="button" onclick="document.getElementById('tambah_barang').click()" class="mr-3 px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</button>
        <button type="button" id="simpanBarangBtn" class="px-6 py-2 bg-green-600 text-white text-sm font-bold rounded-md hover:bg-green-700 shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Simpan barang
        </button>
    </div>
  </form>
</div>

{{-- === CUSTOM MODAL COMPONENT === --}}
<div id="customModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 hidden transition-opacity duration-300">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="customModalContent">
    <div class="p-5 text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4" id="modalIconContainer"></div>
      <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Judul</h3>
      <div class="mt-2">
        <p class="text-sm text-gray-500" id="modalMessage">Pesan</p>
      </div>
    </div>
    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2" id="modalButtons"></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. DOM ELEMENTS ---
    const form = document.getElementById('pembelianForm');
    const submitButton = document.getElementById('submitButton');
    const biayaPengirimanInput = document.getElementById('biaya_pengiriman');
    const diskonInput = document.getElementById('diskon');
    const ppnInput = document.getElementById('ppn');
    const supplierSelect = document.getElementById('id_supplier');
    const jenisPembayaranSelect = document.getElementById('jenis_pembayaran');
    const catatanInput = document.getElementById('catatan');
    
    // POS Elements
    const searchInput = document.getElementById('productSearch');
    const resultsContainer = document.getElementById('searchResults');
    const cartTableBody = document.getElementById('cartTableBody');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const cartCountBadge = document.getElementById('cartCountBadge');
    
    // Add Item Elements
    const tambahBarangCheckbox = document.getElementById('tambah_barang');
    const formTambahBarang = document.getElementById('formTambahBarang');
    formTambahBarang.querySelectorAll('input, select').forEach(input => {
        ['input', 'change'].forEach(eventType => {
            input.addEventListener(eventType, function() {
                this.classList.remove('border-red-500', 'bg-red-50');
                const fieldName = this.name;
                const errorMsg = formTambahBarang.querySelector(`[data-error="${fieldName}"]`);
                if (errorMsg) errorMsg.classList.add('hidden');
            });
        });
    });
    const simpanBarangBtn = document.getElementById('simpanBarangBtn');

    // --- 2. MODAL SYSTEM ---
    const modal = document.getElementById('customModal');
    const modalContent = document.getElementById('customModalContent');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalIconContainer = document.getElementById('modalIconContainer');
    const modalButtons = document.getElementById('modalButtons');

    function openModal(title, message, type, onConfirm = null) {
      modalTitle.textContent = title;
      modalMessage.textContent = message;
      
      let iconHtml = ''; let iconColorClass = '';
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

      modalButtons.innerHTML = '';
      if (type === 'warning' || onConfirm) {
        const btnConfirm = document.createElement('button');
        btnConfirm.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm";
        btnConfirm.textContent = 'Ya, Lanjutkan';
        btnConfirm.onclick = () => { closeModal(); if(onConfirm) onConfirm(); };
        const btnCancel = document.createElement('button');
        btnCancel.className = "mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm";
        btnCancel.textContent = 'Batal';
        btnCancel.onclick = closeModal;
        modalButtons.appendChild(btnConfirm); modalButtons.appendChild(btnCancel);
      } else {
        const btnOk = document.createElement('button');
        btnOk.className = "w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm";
        btnOk.textContent = 'OK';
        btnOk.onclick = closeModal;
        modalButtons.appendChild(btnOk);
      }
      modal.classList.remove('hidden');
      setTimeout(() => { modalContent.classList.remove('scale-95', 'opacity-0'); modalContent.classList.add('scale-100', 'opacity-100'); }, 10);
      modal.classList.add('flex');
    }

    function closeModal() {
      modalContent.classList.remove('scale-100', 'opacity-100'); modalContent.classList.add('scale-95', 'opacity-0');
      setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
    function showNotification(msg, type = 'error') { openModal(type === 'error' ? 'Oops!' : 'Berhasil', msg, type); }
    function showConfirm(msg, callback) { openModal('Konfirmasi', msg, 'warning', callback); }

    // --- 3. DATA & HELPERS ---
    const allProducts = @json($barangs);
    const existingDetails = @json(isset($pembelian) ? $pembelian->detailPembelian : []);
    let cart = [];

    function formatRupiah(angka) {
        if (!angka) return '0';
        angka = Math.round(angka);
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    function unformatRupiah(val) {
        if (!val) return 0;
        return parseFloat(val.toString().replace(/\./g, '').replace(/[^0-9.-]+/g,"")) || 0;
    }

    // --- 4. CART LOGIC ---
    function initCart() {
        if (existingDetails.length > 0) {
            existingDetails.forEach(detail => {
                const product = allProducts.find(p => p.id_barang == detail.id_barang);
                if (product) {
                    cart.push({
                        id: product.id_barang,
                        name: product.nama_barang,
                        buy_price: parseFloat(detail.harga_beli), 
                        qty: parseInt(detail.kuantitas)
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
            resultsContainer.innerHTML = '<div class="col-span-full text-center text-gray-400 py-8 text-sm italic">Mulai ketik nama barang...</div>';
            return;
        }
        const filtered = allProducts.filter(p => 
            p.nama_barang.toLowerCase().includes(query) || (p.kode_barang && p.kode_barang.toLowerCase().includes(query))
        );
        if (filtered.length === 0) {
            resultsContainer.innerHTML = '<div class="col-span-full text-center text-gray-500 py-8 text-sm">Barang tidak ditemukan. <br><span class="text-blue-600 cursor-pointer underline" onclick="document.getElementById(\'tambah_barang\').click()">Tambah Barang Baru</span></div>';
            return;
        }
        filtered.slice(0, 12).forEach(p => { 
            const defaultPrice = parseFloat(p.harga_beli);
            const card = document.createElement('div');
            card.className = `border rounded-md p-3 flex flex-col justify-between transition hover:shadow-md bg-white`;
            card.innerHTML = `
                <div>
                    <div class="font-bold text-sm text-gray-800 truncate" title="${p.nama_barang}">${p.nama_barang}</div>
                    <div class="text-xs text-gray-500 mt-1">Default Beli: Rp ${formatRupiah(defaultPrice)}</div>
                </div>
                <button type="button" onclick="addToCart('${p.id_barang}')"
                    class="mt-3 w-full py-1.5 rounded text-xs font-bold uppercase tracking-wide flex items-center justify-center gap-1 transition-colors bg-blue-600 hover:bg-blue-700 text-white shadow-sm active:bg-blue-800">
                    <span class="text-lg leading-none">+</span> Tambah
                </button>
            `;
            resultsContainer.appendChild(card);
        });
    });

    window.addToCart = function(id) {
        const product = allProducts.find(p => p.id_barang == id);
        if (!product) return;
        const existingItem = cart.find(item => item.id == id);
        if (existingItem) {
            existingItem.qty++;
        } else {
        // Untuk setiap penambahan item baru (baik create maupun edit), reset harga beli ke 0
        cart.push({
          id: product.id_barang,
          name: product.nama_barang,
          buy_price: 0,
          qty: 1
        });
        }
        renderCart();
    }

    window.updateCartItem = function(id, type, value) {
        const item = cart.find(i => i.id == id);
        if (!item) return;
        if (type === 'qty') {
            const newQty = parseInt(value);
            if (newQty > 0) item.qty = newQty;
        } else if (type === 'price') {
            const newPrice = unformatRupiah(value);
            item.buy_price = newPrice;
        }
        hitungTotal(); // Recalc and check state
    }
    
    window.changeQty = function(id, delta) {
        const index = cart.findIndex(i => i.id == id);
        if (index === -1) return;
        if (delta === -1 && cart[index].qty === 1) {
             showConfirm(`Hapus "${cart[index].name}"?`, () => {
                cart.splice(index, 1);
                renderCart();
            });
            return;
        }
        cart[index].qty += delta;
        if(cart[index].qty < 1) cart[index].qty = 1;
        renderCart();
    }

    window.removeFromCart = function(id) {
        showConfirm('Hapus item ini?', () => {
            const index = cart.findIndex(i => i.id == id);
            if (index > -1) {
                cart.splice(index, 1);
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
            const subtotal = item.buy_price * item.qty;
            const row = document.createElement('tr');
            const displayPrice = formatRupiah(item.buy_price);

            row.innerHTML = `
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${item.name}</td>
                <td class="px-4 py-3">
    <div class="relative rounded-md shadow-sm max-w-xs mx-auto">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="text-gray-500 sm:text-sm">Rp</span>
        </div>
        <input type="text" value="${displayPrice}" 
               oninput="updateCartItem('${item.id}', 'price', this.value)"
               onblur="this.value = formatRupiah(unformatRupiah(this.value))"
               class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-3 py-2 sm:text-sm border-gray-300 rounded-md text-right font-medium"
               placeholder="0">
    </div>
</td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-center border border-gray-300 rounded-md w-max mx-auto shadow-sm">
                        <button type="button" onclick="changeQty('${item.id}', -1)" class="px-2 py-1 bg-gray-50 hover:bg-gray-100 text-gray-600 border-r rounded-l-md font-bold">-</button>
                        <input type="number" value="${item.qty}" onchange="updateCartItem('${item.id}', 'qty', this.value)" class="w-12 text-center text-sm border-none focus:ring-0 p-1 bg-white" min="1">
                        <button type="button" onclick="changeQty('${item.id}', 1)" class="px-2 py-1 bg-gray-50 hover:bg-gray-100 text-blue-600 border-l rounded-r-md font-bold">+</button>
                    </div>
                </td>
                <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 total-row-display">Rp ${formatRupiah(subtotal)}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="removeFromCart('${item.id}')" class="text-red-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </td>
            `;
            cartTableBody.appendChild(row);

            const inputId = document.createElement('input'); inputId.type = 'hidden'; inputId.name = `details[${index}][id_barang]`; inputId.value = item.id;
            const inputPrice = document.createElement('input'); inputPrice.type = 'hidden'; inputPrice.name = `details[${index}][harga_beli]`; inputPrice.value = item.buy_price;
            const inputQty = document.createElement('input'); inputQty.type = 'hidden'; inputQty.name = `details[${index}][kuantitas]`; inputQty.value = item.qty;
            hiddenInputsContainer.appendChild(inputId); 
            hiddenInputsContainer.appendChild(inputPrice); 
            hiddenInputsContainer.appendChild(inputQty);
        });
        cartCountBadge.innerText = `${cart.length} Item`;
        hitungTotal();
    }

    function hitungTotal() {
        let subTotal = 0;
        const rows = document.querySelectorAll('#cartTableBody tr');
        cart.forEach((item, idx) => {
            const lineTotal = item.buy_price * item.qty;
            subTotal += lineTotal;
            if(rows[idx]) {
                 const subTotalCell = rows[idx].querySelector('.total-row-display');
                 if(subTotalCell) subTotalCell.innerText = `Rp ${formatRupiah(lineTotal)}`;
                 const hiddenPrice = hiddenInputsContainer.querySelector(`input[name="details[${idx}][harga_beli]"]`);
                 if(hiddenPrice) hiddenPrice.value = item.buy_price;
                 const hiddenQty = hiddenInputsContainer.querySelector(`input[name="details[${idx}][kuantitas]"]`);
                 if(hiddenQty) hiddenQty.value = item.qty;
            }
        });

        const biaya = unformatRupiah(biayaPengirimanInput.value) || 0;
        const diskonP = parseFloat(diskonInput.value) || 0;
        const ppnP = parseFloat(ppnInput.value) || 0;
        const valDiskon = subTotal * (diskonP / 100);
        const afterDiskon = subTotal - valDiskon;
        const valPpn = afterDiskon * (ppnP / 100);
        const total = afterDiskon + valPpn + biaya;

        document.getElementById('subTotalDisplay').value = `Rp ${formatRupiah(subTotal)}`;
        document.getElementById('totalBayarDisplay').value = `Rp ${formatRupiah(total)}`;
        
        updateButtonState();
    }

    // --- 5. STATE MANAGEMENT & BUTTON LOGIC ---
    const isEditMode = {{ isset($pembelian) ? 'true' : 'false' }};
    let initialState = null;

    function getSnapshot() {
        return JSON.stringify({
            supplier: supplierSelect.value,
            diskon: diskonInput.value,
            ppn: ppnInput.value,
            biaya: biayaPengirimanInput.value,
            jenis: jenisPembayaranSelect.value,
            catatan: catatanInput.value,
            cart: cart.map(i => ({ id: i.id, qty: i.qty, price: i.buy_price }))
        });
    }

    function updateButtonState() {
        if (!isEditMode) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            return;
        }
        const currentSnapshot = getSnapshot();
        const hasChanges = currentSnapshot !== initialState;
        submitButton.disabled = !hasChanges;
        if (hasChanges) {
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    const inputElements = [supplierSelect, jenisPembayaranSelect, diskonInput, ppnInput, biayaPengirimanInput, catatanInput];
    inputElements.forEach(el => {
        if(el) { el.addEventListener('input', updateButtonState); el.addEventListener('change', updateButtonState); }
    });
    
    // Formatting Biaya Pengiriman - lebih rapi & handle nilai 0
biayaPengirimanInput.addEventListener('input', function(e) {
    let val = e.target.value.replace(/\D/g, '');
    if (val === '' || val === '0') {
        e.target.value = '';
    } else {
        e.target.value = parseInt(val, 10).toLocaleString('id-ID').replace(/,/g, '.');
    }
    hitungTotal();
});

biayaPengirimanInput.addEventListener('blur', function() {
    if (this.value === '') {
        this.value = '';
    }
    hitungTotal();
});

// Format awal saat halaman dimuat (khusus mode edit)
if (isEditMode) {
    setTimeout(() => {
        if (!biayaPengirimanInput.value || biayaPengirimanInput.value === '0') {
            biayaPengirimanInput.value = '';
        }
        hitungTotal();
    }, 100);
}

    // --- 6. ADD NEW ITEM LOGIC ---
    tambahBarangCheckbox.addEventListener('change', function () {
      formTambahBarang.classList.toggle('hidden', !this.checked);
      if (!this.checked) {
        formTambahBarang.reset();
        formTambahBarang.querySelectorAll('[data-error]').forEach(el => { el.classList.add('hidden'); });
      }
    });

    simpanBarangBtn.addEventListener('click', function (e) {
      e.preventDefault();
      let hasError = false;
      const required = ['nama_barang', 'sku', 'id_kategori_barang', 'id_supplier', 'id_satuan', 'berat'];
      formTambahBarang.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));
      formTambahBarang.querySelectorAll('[data-error]').forEach(el => el.classList.add('hidden'));
      required.forEach(name => {
        const el = formTambahBarang.querySelector(`[name="${name}"]`);
        const err = formTambahBarang.querySelector(`[data-error="${name}"]`);
        if (!el || !el.value.trim()) {
          if(err) { err.textContent = 'Wajib diisi.'; err.classList.remove('hidden'); }
          if(el) el.classList.add('border-red-500','bg-red-50'); 
          hasError = true;
        }
      });
      const ppnChecked = formTambahBarang.querySelector('input[name="kena_ppn"]:checked');
      if (!ppnChecked) {
          const ppnErr = formTambahBarang.querySelector('[data-error="kena_ppn"]');
          ppnErr.textContent = 'Pilih salah satu.'; ppnErr.classList.remove('hidden'); hasError = true;
      }
      if (hasError) { showNotification('Mohon lengkapi field bertanda bintang (*).', 'error'); return; }
      showConfirm('Simpan barang baru ke database?', () => {
          fetch("{{ route('admin.pembelian.storeBarang') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
            body: new FormData(formTambahBarang)
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
                showNotification('Barang berhasil ditambahkan!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message || 'Gagal menyimpan.', 'error');
            }
          })
          .catch(() => showNotification('Terjadi kesalahan jaringan.', 'error'));
      });
    });

    // --- 7. FORM SUBMISSION ---
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let hasError = false;
        document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));
        document.querySelectorAll('.text-red-600').forEach(el => el.classList.add('hidden'));
        document.getElementById('barang_error').classList.add('hidden');

        if (cart.length === 0) {
            const bErr = document.getElementById('barang_error');
            bErr.classList.remove('hidden');
            bErr.innerText = 'Keranjang belanja masih kosong!';
            hasError = true;
        }
      // Validasi: pastikan setiap item memiliki harga beli > 0
      const rows = document.querySelectorAll('#cartTableBody tr');
      cart.forEach((item, idx) => {
        if (!item.buy_price || Number(item.buy_price) <= 0) {
          hasError = true;
          // Highlight input tampilan harga di baris terkait jika ada
          if (rows[idx]) {
            const priceInput = rows[idx].querySelector('input[type="text"]');
            if (priceInput) priceInput.classList.add('border-red-500', 'bg-red-50');
          }
        }
      });
      if (hasError && cart.length > 0) {
        const bErr = document.getElementById('barang_error');
        bErr.classList.remove('hidden');
        bErr.innerText = 'Periksa isian kembali: beberapa item belum memiliki Harga Beli.';
      }
        if (!supplierSelect.value) {
            supplierSelect.classList.add('border-red-500', 'bg-red-50');
            const sErr = document.getElementById('supplier_error');
            sErr.classList.remove('hidden'); sErr.innerText = 'Wajib dipilih.';
            hasError = true;
        }
        if (!jenisPembayaranSelect.value) {
            jenisPembayaranSelect.classList.add('border-red-500', 'bg-red-50');
            const jpErr = document.getElementById('jenis_pembayaran_error');
            jpErr.classList.remove('hidden'); jpErr.innerText = 'Wajib dipilih.';
            hasError = true;
        }

        if (hasError) {
            showNotification('Mohon lengkapi data yang wajib diisi.', 'warning');
            return;
        }

        const totalStr = document.getElementById('totalBayarDisplay').value;
        showConfirm(`Simpan Transaksi?\nTotal: ${totalStr}`, () => {
            form.submit();
        });
    });

    // --- INITIALIZATION ---
    initCart();
    if (isEditMode) {
        setTimeout(() => {
            initialState = getSnapshot();
            updateButtonState();
        }, 500);
    } else {
        updateButtonState();
    }
});
</script>