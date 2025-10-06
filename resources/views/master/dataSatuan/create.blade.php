@extends('layouts.appmaster')

@section('title', 'Tambah Satuan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Satuan</h2>
    
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

    @include('master.dataSatuan._form', [
      'action' => route('master.dataSatuan.store'),
      'method' => 'POST',
      'satuan' => null,
      'nextId' => $nextId
    ])
  </div>
</div>
@endsection