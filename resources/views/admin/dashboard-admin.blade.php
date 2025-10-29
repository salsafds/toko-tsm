@extends('layouts.app-admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container mx-auto">
  <!-- Breadcrumb / Title -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Dashboard Admin</h1>
      <p class="text-sm text-gray-500 mt-1">Ringkasan aktivitas & statistik koperasi</p>
    </div>
  </div>

  <!-- Statistik cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Total Anggota</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">1,234</div>
      <div class="text-xs text-green-600 mt-1">+4.2% vs last month</div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Transaksi Hari Ini</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">56</div>
      <div class="text-xs text-red-500 mt-1">-1.1% vs yesterday</div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Pendapatan (Bulan)</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">Rp 24.500.000</div>
      <div class="text-xs text-green-600 mt-1">+8.6% vs last month</div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Produk Terjual</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">1,020</div>
      <div class="text-xs text-green-600 mt-1">+2.3% vs last month</div>
    </div>
  </div>

  <!-- Dua kolom: grafik + aktivitas terbaru -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <!-- Grafik placeholder (ambil lib chart nanti) -->
    <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-medium text-gray-700">Pendapatan 30 Hari</h2>
        <span class="text-xs text-gray-500">Updated 2 hours ago</span>
      </div>
      <div class="h-64 flex items-center justify-center border border-dashed border-gray-200 rounded">
        <span class="text-sm text-gray-400">[Grafik placeholder — nanti gunakan Chart.js / Recharts]</span>
      </div>
    </div>

    <!-- Aktivitas / Transaksi terbaru -->
    <div class="bg-white p-4 rounded-lg shadow-sm">
      <h2 class="text-sm font-medium text-gray-700 mb-3">Transaksi Terbaru</h2>
      <ul class="space-y-3">
        <li class="flex items-start justify-between">
          <div>
            <div class="text-sm font-medium">Pembelian - Toko A</div>
            <div class="text-xs text-gray-500">12 Apr 2025 • Rp 120.000</div>
          </div>
          <div class="text-sm text-gray-700">Selesai</div>
        </li>

        <li class="flex items-start justify-between">
          <div>
            <div class="text-sm font-medium">Penjualan - Anggota B</div>
            <div class="text-xs text-gray-500">11 Apr 2025 • Rp 250.000</div>
          </div>
          <div class="text-sm text-gray-700">Pending</div>
        </li>

        <li class="flex items-start justify-between">
          <div>
            <div class="text-sm font-medium">Simpanan - Anggota C</div>
            <div class="text-xs text-gray-500">10 Apr 2025 • Rp 500.000</div>
          </div>
          <div class="text-sm text-gray-700">Sukses</div>
        </li>
      </ul>

      <a href="#" class="mt-4 block text-sm text-blue-700 hover:underline">Lihat semua transaksi</a>
    </div>
  </div>

  <!-- Tabel contoh -->
  <div class="bg-white p-4 rounded-lg shadow-sm">
    <h2 class="text-sm font-medium text-gray-700 mb-4">Riwayat Transaksi</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr class="text-left text-xs text-gray-500 uppercase">
            <th class="px-3 py-2">No</th>
            <th class="px-3 py-2">Tanggal</th>
            <th class="px-3 py-2">Jenis</th>
            <th class="px-3 py-2">Akun</th>
            <th class="px-3 py-2">Jumlah</th>
            <th class="px-3 py-2">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          {{-- contoh row statis; nanti render dari controller --}}
          <tr>
            <td class="px-3 py-3 text-sm text-gray-700">1</td>
            <td class="px-3 py-3 text-sm text-gray-700">2025-04-12</td>
            <td class="px-3 py-3 text-sm text-gray-700">Pembelian</td>
            <td class="px-3 py-3 text-sm text-gray-700">Toko A</td>
            <td class="px-3 py-3 text-sm text-gray-700">Rp 120.000</td>
            <td class="px-3 py-3 text-sm text-green-600">Selesai</td>
          </tr>

          <tr>
            <td class="px-3 py-3 text-sm text-gray-700">2</td>
            <td class="px-3 py-3 text-sm text-gray-700">2025-04-11</td>
            <td class="px-3 py-3 text-sm text-gray-700">Penjualan</td>
            <td class="px-3 py-3 text-sm text-gray-700">Anggota B</td>
            <td class="px-3 py-3 text-sm text-gray-700">Rp 250.000</td>
            <td class="px-3 py-3 text-sm text-yellow-600">Pending</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
