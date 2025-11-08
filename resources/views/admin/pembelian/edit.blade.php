@extends('layouts.app-admin')

@section('title', 'Edit Pembelian')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Pembelian</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('admin.pembelian._form', [
      'pembelian' => $pembelian,
      'suppliers' => $suppliers,
      'barangs' => $barangs,
      'kategoriBarang' => $kategoriBarang,
      'satuan' => $satuan,
      'nextIdBarang' => $nextIdBarang
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('pembelianForm');
  const submitButton = document.getElementById('submitButton');
  const supplierSelect = document.getElementById('id_supplier');
  const jenisSelect = document.getElementById('jenis_pembayaran');
  const diskonInput = document.getElementById('diskon');
  const ppnInput = document.getElementById('ppn');
  const biayaPengirimanInput = document.getElementById('biaya_pengiriman');
  const catatanInput = document.getElementById('catatan');
  const detailContainer = document.getElementById('barangContainer');
  if (!form || !submitButton || !detailContainer) return;
  const initial = {
    supplier: supplierSelect?.value || '',
    jenis: jenisSelect?.value || '',
    diskon: diskonInput?.value || '0',
    ppn: ppnInput?.value || '0',
    biayaPengiriman: biayaPengirimanInput?.value || '0',
    catatan: catatanInput?.value || '',
    details: getDetailValues()
  };
  function getDetailValues() {
    const details = [];
    document.querySelectorAll('.barang-row').forEach(row => {
      const idBarang = row.querySelector('select[name$="[id_barang]"]')?.value || '';
      const harga = row.querySelector('input[name$="[harga_beli]"]')?.value || '';
      const kuantitas = row.querySelector('input[name$="[kuantitas]"]')?.value || '';
      details.push({ idBarang, harga, kuantitas });
    });
    return details;
  }
  function checkChanges() {
    const current = {
      supplier: supplierSelect?.value || '',
      jenis: jenisSelect?.value || '',
      diskon: diskonInput?.value || '0',
      ppn: ppnInput?.value || '0',
      biayaPengiriman: biayaPengirimanInput?.value || '0',
      catatan: catatanInput?.value || '',
      details: getDetailValues()
    };
    const sameMain = initial.supplier === current.supplier &&
                     initial.jenis === current.jenis &&
                     initial.diskon === current.diskon &&
                     initial.ppn === current.ppn &&
                     initial.biayaPengiriman === current.biayaPengiriman &&
                     initial.catatan === current.catatan;
    const sameDetails = initial.details.length === current.details.length &&
                        initial.details.every((d, i) =>
                          d.idBarang === current.details[i].idBarang &&
                          d.harga === current.details[i].harga &&
                          d.kuantitas === current.details[i].kuantitas
                        );
    const hasChanges = !(sameMain && sameDetails);
    submitButton.disabled = !hasChanges;
    submitButton.classList.toggle('opacity-50', !hasChanges);
  }
  [supplierSelect, jenisSelect, diskonInput, ppnInput, biayaPengirimanInput, catatanInput].forEach(el => {
    if (el) {
      el.addEventListener('input', checkChanges);
      el.addEventListener('change', checkChanges);
    }
  });
  const observer = new MutationObserver(checkChanges);
  observer.observe(detailContainer, { childList: true, subtree: true });
  detailContainer.addEventListener('input', checkChanges);
  detailContainer.addEventListener('change', checkChanges);
  checkChanges();
});
</script>
@endsection