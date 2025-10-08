@extends('layouts.appmaster')

@section('title', 'Edit Satuan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Satuan</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- $satuan harus diberikan oleh controller --}}
    @include('master.data-satuan._form', [
      'action' => route('master.data-satuan.update', $satuan->id_satuan),
      'method' => 'PUT',
      'satuan' => $satuan
    ])
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form#satuanForm') || document.querySelector('form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit 
      ? 'Apakah Anda yakin ingin memperbarui data satuan ini?' 
      : 'Apakah Anda yakin ingin menyimpan data satuan ini?';

    if (!confirm(message)) {
      e.preventDefault();
    }
  });
});
</script>
@endsection
