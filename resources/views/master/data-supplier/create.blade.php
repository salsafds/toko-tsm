@extends('layouts.appmaster')

@section('title', 'Tambah Supplier')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Supplier</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @include('master.data-supplier._form', [
      'action' => route('master.data-supplier.store'),
      'method' => 'POST',
      'supplier' => null,
      'nextId' => $nextId,
      'negara' => $negara,
      'provinsi' => $provinsi,
      'kota' => $kota,
      'isEdit' => false
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

  const namaError = document.querySelector('#nama_supplier_error');
  const alamatError = document.querySelector('#alamat_error');
  const negaraError = document.querySelector('#id_negara_error');
  const provinsiError = document.querySelector('#id_provinsi_error');
  const kotaError = document.querySelector('#id_kota_error');
  const teleponError = document.querySelector('#telepon_supplier_error');
  const emailError = document.querySelector('#email_supplier_error');

  if (!form) return;

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
      // basic email format check
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
