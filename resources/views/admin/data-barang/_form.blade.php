<form action="{{ isset($barang) ? route('admin.data-barang.update', $barang->id_barang) : route('admin.data-barang.store') }}" method="POST" class="space-y-6" id="barangForm">
  @csrf
  @if(isset($barang))
    @method('PUT')
  @endif
  
  <div class="grid grid-cols-2 gap-4">
    {{-- ID Barang --}}
    <div>
      <label class="block text-sm font-medium text-gray-700">ID Barang</label>
      <input type="text" name="id_barang" value="{{ old('id_barang', isset($barang) ? $barang->id_barang : ($nextId ?? '')) }}"
             readonly class="w-full rounded-md border bg-gray-100 px-3 py-2 text-sm text-gray-700 cursor-not-allowed">
      <p class="text-xs text-gray-500">
        @if(isset($barang))
          ID tidak dapat diubah.
        @else
          ID dibuat otomatis secara berurutan. Preview: {{ $nextId ?? '' }}.
        @endif
      </p>
    </div>

    {{-- Nama Barang --}}
    <div>
      <label for="nama_barang" class="block text-sm font-medium text-gray-700">
        Nama Barang <span class="text-rose-600">*</span>
      </label>
      <input
        id="nama_barang"
        name="nama_barang"
        value="{{ old('nama_barang', $barang->nama_barang ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('nama_barang') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan nama barang"
        aria-invalid="{{ $errors->has('nama_barang') ? 'true' : 'false' }}"
      >
      @if ($errors->has('nama_barang'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('nama_barang') }}</p>
      @else
        <p id="nama_barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: Laptop ASUS X550Z.</p>
      @endif
    </div>

    {{-- Kode SKU --}}
    <div>
      <label for="sku" class="block text-sm font-medium text-gray-700">
        Kode SKU <span class="text-rose-600">*</span>
      </label>
      <input
        id="sku"
        name="sku"
        value="{{ old('sku', $barang->sku ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('sku') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan kode SKU"
        aria-invalid="{{ $errors->has('sku') ? 'true' : 'false' }}"
      >
      @if ($errors->has('sku'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('sku') }}</p>
      @else
        <p id="sku_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: TS-BL-M-001</p>
      @endif
    </div>

    {{-- Kategori Barang --}}
    <div>
      <label for="id_kategori_barang" class="block text-sm font-medium text-gray-700">
        Kategori Barang <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_kategori_barang"
        name="id_kategori_barang"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_kategori_barang') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_kategori_barang') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Kategori --</option>
        @foreach($kategoriBarang as $k)
          <option value="{{ $k->id_kategori_barang }}" {{ old('id_kategori_barang', $barang->id_kategori_barang?? '') == $k->id_kategori_barang ? 'selected' : '' }}>
            {{ $k->nama_kategori }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_kategori_barang'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_kategori_barang') }}</p>
      @else
        <p id="id_kategori_barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih kategori barang.</p>
      @endif
    </div>

    {{-- Supplier --}}
    <div>
      <label for="id_supplier" class="block text-sm font-medium text-gray-700">
        Supplier <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_supplier"
        name="id_supplier"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_supplier') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_supplier') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Supplier --</option>
        @foreach($supplier as $s)
          <option value="{{ $s->id_supplier }}" {{ old('id_supplier', $barang->id_supplier ?? '') == $s->id_supplier ? 'selected' : '' }}>
            {{ $s->nama_supplier }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_supplier'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_supplier') }}</p>
      @else
        <p id="id_supplier_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih supplier barang.</p>
      @endif
    </div>

    {{-- Satuan --}}
    <div>
      <label for="id_satuan" class="block text-sm font-medium text-gray-700">
        Satuan <span class="text-rose-600">*</span>
      </label>
      <select
        id="id_satuan"
        name="id_satuan"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('id_satuan') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        aria-invalid="{{ $errors->has('id_satuan') ? 'true' : 'false' }}"
      >
        <option value="">-- Pilih Satuan --</option>
        @foreach($satuan as $s)
          <option value="{{ $s->id_satuan }}" {{ old('id_satuan', $barang->id_satuan ?? '') == $s->id_satuan ? 'selected' : '' }}>
            {{ $s->nama_satuan }}
          </option>
        @endforeach
      </select>
      @if ($errors->has('id_satuan'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('id_satuan') }}</p>
      @else
        <p id="id_satuan_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Pilih satuan barang.</p>
      @endif
    </div>

    {{-- Merk Barang --}}
    <div>
      <label for="merk_barang" class="block text-sm font-medium text-gray-700">
        Merk Barang
      </label>
      <input
        id="merk_barang"
        name="merk_barang"
        value="{{ old('merk_barang', $barang->merk_barang ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('merk_barang') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan merk barang (opsional)"
      >
      @if ($errors->has('merk_barang'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('merk_barang') }}</p>
      @else
        <p id="merk_barang_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: ASUS, Samsung.</p>
      @endif
    </div>

    {{-- Berat --}}
    <div>
      <label for="berat" class="block text-sm font-medium text-gray-700">Berat <span class="text-rose-600">*</span>
      </label>
      <input
        id="berat"
        name="berat"
        type="number"
        step="0.01"
        value="{{ old('berat', $barang->berat ?? '') }}"
        class="w-full rounded-md border px-3 py-2 text-sm {{ $errors->has('berat') ? 'border-red-500 bg-red-50 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-blue-500 focus:ring-blue-100' }}"
        placeholder="Masukkan berat barang"
      >
      @if ($errors->has('berat'))
        <p class="text-sm text-red-600 mt-1">{{ $errors->first('berat') }}</p>
      @else
        <p id="berat_error" class="text-sm text-red-600 mt-1 hidden"></p>
        <p class="text-xs text-gray-500">Contoh: 1.5 </p>
      @endif
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

    {{-- PPN --}}
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
  
  {{-- Tombol --}}
  <div class="flex items-center gap-3">
    <button 
      type="submit" 
      class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800"
      id="submitButton"
    >
      @if(isset($barang))
        Update
      @else
        Simpan
      @endif
    </button>
    <a href="{{ route('admin.data-barang.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md text-sm text-gray-700 hover:bg-gray-50">
      Batal
    </a>
  </div>
</form>

  {{-- === CUSTOM MODAL  === --}}
  <div id="customModal" class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 overflow-hidden" id="customModalContent">
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
    const form = document.getElementById('barangForm');
    form.setAttribute('novalidate', true);
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
        btnConfirm.textContent = 'Ya, Simpan';
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
      modal.classList.add('flex');
    }

    function closeModal() {
      modal.classList.add('hidden');
    }

    const showNotif = (message, type = 'success') => {
        openModal(type === 'error' ? 'Oops!' : (type === 'warning' ? 'Perhatian' : 'Berhasil'), message, type);
    };

    const showConfirm = (title, message, confirmText, callback) => {
        openModal(title, message, 'warning', callback);
    };

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        let hasError = false;

        document.querySelectorAll('.text-red-600.mt-1').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('input, select').forEach(el => el.classList.remove('border-red-500', 'bg-red-50'));

        const requiredFields = [
            { id: 'nama_barang', msg: 'Nama barang wajib diisi.' },
            { id: 'sku', msg: 'Kode SKU wajib diisi.' },
            { id: 'id_kategori_barang', msg: 'Pilih kategori barang.' },
            { id: 'id_supplier', msg: 'Pilih supplier.' },
            { id: 'id_satuan', msg: 'Pilih satuan barang.' },
            { id: 'berat', msg: 'Berat barang wajib diisi.' }
        ];

        requiredFields.forEach(field => {
            const el = document.getElementById(field.id);
            if (el && !el.value.trim()) {
                el.classList.add('border-red-500', 'bg-red-50');
                const errorText = document.getElementById(field.id + '_error');
                if (errorText) {
                    errorText.classList.remove('hidden');
                    errorText.textContent = field.msg;
                }
                hasError = true;
            }
        });

        const ppnRadios = document.querySelectorAll('input[name="kena_ppn"]');
        let ppnChecked = false;
        ppnRadios.forEach(r => { if(r.checked) ppnChecked = true; });
        
        if (!ppnChecked) {
            const errorText = document.getElementById('kena_ppn_error');
            if (errorText) {
                errorText.classList.remove('hidden');
                errorText.textContent = 'Pilih status PPN.';
            }
            hasError = true;
        }

        if (hasError) {
            showNotif('Periksa kembali isian form Anda.', 'warning');
        } else {
            const namaBarang = document.getElementById('nama_barang').value;
            showConfirm(
                'Simpan Data Barang?', 
                `Pastikan data untuk "${namaBarang}" sudah benar. Lanjutkan penyimpanan?`, 
                'Ya, Simpan', 
                () => {
                    form.submit();
                }
            );
        }
    });
});
</script>