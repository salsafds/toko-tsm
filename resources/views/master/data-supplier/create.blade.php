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
  const submitButton = document.querySelector('#submitButton');

  const namaError = document.querySelector('#nama_supplier_error');
  const alamatError = document.querySelector('#alamat_error');
  const negaraError = document.querySelector('#id_negara_error');
  const provinsiError = document.querySelector('#id_provinsi_error');
  const kotaError = document.querySelector('#id_kota_error');
  const teleponError = document.querySelector('#telepon_supplier_error');
  const emailError = document.querySelector('#email_supplier_error');

  if (!form || !negaraSelect || !provinsiSelect || !kotaSelect) {
    console.error('Form or dropdown elements not found');
    return;
  }

  // Dynamic dropdown logic
  function updateProvinsiOptions(id_negara, selectedProvinsi = '') {
    if (!id_negara) {
      provinsiSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
      kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
      return;
    }

    const url = form.dataset.provinsisUrl.replace(':id_negara', id_negara);
    console.log('Fetching provinsi from:', url); // Debug log
    fetch(url, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json'
      }
    })
      .then(response => {
        console.log('Provinsi response status:', response.status); // Debug log
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        return response.json();
      })
      .then(data => {
        console.log('Provinsi data:', data); // Debug log
        provinsiSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
        data.forEach(provinsi => {
          const option = document.createElement('option');
          option.value = provinsi.id_provinsi;
          option.textContent = provinsi.nama_provinsi;
          if (provinsi.id_provinsi === selectedProvinsi) {
            option.selected = true;
          }
          provinsiSelect.appendChild(option);
        });
        if (selectedProvinsi) {
          updateKotaOptions(selectedProvinsi, kotaSelect.dataset.selected || '');
        } else {
          kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        }
      })
      .catch(error => {
        console.error('Error fetching provinsi:', error);
        provinsiSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
        kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        provinsiError.textContent = 'Gagal memuat provinsi. Silakan coba lagi.';
        provinsiError.classList.remove('hidden');
      });
  }

  function updateKotaOptions(id_provinsi, selectedKota = '') {
    if (!id_provinsi) {
      kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
      return;
    }

    const url = form.dataset.kotasUrl.replace(':id_provinsi', id_provinsi);
    console.log('Fetching kota from:', url); // Debug log
    fetch(url, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json'
      }
    })
      .then(response => {
        console.log('Kota response status:', response.status); // Debug log
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        return response.json();
      })
      .then(data => {
        console.log('Kota data:', data); // Debug log
        kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        data.forEach(kota => {
          const option = document.createElement('option');
          option.value = kota.id_kota;
          option.textContent = kota.nama_kota;
          if (kota.id_kota === selectedKota) {
            option.selected = true;
          }
          kotaSelect.appendChild(option);
        });
      })
      .catch(error => {
        console.error('Error fetching kota:', error);
        kotaSelect.innerHTML = '<option value="">-- Pilih Kota --</option>';
        kotaError.textContent = 'Gagal memuat kota. Silakan coba lagi.';
        kotaError.classList.remove('hidden');
      });
  }

  // Initialize dropdowns on page load
  if (negaraSelect.value) {
    console.log('Initializing with negara:', negaraSelect.value, 'provinsi:', provinsiSelect.dataset.selected); // Debug log
    updateProvinsiOptions(negaraSelect.value, provinsiSelect.dataset.selected || '');
  }

  // Event listeners for dropdown changes
  negaraSelect.addEventListener('change', () => {
    console.log('Negara changed to:', negaraSelect.value); // Debug log
    updateProvinsiOptions(negaraSelect.value);
  });

  provinsiSelect.addEventListener('change', () => {
    console.log('Provinsi changed to:', provinsiSelect.value); // Debug log
    updateKotaOptions(provinsiSelect.value);
  });

  // Form submission with validation
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // Reset errors
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
    } else if (telepon.length > 20) {
      teleponError.textContent = 'Telepon tidak boleh lebih dari 20 karakter.';
      teleponError.classList.remove('hidden');
      teleponInput.classList.add('border-red-500', 'bg-red-50');
      hasError = true;
    }

    if (email) {
      const re = /\S+@\S+\.\S+/;
      if (!re.test(email)) {
        emailError.textContent = 'Format email tidak valid.';
        emailError.classList.remove('hidden');
        emailInput.classList.add('border-red-500', 'bg-red-50');
        hasError = true;
      }
    }

    if (hasError) return;

    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit ? 'Apakah Anda yakin ingin mengedit data supplier ini?' : 'Apakah Anda yakin ingin menyimpan data supplier ini?';
    if (confirm(message)) {
      form.submit();
    }
  });
});
</script>
@endsection