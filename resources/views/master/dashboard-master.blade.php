@extends('layouts.appmaster')

@section('title', 'Dashboard Master')

@section('content')
<div class="container mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">
                Ringkasan aktivitas & statistik koperasi — {{ $periode }}
            </p>
        </div>
    </div>

    {{-- ================= SUMMARY ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Total Transaksi</div>
            <div class="mt-2 text-2xl font-bold text-gray-800">
                {{ $summary['transaksi'] }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Total Penjualan</div>
            <div class="mt-2 text-2xl font-bold text-gray-800">
                {{ $summary['penjualan'] }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Total Pembelian</div>
            <div class="mt-2 text-2xl font-bold text-gray-800">
                {{ $summary['pembelian'] }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Barang Terjual</div>
            <div class="mt-2 text-2xl font-bold text-gray-800">
                {{ number_format($summary['barang_terjual']) }}
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Pendapatan (Rp)</div>
            <div class="mt-2 text-2xl font-bold text-gray-800">
                Rp {{ number_format($summary['pendapatan'],0,',','.') }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Pengeluaran (Rp)</div>
            <div class="mt-2 text-2xl font-bold text-gray-800">
                Rp {{ number_format($summary['pengeluaran'],0,',','.') }}
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="text-sm text-gray-500">Keuntungan (Rp)</div>
            <div class="mt-2 text-2xl font-bold
                {{ $summary['keuntungan'] >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                Rp {{ number_format($summary['keuntungan'],0,',','.') }}
            </div>
        </div>

    </div>

    {{-- ================= CHART ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- LINE CHART --}}
        <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-3">
                Penjualan & Pembelian Harian
            </h2>
            <div class="h-64">
                <canvas id="chartLine"></canvas>
            </div>
        </div>

        {{-- CUPLIKAN TRANSAKSI --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-3">
                Transaksi Terbaru Bulan Ini
            </h2>

            <ul class="space-y-3">
                @forelse($transaksiBulanan->take(5) as $trx)
                    <li class="flex justify-between">
                        <div>
                            <div class="text-sm font-medium">
                                {{ $trx['jenis'] }} - {{ $trx['akun'] }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $trx['tanggal'] }} • 
                                Rp {{ number_format($trx['total'],0,',','.') }}
                            </div>
                        </div>
                        <div class="text-sm text-gray-700">
                            {{ ucfirst($trx['status']) }}
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">
                        Belum ada transaksi bulan ini
                    </li>
                @endforelse
            </ul>

            <a href="#riwayat-transaksi"
              class="mt-4 block text-sm text-blue-700 hover:underline">
                Lihat semua transaksi bulan ini
            </a>
        </div>
    </div>

    {{-- ================= TOP DATA (1 BARIS) ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- TOP BARANG --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-3">
                Top 5 Barang Terlaris
            </h2>
            <ul class="space-y-2 text-sm">
                @forelse($top['barang'] as $b)
                    <li class="flex justify-between">
                        <span class="truncate">{{ $b->barang->nama_barang }}</span>
                        <span class="font-semibold">{{ $b->total }}</span>
                    </li>
                @empty
                    <li class="text-gray-500">Tidak ada data</li>
                @endforelse
            </ul>
        </div>

        {{-- TOP SUPPLIER --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-3">
                Top Supplier
            </h2>
            <ul class="space-y-2 text-sm">
                @forelse($top['supplier'] as $s)
    <li class="flex justify-between">
        <span class="truncate">{{ $s->supplier->nama_supplier }}</span>
        <span class="font-semibold">
            {{ number_format($s->jumlah_transaksi, 0, ',', '.') }}
        </span>
    </li>
                @empty
                    <li class="text-gray-500">Tidak ada data</li>
                @endforelse
            </ul>
        </div>

        {{-- TOP PELANGGAN --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-3">
                Top Pelanggan
            </h2>
            <ul class="space-y-2 text-sm">
                @forelse($top['pelanggan'] as $p)
                    <li class="flex justify-between">
                        <span class="truncate">{{ $p->pelanggan->nama_pelanggan }}</span>
                        <span class="font-semibold">
                            {{ number_format($p->total,0,',','.') }}
                        </span>
                    </li>
                @empty
                    <li class="text-gray-500">Tidak ada data</li>
                @endforelse
            </ul>
        </div>

        {{-- TOP ANGGOTA --}}
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h2 class="text-sm font-medium text-gray-700 mb-3">
                Top Anggota
            </h2>
            <ul class="space-y-2 text-sm">
                @forelse($top['anggota'] as $a)
                    <li class="flex justify-between">
                        <span class="truncate">{{ $a->anggota->nama_anggota }}</span>
                        <span class="font-semibold">
                            {{ number_format($a->total,0,',','.') }}
                        </span>
                    </li>
                @empty
                    <li class="text-gray-500">Tidak ada data</li>
                @endforelse
            </ul>
        </div>

    </div>

    {{-- ================= RIWAYAT TRANSAKSI BULANAN ================= --}}
    <div id="riwayat-transaksi" class="bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-sm font-medium text-gray-700 mb-4">
            Riwayat Transaksi Bulan Ini
        </h2>

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
                    @forelse($transaksiBulanan as $i => $row)
                        <tr>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                {{ $i + 1 }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                {{ $row['tanggal'] }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                {{ $row['jenis'] }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                {{ $row['akun'] }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                Rp {{ number_format($row['total'],0,',','.') }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-700">
                                {{ ucfirst($row['status']) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-3 py-4 text-center text-sm text-gray-500">
                                Belum ada transaksi bulan ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


{{-- ================= CHART JS ================= --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartLine');
    if (!ctx) return;

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js belum loaded via Vite');
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
                            if (label) label += ': ';
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
                        text: 'Tanggal'
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
