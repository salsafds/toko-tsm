<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan - {{ $penjualan->id_penjualan }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        .total { font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="font-size: 16px;">Struk Penjualan</h1>
        <p><strong>ID Penjualan:</strong> {{ $penjualan->id_penjualan }}</p>
        <p><strong>Tanggal Order:</strong> {{ $penjualan->tanggal_order->format('d-m-Y H:i') }}</p>
        @if($penjualan->tanggal_selesai)
            <p><strong>Tanggal Selesai:</strong> {{ $penjualan->tanggal_selesai->format('d-m-Y H:i') }}</p>
        @endif
    </div>

    <div class="details">
        <p><strong>Pelanggan/Anggota:</strong> {{ $penjualan->pelanggan ? $penjualan->pelanggan->nama_pelanggan : ($penjualan->anggota ? $penjualan->anggota->nama_anggota : 'N/A') }}</p>
        <p><strong>Kasir:</strong> {{ $penjualan->user->nama_user }}</p>
        <p><strong>Jenis Pembayaran:</strong> {{ ucfirst($penjualan->jenis_pembayaran) }}</p>
        @if($penjualan->diskon_penjualan > 0)
            <p><strong>Diskon:</strong> {{ $penjualan->diskon_penjualan }}%</p>
        @endif
        @if($penjualan->catatan)
            <p><strong>Catatan:</strong> {{ $penjualan->catatan }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kuantitas</th>
                <th>Harga Satuan</th>
                <th class="right">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->detailPenjualan as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->nama_barang }}</td>
                    <td>{{ $detail->kuantitas }}</td>
                    <td>Rp {{ number_format($detail->barang->retail, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="4">Total Harga</td>
                <td class="right">Rp {{ number_format($penjualan->total_harga_penjualan, 0, ',', '.') }}</td>
            </tr>
            @if($penjualan->pengiriman)
                <tr>
                    <td colspan="4">Biaya Pengiriman</td>
                    <td class="right">Rp {{ number_format($penjualan->pengiriman->biaya_pengiriman, 0, ',', '.') }}</td>
                </tr>
                <tr class="total">
                    <td colspan="4">Grand Total</td>
                    <td class="right">Rp {{ number_format($penjualan->total_harga_penjualan + $penjualan->pengiriman->biaya_pengiriman, 0, ',', '.') }}</td>
                </tr>
            @endif
        </tfoot>
    </table>

    @if($penjualan->pengiriman)
        <div class="details">
            <h3 style="font-size: 14px;">Detail Pengiriman</h3>
            <p><strong>Agen Ekspedisi:</strong> {{ $penjualan->pengiriman->agenEkspedisi->nama_ekspedisi }}</p>
            <p><strong>Nama Penerima:</strong> {{ $penjualan->pengiriman->nama_penerima }}</p>
            <p><strong>Telepon Penerima:</strong> {{ $penjualan->pengiriman->telepon_penerima }}</p>
            <p><strong>Alamat Penerima:</strong> {{ $penjualan->pengiriman->alamat_penerima }}</p>
            <p><strong>Kode Pos:</strong> {{ $penjualan->pengiriman->kode_pos }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Terima Kasih atas Kunjungan Anda!</p>
        <p>Dicetak pada: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>