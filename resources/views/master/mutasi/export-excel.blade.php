<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Barang</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; font-size: 12px; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>LAPORAN MUTASI BARANG</h2>
    <p>Periode: {{ $periodText ?? 'Semua Periode' }}</p>
    <p>Dicetak pada: {{ $nowWIB->translatedFormat('d F Y H:i') }} WIB</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Keterangan</th>
                <th>Stok Awal</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Saldo Akhir</th>
                <th>Harga Beli</th>
                <th>Total Harga</th>
                <th>Average Price</th>
                <th>Nilai Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $row['tanggal']->format('d/m/Y') }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td>{{ $row['keterangan'] }}</td>
                    <td class="text-right">{{ number_format($row['stok_awal']) }}</td>
                    <td class="text-right">{{ number_format($row['masuk']) }}</td>
                    <td class="text-right">{{ number_format($row['keluar']) }}</td>
                    <td class="text-right">{{ number_format($row['saldo_akhir']) }}</td>
                    <td class="text-right">{{ number_format($row['harga_beli'], 0) }}</td>
                    <td class="text-right">{{ number_format($row['total_harga']) }}</td>
                    <td class="text-right">{{ number_format($row['average_price'], 0) }}</td>
                    <td class="text-right">{{ number_format($row['nilai_stok']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>