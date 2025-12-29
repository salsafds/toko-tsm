@extends('layouts.app-admin')

@section('title', 'Detail Pembelian')

@section('content')
<div class="container mx-auto">
  <div class="flex flex-col items-start mb-4 sm:mb-6">
    <h1 class="text-2xl sm:text-2xl font-semibold text-gray-800 text-left">Informasi Detail Pembelian</h1>
  </div>

    {{-- Tombol Kembali --}}
    <div class="mb-4">
    <a href="{{ route('admin.pembelian.index') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-gray-500 text-white rounded-md text-sm hover:bg-gray-600">
        <svg class="h-4 sm:h-5 w-4 sm:w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar
    </a>
    </div>

  <!-- Informasi Pembelian -->
  <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembelian</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">ID Pembelian</label>
        <p class="text-sm text-gray-800">{{ $pembelian->id_pembelian }}</p>
      </div>
      <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Tanggal Pembelian</label>
        <p class="text-sm text-gray-800">{{ $pembelian->tanggal_pembelian->format('d/m/Y') }}</p>
      </div>
      <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Supplier</label>
        <p class="text-sm text-gray-800">{{ $pembelian->supplier->nama_supplier }}</p>
      </div>
      <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">User</label>
        <p class="text-sm text-gray-800">{{ $pembelian->user->nama_lengkap }}</p>
      </div>
      <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Tanggal Terima</label>
        <p class="text-sm text-gray-800">
          @if($pembelian->tanggal_terima)
            {{ $pembelian->tanggal_terima->format('d/m/Y') }}
          @else
            Belum diterima
          @endif
        </p>
      </div>
    </div>
  </div>

  <!-- Informasi Biaya -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Biaya</h2>
    @php
        // sub total dari detail barang
        $subTotal = 0;
        if ($pembelian->detailPembelian) {
        foreach ($pembelian->detailPembelian as $detail) {
            $subTotal += $detail->harga_beli * $detail->kuantitas;
        }
        }
        // total pembayaran seperti di form
        $diskon = $pembelian->diskon ?? 0;
        $ppn = $pembelian->ppn ?? 0;
        $biayaPengiriman = $pembelian->biaya_pengiriman ?? 0;

        $nilaiDiskon = ($diskon / 100) * $subTotal;
        $setelahDiskon = $subTotal - $nilaiDiskon;
        $nilaiPpn = ($ppn / 100) * $setelahDiskon;
        $totalSetelahPpn = $setelahDiskon + $nilaiPpn;
        $totalBayar = $totalSetelahPpn + $biayaPengiriman;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Sub Total</label>
        <p class="text-sm text-gray-800 font-semibold">Rp {{ number_format($subTotal, 0, ',', '.') }}</p>
        </div>
        <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Diskon (%)</label>
        <p class="text-sm text-gray-800">{{ (int)$diskon }}%</p>  <!-- Ubah dari {{ $diskon }}% -->
        </div>
        <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">PPN (%)</label>
        <p class="text-sm text-gray-800">{{ (int)$ppn }}%</p>  <!-- Ubah dari {{ $ppn }}% -->
        </div>
        <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Biaya Pengiriman</label>
        <p class="text-sm text-gray-800">Rp {{ number_format($biayaPengiriman, 0, ',', '.') }}</p>
        </div>
        <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Total Bayar</label>
        <p class="text-sm font-bold text-blue-700">Rp {{ number_format($totalBayar, 0, ',', '.') }}</p>
        </div>
        <div>
        <label class="block text-xs sm:text-sm font-medium text-gray-600">Jenis Pembayaran</label>
        <p class="text-sm text-gray-800">{{ $pembelian->jenis_pembayaran == 'Cash' ? 'Tunai' : ($pembelian->jenis_pembayaran == 'Kredit' ? 'Kredit' : 'N/A') }}</p>
        </div>
    </div>
    </div>

  <!-- Detail Barang -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 border border-gray-200">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Barang</h2>
    @if($pembelian->detailPembelian && $pembelian->detailPembelian->count() > 0)
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
            <tr class="text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                <th class="w-24 sm:w-32 px-2 sm:px-4 py-2 sm:py-3 border-r border-gray-200">ID Barang</th>
                <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r border-gray-200">Nama Barang</th>
                <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r border-gray-200">Harga Beli</th>
                <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3 border-r border-gray-200">Kuantitas</th>
                <th class="w-32 sm:w-40 px-2 sm:px-4 py-2 sm:py-3">Total</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @foreach($pembelian->detailPembelian as $detail)
                <tr>
                <td class="px-2 sm:px-4 py-2 text-sm text-gray-700 border-r border-gray-100">{{ $detail->id_barang }}</td>
                <td class="px-2 sm:px-4 py-2 text-sm text-gray-700 border-r border-gray-100">{{ $detail->barang->nama_barang ?? 'N/A' }}</td>
                <td class="px-2 sm:px-4 py-2 text-sm text-gray-700 border-r border-gray-100">Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}</td>
                <td class="px-2 sm:px-4 py-2 text-sm text-gray-700 border-r border-gray-100">{{ $detail->kuantitas }}</td>
                <td class="px-2 sm:px-4 py-2 text-sm text-gray-700">Rp {{ number_format($detail->harga_beli * $detail->kuantitas, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    @else
        <p class="text-sm text-gray-500">Tidak ada detail barang untuk pembelian ini.</p>
    @endif
        </div>
    </div>
    @endsection