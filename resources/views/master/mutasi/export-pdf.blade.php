<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Barang</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #f0f0f0; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>LAPORAN MUTASI BARANG</h2>
    <p style="text-align:center;">Periode: {{ $periodText ?? 'Semua Periode' }}</p>
    <p style="text-align:center;">Dicetak pada: {{ $generatedAt }} WIB</p>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="8%">Tanggal</th>
                <th width="18%">Barang</th>
                <th width="8%">Keterangan</th>
                <th width="7%">Stok Awal</th>
                <th width="6%">Masuk</th>
                <th width="6%">Keluar</th>
                <th width="7%">Saldo</th>
                <th width="9%">Harga Beli</th>
                <th width="10%">Total</th>
                <th width="9%">Avg Price</th>
                <th width="10%">Nilai Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $row['tanggal']->format('d/m/Y') }}</td>
                    <td>{{ $row['nama_barang'] }}</td>
                    <td class="text-center">{{ $row['keterangan'] }}</td>
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