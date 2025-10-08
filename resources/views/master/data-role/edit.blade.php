{{-- resources/views/master/data-role/edit.blade.php --}}
@extends('layouts.appmaster')

@section('title', 'Edit Role')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Edit Data Role</h2>

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

    {{-- controller harus mengirim $role --}}
    @include('master.data-role._form', [
      'action' => route('master.data-role.update', $role->id_role),
      'method' => 'PUT',
      'role' => $role
    ])
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#roleForm') || document.querySelector('form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    const isEdit = form.querySelector('input[name="_method"]')?.value === 'PUT';
    const message = isEdit
      ? 'Apakah Anda yakin ingin memperbarui data role ini?'
      : 'Apakah Anda yakin ingin menyimpan data role ini?';

    if (!confirm(message)) {
      e.preventDefault();
    }
  });
});
</script>
@endsection
