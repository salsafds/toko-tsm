@extends('layouts.app-admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container mx-auto">

  {{-- HEADER --}}
  <div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Dashboard Admin Toko</h1>
    <p class="text-sm text-gray-500 mt-1">Ringkasan operasional hari ini</p>
  </div>

  {{-- 1️⃣ SUMMARY CARDS --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Pendapatan Hari Ini</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">
        Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}
      </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Penjualan Hari Ini</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">
        {{ $totalPenjualanHariIni }}
      </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Total Transaksi</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">
        {{ $totalTransaksiHariIni }}
      </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Barang Terjual</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">
        {{ $totalBarangTerjualHariIni }}
      </div>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Total Pembeli</div>
      <div class="mt-2 text-2xl font-bold text-gray-800">
        {{ $totalPembeliHariIni }}
      </div>
    </div>
  </div>

  {{-- 2️⃣ GRAFIK --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow-sm">
      <div class="flex justify-between mb-3">
        <h2 class="text-sm font-medium text-gray-700">Penjualan & Pembelian Hari Ini</h2>
        <span class="text-xs text-gray-500">Realtime</span>
      </div>
      <div class="h-64">
        <canvas id="chartHarian"></canvas>
      </div>
    </div>

    {{-- 3️⃣ TRANSAKSI TERBARU --}}
    <div class="bg-white p-4 rounded-lg shadow-sm">
      <h2 class="text-sm font-medium text-gray-700 mb-3">Transaksi Terbaru</h2>

      <ul class="space-y-3">
        @forelse ($transaksiTerbaru as $trx)
          <li class="flex justify-between">
            <div>
              <div class="text-sm font-medium">
                {{ $trx['jenis'] }} - {{ $trx['akun'] }}
              </div>
              <div class="text-xs text-gray-500">
                {{ $trx['tanggal'] }} • Rp {{ number_format($trx['total'], 0, ',', '.') }}
              </div>
            </div>
            <div class="text-sm text-gray-700">
              {{ ucfirst($trx['status']) }}
            </div>
          </li>
        @empty
          <li class="text-sm text-gray-500">Belum ada transaksi hari ini</li>
        @endforelse
      </ul>

      <a href="#riwayat-transaksi"
         class="mt-4 block text-sm text-blue-700 hover:underline">
        Lihat semua transaksi hari ini
      </a>
    </div>
  </div>

  {{-- 5️⃣ STATUS STOK --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Barang Stok Habis</div>
      <div class="mt-2 text-xl font-bold text-gray-800">{{ $barangStokHabis }}</div>
      @if ($daftarBarangStokHabis->count())
        <div class="mt-3">
          <ul class="text-sm text-gray-600 list-disc pl-4 space-y-1">
            @foreach ($daftarBarangStokHabis as $barang)
              <li>{{ $barang->nama_barang }}</li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500">Barang Stok Menipis</div>
      <div class="mt-2 text-xl font-bold text-gray-800">{{ $barangStokMenipis }}</div>
      @if ($daftarBarangStokMenipis->count())
        <div class="mt-3">
          <ul class="text-sm text-gray-600 list-disc pl-4 space-y-1">
            @foreach ($daftarBarangStokMenipis as $barang)
              <li>
                {{ $barang->nama_barang }}
                <span class="text-xs text-gray-500">(sisa {{ $barang->stok }})</span>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="text-sm text-gray-500 mb-2">Barang Terlaris Hari Ini</div>
      <ul class="text-sm text-gray-700 space-y-1">
        @forelse ($barangTerlarisHariIni as $item)
          <li>{{ $item->barang->nama_barang }} ({{ $item->total }})</li>
        @empty
          <li class="text-gray-400">Belum ada</li>
        @endforelse
      </ul>
    </div>
  </div>

  {{-- 4️⃣ RIWAYAT TRANSAKSI --}}
  <div id="riwayat-transaksi" class="bg-white p-4 rounded-lg shadow-sm">
    <h2 class="text-sm font-medium text-gray-700 mb-4">Riwayat Transaksi Hari Ini</h2>

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
          @forelse ($riwayatTransaksiHariIni as $i => $row)
            <tr>
              <td class="px-3 py-3 text-sm text-gray-700">{{ $i + 1 }}</td>
              <td class="px-3 py-3 text-sm text-gray-700">{{ $row['tanggal'] }}</td>
              <td class="px-3 py-3 text-sm text-gray-700">{{ $row['jenis'] }}</td>
              <td class="px-3 py-3 text-sm text-gray-700">{{ $row['akun'] }}</td>
              <td class="px-3 py-3 text-sm text-gray-700">
                Rp {{ number_format($row['total'], 0, ',', '.') }}
              </td>
              <td class="px-3 py-3 text-sm text-gray-700">
                {{ ucfirst($row['status']) }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">
                Belum ada transaksi hari ini
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

{{-- CHART --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartHarian');
    if (!ctx) return;

    // Safety check kalau Chart belum ready
    if (typeof Chart === 'undefined') {
        console.error('Chart.js belum loaded!');
        return;
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Penjualan',
                    data: @json($chartPenjualan),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.15)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Pembelian',
                    data: @json($chartPembelian),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.15)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Jam'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    },
                    title: {
                        display: true,
                        text: 'Nominal (Rp)'
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
