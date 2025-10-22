@extends('layouts.appmaster')

@section('title', 'Edit Supplier')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Supplier</h2>
    @include('master.data-supplier._form', [
      'action' => route('master.data-supplier.update', $supplier->id_supplier),
      'method' => 'PUT',
      'supplier' => $supplier,
      'negara' => $negara,
      'provinsi' => $provinsi,
      'kota' => $kota,
      'isEdit' => true
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#supplierForm');
  const namaInput = document.querySelector('#nama_supplier');
  const alamatInput = document.querySelector('#alamat');
  const negaraSelect = document.querySelector('#id_negara');
  const provinsiSelect = document.querySelector('#id_provinsi');
  const kotaSelect = document.querySelector('#id_kota');
  const teleponInput = document.querySelector('#telepon_supplier');
  const emailInput = document.querySelector('#email_supplier');
  const submitButton = document.querySelector('#submitButton');

  const namaError = document.querySelector('#nama_supplier_error');
  const alamatError = document.querySelector('#alamat_error');
  const negaraError = document.querySelector('#id_negara_error');
  const provinsiError = document.querySelector('#id_provinsi_error');
  const kotaError = document.querySelector('#id_kota_error');
  const teleponError = document.querySelector('#telepon_supplier_error');
  const emailError = document.querySelector('#email_supplier_error');

  if (!form) return;

  // simpan nilai awal
  const initial = {
    nama: namaInput?.value.trim() || '',
    alamat: alamatInput?.value.trim() || '',
    negara: negaraSelect?.value || '',
    provinsi: provinsiSelect?.value || '',
    kota: kotaSelect?.value || '',
    telepon: teleponInput?.value.trim() || '',
    email: emailInput?.value.trim() || ''
  };

  function checkChanges() {
    const current = {
      nama: namaInput?.value.trim() || '',
      alamat: alamatInput?.value.trim() || '',
      negara: negaraSelect?.value || '',
      provinsi: provinsiSelect?.value || '',
      kota: kotaSelect?.value || '',
      telepon: teleponInput?.value.trim() || '',
      email: emailInput?.value.trim() || ''
    };

    const same = Object.keys(initial).every(k => initial[k] === current[k]);
    if (same) {
      submitButton.disabled = true;
      submitButton.classList.add('opacity-50');
    } else {
      submitButton.disabled = false;
      submitButton.classList.remove('opacity-50');
    }
  }

  [namaInput, alamatInput, negaraSelect, provinsiSelect, kotaSelect, teleponInput, emailInput].forEach(el => {
    if (!el) return;
    el.addEventListener('input', checkChanges);
    el.addEventListener('change', checkChanges);
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset
    [namaError, alamatError, negaraError, provinsiError, kotaError, teleponError, emailError].forEach(el => {
      if (el) { el.textContent = ''; el.classList.add('hidden'); }
    });
    [namaInput, alamatInput, negaraSelect, provinsiSelect, kotaSelect, teleponInput, emailInput].forEach(el => {
      if (el) el.classList.remove('border-red-500', 'bg-red-50');
    });

    let hasError = false;

    const nama = namaInput?.value.trim() || '';
    const alamat = alamatInput?.value.trim() || '';
    const negara = negaraSelect?.value || '';
    const provinsi = provinsiSelect?.value || '';
    const kota = kotaSelect?.value || '';
    const telepon = teleponInput?.value.trim() || '';
    const email = emailInput?.value.trim() || '';

    if (!nama) {
      namaError.textContent = 'Nama supplier wajib diisi.';
      namaError.classList.remove('hidden');
      namaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else if (nama.length > 100) {
      namaError.textContent = 'Nama supplier tidak boleh lebih dari 100 karakter.';
      namaError.classList.remove('hidden');
      namaInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!alamat) {
      alamatError.textContent = 'Alamat wajib diisi.';
      alamatError.classList.remove('hidden');
      alamatInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!negara) {
      negaraError.textContent = 'Negara wajib dipilih.';
      negaraError.classList.remove('hidden');
      negaraSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!provinsi) {
      provinsiError.textContent = 'Provinsi wajib dipilih.';
      provinsiError.classList.remove('hidden');
      provinsiSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!kota) {
      kotaError.textContent = 'Kota wajib dipilih.';
      kotaError.classList.remove('hidden');
      kotaSelect.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!telepon) {
      teleponError.textContent = 'Telepon wajib diisi.';
      teleponError.classList.remove('hidden');
      teleponInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (!email) {
      emailError.textContent = 'Email wajib diisi.';
      emailError.classList.remove('hidden');
      emailInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    } else {
      const re = /\S+@\S+\.\S+/;
      if (!re.test(email)) {
        emailError.textContent = 'Format email tidak valid.';
        emailError.classList.remove('hidden');
        emailInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }
    }

    if (hasError) return;

    // Konfirmasi
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit ? 'Apakah Anda yakin ingin mengedit data supplier ini?' : 'Apakah Anda yakin ingin menyimpan data supplier ini?';
    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>
@endsection
