<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan {{ $periodeTeks }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        h1, h2, h3 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-red { background-color: #ffebee; color: red; }
    </style>
</head>
<body>

    <h1>LAPORAN BULANAN TOKO KOPERASI</h1>
    <h2>Periode: {{ $periodeTeks }}</h2>
    <p style="text-align:center;">Dicetak pada: {{ date('d F Y H:i') }}</p>

    <table>
        <tr>
            <th>Omzet Penjualan</th>
            <th>Laba Kotor</th>
            <th>Nilai Stok Akhir</th>
            <th>Stok Kritis</th>
        </tr>
        <tr style="font-size:12pt; font-weight:bold;">
            <td class="text-right">Rp {{ number_format($omzet,0,',','.') }}</td>
            <td class="text-right">Rp {{ number_format($labaKotor,0,',','.') }} ({{ round($marginPersen, 1) }}%)</td>
            <td class="text-right">Rp {{ number_format($nilaiStokAkhir,0,',','.') }}</td>
            <td class="text-center">{{ $stokKritis->count() }} barang</td>
        </tr>
    </table>

    <h3>RINGKASAN KEUANGAN</h3>
    <table>
        <tr>
            <th colspan="2">RINGKASAN PEMBELIAN</th>
            <th colspan="2">HPP & LABA KOTOR</th>
        </tr>
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

    <h3>10 BARANG TERLARIS</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Barang</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Omzet</th>
            </tr>
        </thead>
        <tbody>
            @foreach($terlaris as $i => $item)
            <tr>
                <td class="text-center font-bold">{{ $i+1 }}</td>
                <td>{{ $item->nama_barang }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->omzet,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($stokKritis->count() > 0)
    <h3>STOK KRITIS</h3>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th class="text-center">Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stokKritis as $b)
            <tr class="bg-red">
                <td>{{ $b->id_barang ?? '-' }}</td>
                <td>{{ $b->nama_barang }}</td>
                <td class="text-center font-bold">{{ $b->stok }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h3>RIWAYAT TRANSAKSI</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>ID</th>
                <th>Jenis</th>
                <th>Pihak</th>
                <th>Kasir</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $t)
            <tr>
                <td>{{ date('d/m/Y', strtotime($t->tanggal)) }}</td>
                <td>{{ $t->id }}</td>
                <td>{{ ucfirst($t->jenis) }}</td>
                <td>{{ $t->nama_pihak }}</td>
                <td>{{ $t->kasir }}</td>
                <td class="text-right">Rp {{ number_format($t->total,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>