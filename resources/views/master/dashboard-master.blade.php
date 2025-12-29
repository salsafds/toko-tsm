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

    {{-- SUMMARY --}}
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- LINE CHART --}}
        <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow-sm relative">
            <div class="flex justify-between items-start mb-3">
                <h2 class="text-sm font-medium text-gray-700" id="chartTitle">
                    Penjualan & Pembelian Harian
                </h2>
                <a href="#" id="backToMonthly" class="text-xs text-blue-600 hover:underline hidden">
                    ← Kembali ke data bulanan
                </a>
            </div>
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

    {{-- TOP DATA --}}
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

    {{-- RIWAYAT TRANSAKSI BULANAN --}}
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
                        <th class="px-3 py-2">Pembeli</th>
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


{{-- CHART --}}
@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartLine');
    const chartTitle = document.getElementById('chartTitle');
    const backLink = document.getElementById('backToMonthly');
    if (!ctx) return;

    let chart;
    const labels = @json($chartLabels);
    const dataPenjualan = @json($chartPenjualan);
    const dataPembelian = @json($chartPembelian);

    // Jika penjualan atau pembelian > 0
    const clickableDays = labels.map((_, i) => dataPenjualan[i] > 0 || dataPembelian[i] > 0);

    function renderMonthlyChart() {
        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Penjualan',
                        data: dataPenjualan,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.15)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: dataPenjualan.map(v => v > 0 ? 5 : 3),
                        pointHoverRadius: 8
                    },
                    {
                        label: 'Pembelian',
                        data: dataPembelian,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.15)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: dataPembelian.map(v => v > 0 ? 5 : 3),
                        pointHoverRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'nearest',     // penting: ambil yang terdekat dengan kursor
                    intersect: true,     // klik harus tepat di titik/garis
                    axis: 'x'
                },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.parsed.y === null || context.parsed.y === 0) return '';
                                return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: { title: { display: true, text: 'Tanggal' } },
                    y: {
                        beginAtZero: true,
                        ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') },
                        title: { display: true, text: 'Nominal (Rp)' }
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length === 0) return;

                    const clickedElement = elements[0];
                    const index = clickedElement.index;
                    const datasetIndex = clickedElement.datasetIndex;

                    // Validasi: hari harus punya transaksi
                    if (!clickableDays[index]) return;

                    // Pastikan datasetIndex valid
                    if (datasetIndex !== 0 && datasetIndex !== 1) return;

                    const date = labels[index];
                    const month = '{{ Carbon\Carbon::now()->format("m") }}';
                    const year = '{{ Carbon\Carbon::now()->format("Y") }}';
                    const fullDate = `${year}-${month.padStart(2, '0')}-${String(date).padStart(2, '0')}`;

                    // Tentukan tipe berdasarkan dataset yang diklik
                    const tipe = datasetIndex === 0 ? 'penjualan' : 'pembelian';

                    fetch(`{{ route('master.dashboard.drilldown') }}?tanggal=${fullDate}&tipe=${tipe}`)
                        .then(res => {
                            if (!res.ok) throw new Error('Network error');
                            return res.json();
                        })
                        .then(data => {
                            if (chart) chart.destroy();

                            chart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [{
                                        label: data.tipe,
                                        data: data.data,
                                        borderColor: tipe === 'penjualan' ? 'rgb(34, 197, 94)' : 'rgb(59, 130, 246)',
                                        backgroundColor: tipe === 'penjualan' 
                                            ? 'rgba(34, 197, 94, 0.15)' 
                                            : 'rgba(59, 130, 246, 0.15)',
                                        tension: 0.3,
                                        fill: true,
                                        pointRadius: 5,
                                        pointHoverRadius: 8
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { position: 'top' },
                                        tooltip: {
                                            callbacks: {
                                                label: ctx => `${data.tipe}: Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                                            }
                                        }
                                    },
                                    scales: {
                                        x: { title: { display: true, text: 'Jam' } },
                                        y: {
                                            beginAtZero: true,
                                            ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') },
                                            title: { display: true, text: 'Nominal (Rp)' }
                                        }
                                    }
                                }
                            });

                            chartTitle.textContent = data.title;
                            backLink.classList.remove('hidden');
                        })
                        .catch(err => {
                            console.error('Error drilldown:', err);
                            alert('Gagal memuat data drilldown. Cek console untuk detail.');
                        });
                }
            }
        });

        chartTitle.textContent = 'Penjualan & Pembelian Harian';
        backLink.classList.add('hidden');
    }

    renderMonthlyChart();

    backLink.addEventListener('click', e => {
        e.preventDefault();
        renderMonthlyChart();
    });
});
</script>
@endpush
@endsection
