@extends('layouts.appmaster')

@section('title', 'Edit Pelanggan')

@section('content')
<div class="container mx-auto p-6">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Pelanggan</h2>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        @include('master.data-pelanggan._form', [
            'action' => route('master.data-pelanggan.update', $pelanggan->id_pelanggan),
            'method' => 'PUT',
            'pelanggan' => $pelanggan,
            'isEdit' => true,
            'negara' => $negara,
            'provinsi' => $provinsi,
            'kota' => $kota
        ])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#pelangganForm');
    const namaPelangganInput = document.querySelector('#nama_pelanggan');
    const nomorTeleponInput = document.querySelector('#nomor_telepon');
    const kategoriPelangganSelect = document.querySelector('#kategori_pelanggan');
    const emailPelangganInput = document.querySelector('#email_pelanggan');
    const negaraSelect = document.querySelector('#id_negara');
    const provinsiSelect = document.querySelector('#id_provinsi');
    const kotaSelect = document.querySelector('#id_kota');
    const alamatPelangganInput = document.querySelector('#alamat_pelanggan');
    const submitButton = document.querySelector('#submitButton');

    const namaError = document.querySelector('#nama_pelanggan_error');
    const teleponError = document.querySelector('#nomor_telepon_error');
    const kategoriError = document.querySelector('#kategori_pelanggan_error');
    const emailError = document.querySelector('#email_pelanggan_error');
    const negaraError = document.querySelector('#id_negara_error');
    const provinsiError = document.querySelector('#id_provinsi_error');
    const kotaError = document.querySelector('#id_kota_error');
    const alamatError = document.querySelector('#alamat_pelanggan_error');

    if (!form || !submitButton) return;

    // simpan nilai awal
    const initial = {
        nama: namaPelangganInput?.value.trim() || '',
        telepon: nomorTeleponInput?.value.trim() || '',
        kategori: kategoriPelangganSelect?.value || '',
        email: emailPelangganInput?.value.trim() || '',
        negara: negaraSelect?.value || '',
        provinsi: provinsiSelect?.value || '',
        kota: kotaSelect?.value || '',
        alamat: alamatPelangganInput?.value.trim() || ''
    };

    function checkChanges() {
        const current = {
            nama: namaPelangganInput?.value.trim() || '',
            telepon: nomorTeleponInput?.value.trim() || '',
            kategori: kategoriPelangganSelect?.value || '',
            email: emailPelangganInput?.value.trim() || '',
            negara: negaraSelect?.value || '',
            provinsi: provinsiSelect?.value || '',
            kota: kotaSelect?.value || '',
            alamat: alamatPelangganInput?.value.trim() || ''
        };
        const same = initial.nama === current.nama &&
                     initial.telepon === current.telepon &&
                     initial.kategori === current.kategori &&
                     initial.email === current.email &&
                     initial.negara === current.negara &&
                     initial.provinsi === current.provinsi &&
                     initial.kota === current.kota &&
                     initial.alamat === current.alamat;
        if (same) {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50');
        } else {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50');
        }
    }

    [namaPelangganInput, nomorTeleponInput, kategoriPelangganSelect, emailPelangganInput, negaraSelect, provinsiSelect, kotaSelect, alamatPelangganInput].forEach(el => {
        if (!el) return;
        el.addEventListener('input', checkChanges);
        el.addEventListener('change', checkChanges);
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Reset
        [namaError, teleponError, kategoriError, emailError, negaraError, provinsiError, kotaError, alamatError].forEach(el => {
            if (el) { el.textContent=''; el.classList.add('hidden'); }
        });
        [namaPelangganInput, nomorTeleponInput, kategoriPelangganSelect, emailPelangganInput, negaraSelect, provinsiSelect, kotaSelect, alamatPelangganInput].forEach(el => {
            if (el) el.classList.remove('border-red-500', 'bg-red-50');
        });

        let hasError = false;
        const nama = namaPelangganInput?.value.trim() || '';
        const telepon = nomorTeleponInput?.value.trim() || '';
        const kategori = kategoriPelangganSelect?.value || '';
        const email = emailPelangganInput?.value.trim() || '';
        const negara = negaraSelect?.value || '';
        const provinsi = provinsiSelect?.value || '';
        const kota = kotaSelect?.value || '';
        const alamat = alamatPelangganInput?.value.trim() || '';

        if (!nama) {
            namaError.textContent = 'Nama pelanggan wajib diisi.';
            namaError.classList.remove('hidden');
            namaPelangganInput.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        } else if (nama.length > 100) {
            namaError.textContent = 'Nama pelanggan tidak boleh lebih dari 100 karakter.';
            namaError.classList.remove('hidden');
            namaPelangganInput.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        }

        if (!telepon) {
            teleponError.textContent = 'Nomor telepon wajib diisi.';
            teleponError.classList.remove('hidden');
            nomorTeleponInput.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        } else if (telepon.length > 20) {
            teleponError.textContent = 'Nomor telepon tidak boleh lebih dari 20 karakter.';
            teleponError.classList.remove('hidden');
            nomorTeleponInput.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        }

        if (!kategori) {
            kategoriError.textContent = 'Kategori pelanggan wajib dipilih.';
            kategoriError.classList.remove('hidden');
            kategoriPelangganSelect.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        }

        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            emailError.textContent = 'Email tidak valid.';
            emailError.classList.remove('hidden');
            emailPelangganInput.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        } else if (email.length > 100) {
            emailError.textContent = 'Email tidak boleh lebih dari 100 karakter.';
            emailError.classList.remove('hidden');
            emailPelangganInput.classList.add('border-red-500', 'bg-red-50');
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

        if (!alamat) {
            alamatError.textContent = 'Alamat wajib diisi.';
            alamatError.classList.remove('hidden');
            alamatPelangganInput.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        } else if (alamat.length > 255) {
            alamatError.textContent = 'Alamat tidak boleh lebih dari 255 karakter.';
            alamatError.classList.remove('hidden');
            alamatPelangganInput.classList.add('border-red-500', 'bg-red-50');
            hasError = true;
        }

        if (hasError) return;

        const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
        const message = isEdit
            ? 'Apakah Anda yakin ingin mengedit data pelanggan ini?'
            : 'Apakah Anda yakin ingin menyimpan data pelanggan ini?';

        if (confirm(message)) {
            form.submit();
        }
    });

    // initial check
    checkChanges();
});
</script>
@endsection