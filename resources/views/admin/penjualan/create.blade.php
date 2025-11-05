@extends('layouts.app-admin')

@section('title', 'Tambah Penjualan')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Penjualan</h2>
    
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @include('admin.penjualan._form', [
      'action' => route('admin.penjualan.store'),
      'method' => 'POST',
      'penjualan' => null,
      'nextId' => $nextId,
      'pelanggans' => $pelanggans,
      'anggotas' => $anggotas,
      'barangs' => $barangs,
      'agenEkspedisis' => $agenEkspedisis,
      'isEdit' => false
    ])
  </div>
</div>
@endsection