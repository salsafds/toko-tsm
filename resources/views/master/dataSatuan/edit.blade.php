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

    {{-- $satuan harus diberikan oleh controller --}}
    @include('master.dataSatuan._form', [
      'action' => route('master.dataSatuan.update', $satuan->id) ?? '#',
      'method' => 'PUT',
      'satuan' => $satuan
    ])
  </div>
</div>
@endsection
