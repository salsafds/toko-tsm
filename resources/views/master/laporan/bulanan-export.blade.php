<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan {{ $periodeTeks }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; margin: 30px; }
        h1, h2, h3 { text-align: center; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-red { background: #ffebee; }
    </style>
</head>
<body>

    <h1>LAPORAN BULANAN TOKO KOPERASI</h1>
    <h2>Periode: {{ $periodeTeks }}</h2>
    <p style="text-align:center;">Dicetak pada: {{ now()->format('d F Y H:i') }}</p>

    <!-- 4 Kartu Utama -->
    <table>
        <tr>
            <th>Omzet Penjualan</th>
            <th>Laba Kotor</th>
            <th>Nilai Stok Akhir</th>
            <th>Stok Kritis</th>
        </tr>
        <tr style="font-size:13pt; font-weight:bold;">
            <td class="text-right">Rp {{ number_format($omzet,0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($labaKotor,0,',','.') }} ({{ $marginPersen }}%)</td>
            <td class="text-right">Rp {{ number_format($nilaiStokAkhir,0,',','.') }}</td>
            <td class="text-center">{{ $stokKritis->count() }} barang</td>
        </tr>
    </table>

    <!-- Ringkasan -->
    <table>
        <tr><th colspan="2">RINGKASAN PEMBELIAN</th><th colspan="2">HPP & LABA KOTOR</th></tr>
        <tr>
            <td>Total Nota Pembelian</td>
            <td class="text-right font-bold">Rp {{ number_format($totalPembelian,0,',','.') }}</td>
            <td>HPP</td>
            <td class="text-right font-bold">Rp {{ number_format($hpp,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Barang Masuk Stok</td>
            <td class="text-right font-bold">Rp {{ number_format($pembelianMasuk,0,',','.') }}</td>
            <td>Laba Kotor</td>
            <td class="text-right font-bold">Rp {{ number_format($labaKotor,0,',','.') }}</td>
        </tr>
    </table>

    <!-- 10 Terlaris -->
    <h3>10 BARANG TERLARIS</h3>
    <table>
        <tr><th>#</th><th>Nama Barang</th><th class="text-right">Qty</th><th class="text-right">Omzet</th></tr>
        @foreach($terlaris as $i => $item)
        <tr>
            <td class="text-center font-bold">{{ $i+1 }}</td>
            <td>{{ $item->nama_barang }}</td>
            <td class="text-right">{{ $item->qty }}</td>
            <td class="text-right">Rp {{ number_format($item->omzet,0,',','.') }}</td>
        </tr>
        @endforeach
    </table>

    <!-- Stok Kritis -->
    @if($stokKritis->count() > 0)
    <h3>STOK KRITIS</h3>
    <table>
        <tr><th>Kode</th><th>Nama Barang</th><th class="text-center">Stok</th></tr>
        @foreach($stokKritis as $b)
        <tr class="bg-red">
            <td>{{ $b->id_barang }}</td>
            <td>{{ $b->nama_barang }}</td>
            <td class="text-center font-bold">{{ $b->stok }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- Riwayat Transaksi -->
    <h3>RIWAYAT TRANSAKSI</h3>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>ID</th>
            <th>Jenis</th>
            <th>Pihak</th>
            <th>Kasir</th>
            <th class="text-right">Jumlah</th>
        </tr>
        @foreach($transaksi as $t)
        <tr>
            <td>{{ $t->tanggal->format('d/m/Y') }}</td>
            <td>{{ $t->id }}</td>
            <td>{{ $t->jenis == 'penjualan' ? 'Jual' : 'Beli' }}</td>
            <td>{{ $t->nama_pihak }}</td>
            <td>{{ $t->kasir }}</td>
            <td class="text-right">Rp {{ number_format($t->total,0,',','.') }}</td>
        </tr>
        @endforeach
    </table>

</body>
</html>