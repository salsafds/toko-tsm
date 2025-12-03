<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Barang - {{ $periodeTeks }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f0f0f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1 style="text-align:center">DAFTAR BARANG SAAT INI</h1>
    <h2 style="text-align:center">{{ $periodeTeks }}</h2>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th class="text-center">Margin</th>
                <th class="text-right">Harga Beli</th>
                <th class="text-center">Stok</th>
                <th class="text-right">Harga Jual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daftarBarang as $b)
            <tr {{ $b->stok < 10 ? 'style="background:#ffebee"' : '' }}>
                <td>{{ $b->id_barang }}</td>
                <td>{{ $b->nama_barang }}</td>
                <td class="text-center">{{ $b->margin }}%</td>
                <td class="text-right">Rp {{ number_format($b->harga_beli,0,',','.') }}</td>
                <td class="text-center {{ $b->stok < 10 ? 'font-weight:bold;color:red' : '' }}">{{ $b->stok }}</td>
                <td class="text-right">Rp {{ number_format($b->retail,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>