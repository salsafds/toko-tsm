@extends('layouts.appmaster')

@section('title', 'Tambah Satuan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Satuan</h2>
    {{-- jika ada flash errors global --}}
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    {{-- include form partial --}}
    @include('master.dataSatuan._form', [
      'action' => route('master.dataSatuan.store') ?? '#',
      'method' => 'POST',
      'satuan' => null
    ])
  </div>
</div>
@endsection
