@extends('layouts.app-admin')

@section('title', 'Edit Penjualan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Penjualan</h2>
    
    @include('admin.penjualan._form', [
      'action' => route('admin.penjualan.update', $penjualan->id_penjualan),
      'method' => 'PUT',
      'penjualan' => $penjualan,
      'pelanggans' => $pelanggans,
      'anggotas' => $anggotas,
      'barangs' => $barangs,
      'agenEkspedisis' => $agenEkspedisis,
      'isEdit' => true
    ])
  </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('penjualanForm');
  const submitButton = document.getElementById('submitButton');
  const barangContainer = document.getElementById('barangContainer');

  if (!form || !submitButton || !barangContainer) return;

  const initial = {
    id_pelanggan: document.getElementById('id_pelanggan')?.value || '',
    id_anggota: document.getElementById('id_anggota')?.value || '',
    ekspedisi: document.getElementById('ekspedisi')?.checked || false,
    id_agen_ekspedisi: document.getElementById('id_agen_ekspedisi')?.value || '',
    nama_penerima: document.getElementById('nama_penerima')?.value || '',
    telepon_penerima: document.getElementById('telepon_penerima')?.value || '',
    kode_pos: document.getElementById('kode_pos')?.value || '',
    alamat_penerima: document.getElementById('alamat_penerima')?.value || '',
    nomor_resi: document.getElementById('nomor_resi')?.value || '',
    biaya_pengiriman: document.getElementById('biaya_pengiriman')?.value || '',
    diskon_penjualan: document.getElementById('diskon_penjualan')?.value || '0',
    jenis_pembayaran: document.getElementById('jenis_pembayaran')?.value || '',
    uang_diterima: document.getElementById('uang_diterima')?.value || '',
    catatan: document.getElementById('catatan')?.value || '',
    barang: getBarangValues()
  };

  function getBarangValues() {
    const values = [];
    document.querySelectorAll('.barang-row').forEach(row => {
      const id_barang = row.querySelector('select[name$="[id_barang]"]')?.value || '';
      const kuantitas = row.querySelector('input[name$="[kuantitas]"]')?.value || '';
      values.push({ id_barang, kuantitas });
    });
    return values;
  }

  function checkChanges() {
    const current = {
      id_pelanggan: document.getElementById('id_pelanggan')?.value || '',
      id_anggota: document.getElementById('id_anggota')?.value || '',
      ekspedisi: document.getElementById('ekspedisi')?.checked || false,
      id_agen_ekspedisi: document.getElementById('id_agen_ekspedisi')?.value || '',
      nama_penerima: document.getElementById('nama_penerima')?.value || '',
      telepon_penerima: document.getElementById('telepon_penerima')?.value || '',
      kode_pos: document.getElementById('kode_pos')?.value || '',
      alamat_penerima: document.getElementById('alamat_penerima')?.value || '',
      nomor_resi: document.getElementById('nomor_resi')?.value || '',
      biaya_pengiriman: document.getElementById('biaya_pengiriman')?.value || '',
      diskon_penjualan: document.getElementById('diskon_penjualan')?.value || '0',
      jenis_pembayaran: document.getElementById('jenis_pembayaran')?.value || '',
      uang_diterima: document.getElementById('uang_diterima')?.value || '',
      catatan: document.getElementById('catatan')?.value || '',
      barang: getBarangValues()
    };


    const mainChanged = 
      initial.id_pelanggan !== current.id_pelanggan ||
      initial.id_anggota !== current.id_anggota ||
      initial.ekspedisi !== current.ekspedisi ||
      initial.diskon_penjualan !== current.diskon_penjualan ||
      initial.jenis_pembayaran !== current.jenis_pembayaran ||
      initial.uang_diterima !== current.uang_diterima ||
      initial.catatan !== current.catatan;


    const ekspedisiChanged = current.ekspedisi && (
      initial.id_agen_ekspedisi !== current.id_agen_ekspedisi ||
      initial.nama_penerima !== current.nama_penerima ||
      initial.telepon_penerima !== current.telepon_penerima ||
      initial.kode_pos !== current.kode_pos ||
      initial.alamat_penerima !== current.alamat_penerima ||
      initial.nomor_resi !== current.nomor_resi ||
      initial.biaya_pengiriman !== current.biaya_pengiriman
    );


    const barangChanged = 
      initial.barang.length !== current.barang.length ||
      initial.barang.some((init, i) => {
        const curr = current.barang[i] || {};
        return init.id_barang !== curr.id_barang || init.kuantitas !== curr.kuantitas;
      });

    const hasChanges = mainChanged || ekspedisiChanged || barangChanged;

    submitButton.disabled = !hasChanges;
    submitButton.classList.toggle('opacity-50', !hasChanges);
    submitButton.classList.toggle('cursor-not-allowed', !hasChanges);
  }

  const inputs = [
    'id_pelanggan', 'id_anggota', 'ekspedisi', 'id_agen_ekspedisi',
    'nama_penerima', 'telepon_penerima', 'kode_pos', 'alamat_penerima',
    'nomor_resi', 'biaya_pengiriman', 'diskon_penjualan',
    'jenis_pembayaran', 'uang_diterima', 'catatan'
  ];

  inputs.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', checkChanges);
      el.addEventListener('change', checkChanges);
    }
  });

  const observer = new MutationObserver(checkChanges);
  observer.observe(barangContainer, { childList: true, subtree: true });


  barangContainer.addEventListener('input', checkChanges);
  barangContainer.addEventListener('change', checkChanges);


  checkChanges();
});
</script>