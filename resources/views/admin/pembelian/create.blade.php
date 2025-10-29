Create.blade.php
@extends('layouts.app-admin')

@section('title', 'Tambah Pembelian')

@section('content')
<div class="container mx-auto p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Data Pembelian</h2>

    @if(session('error'))
      <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">{{ session('error') }}</div>
    @endif

    @include('admin.pembelian._form', [
      'action' => route('admin.pembelian.store'),
      'method' => 'POST',
      'pembelian' => null,
      'nextId' => $nextId,
      'suppliers' => $suppliers,
      'users' => $users,
      'barangs' => $barangs,
      'isEdit' => false
    ])
  </div>
</div>
@endsection