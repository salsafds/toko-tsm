{{-- resources/views/master/data-role/create.blade.php --}}
@extends('layouts.appmaster')

@section('title', 'Tambah Role')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Role</h2>

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

    @include('master.data-role._form', [
      'action' => route('master.data-role.store'),
      'method' => 'POST',
      'role' => null,
      'nextId' => $nextId
    ])
  </div>
</div>
@endsection
