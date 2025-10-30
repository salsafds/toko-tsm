
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
           value="{{ old('tanggal_pembelian', $pembelian->tanggal_pembelian ?? '') }}"
           class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
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
  </div>

  {{-- User --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">User <span class="text-rose-600">*</span></label>
    <select id="id_user" name="id_user" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
      <option value="">-- Pilih User --</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}" {{ old('id_user', $pembelian->id_user ?? '') == $u->id ? 'selected' : '' }}>
          {{ $u->name }}
        </option>
      @endforeach
    </select>
  </div>

  {{-- Jenis Pembayaran --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Jenis Pembayaran <span class="text-rose-600">*</span></label>
    <select id="jenis_pembayaran" name="jenis_pembayaran" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
      <option value="">-- Pilih Jenis --</option>
      <option value="cash" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'cash' ? 'selected' : '' }}>Cash/Tunai</option>
      <option value="kredit" {{ old('jenis_pembayaran', $pembelian->jenis_pembayaran ?? '') == 'kredit' ? 'selected' : '' }}>Kredit</option>
    </select>
  </div>

  {{-- Jumlah Bayar --}}
  <div>
    <label class="block text-sm font-medium text-gray-700">Jumlah Bayar <span class="text-rose-600">*</span></label>
    <input id="jumlah_bayar" name="jumlah_bayar" type="number" step="0.01"
           value="{{ old('jumlah_bayar', $pembelian->jumlah_bayar ?? '') }}"
           class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
  </div>

  {{-- Section Detail Barang --}}
  <div id="detail_section" class="space-y-4">
    <h3 class="text-lg font-medium text-gray-800">Detail Barang</h3>
    <div id="detail_container">
      <div class="detail_row grid grid-cols-4 gap-3 items-end">
        <div>
          <label class="block text-sm font-medium text-gray-700">Barang</label>
          <select name="details[0][id_barang]" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
            <option value="">-- Pilih Barang --</option>
            @foreach($barangs as $b)
              <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
          <input name="details[0][harga_beli]" type="number" step="0.01" min="0" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Stok</label>
          <input name="details[0][kuantitas]" type="number" min="1" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200 focus:border-blue-500">
        </div>
        <div class="flex items-center gap-2">
          <button type="button" class="add_detail inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Tambah</button>
          <button type="button" class="remove_detail inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 hidden">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Tombol Simpan Pembelian --}}
  <div class="flex items-center gap-3">
    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-700 text-white text-sm rounded-md hover:bg-blue-800">
      {{ isset($pembelian) ? 'Update' : 'Simpan Pembelian' }}
    </button>
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
        <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
        <input type="text" name="nama_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Kategori Barang</label>
        <select name="id_kategori_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Kategori --</option>
          @foreach($kategoriBarang as $k)
            <option value="{{ $k->id_kategori_barang }}">{{ $k->nama_kategori }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Supplier</label>
        <select name="id_supplier_barang" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Supplier --</option>
          @foreach($suppliers as $s)
            <option value="{{ $s->id_supplier }}">{{ $s->nama_supplier }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Satuan</label>
        <select name="id_satuan" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
          <option value="">-- Pilih Satuan --</option>
          @foreach($satuan as $s)
            <option value="{{ $s->id_satuan }}">{{ $s->nama_satuan }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Berat (kg)</label>
        <input type="number" step="0.01" name="berat" class="w-full rounded-md border px-3 py-2 text-sm border-gray-200">
      </div>
    </div>

    <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">Simpan Barang</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tambahBarangCheckbox = document.getElementById('tambah_barang');
  const formTambahBarang = document.getElementById('formTambahBarang');

  tambahBarangCheckbox.addEventListener('change', () => {
    formTambahBarang.classList.toggle('hidden', !tambahBarangCheckbox.checked);
  });

  formTambahBarang.addEventListener('submit', function (e) {
    e.preventDefault();

    fetch("{{ route('admin.data-barang.store') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: new FormData(formBarang)
      })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Barang berhasil ditambahkan!');
        formTambahBarang.reset();
        formTambahBarang.classList.add('hidden');
        tambahBarangCheckbox.checked = false;

        // Update dropdown barang di form pembelian
        document.querySelectorAll('select[name^="details"]').forEach(select => {
          const option = document.createElement('option');
          option.value = data.barang.id_barang;
          option.textContent = data.barang.nama_barang;
          select.appendChild(option);
        });
      } else {
        alert('Gagal menambah barang.');
      }
    })
    .catch(() => alert('Terjadi kesalahan.'));
  });
});
</script>
