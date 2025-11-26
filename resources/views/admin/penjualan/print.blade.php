<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $penjualan->id_penjualan }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            margin: 0;
            padding: 10px;
            width: 80mm;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mt-2 { margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; }
        hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        @media print {
            body { width: 80mm; margin: 0; padding: 5mm; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="text-center mb-2">
    <h3 style="margin:0; font-size:14px;">Koperasi Tunas Sejahtera Mandiri</h3>
    <div style="font-size:10px;">Jl. Karah Agung 45, Surabaya<br>Telp: 0812-3456-7890</div>
    <div style="font-size:10px; border-bottom:1px dashed #000; padding-bottom:4px;">
        {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<div style="font-size:10px; line-height:1.4;">
    <div>Kasir : {{ $penjualan->user->nama_lengkap ?? 'Admin' }}</div>
    <div>No. Nota : {{ $penjualan->id_penjualan }}</div>
    <div>Tanggal : {{ $penjualan->tanggal_order->format('d-m-Y H:i') }}</div>
    @if($penjualan->pelanggan || $penjualan->anggota)
    <div>Pembeli : {{ $penjualan->pelanggan?->nama_pelanggan ?? $penjualan->anggota?->nama_anggota ?? 'Umum' }}</div>
    @endif
</div>

<hr>

@foreach($penjualan->detailPenjualan as $detail)
@php
    // INI DIA TRIKNYA: Hitung harga satuan dari sub_total ÷ kuantitas
    $harga_satuan_saat_transaksi = $detail->kuantitas > 0 ? $detail->sub_total / $detail->kuantitas : 0;
@endphp
<div style="font-size:11px;">
    {{ $detail->barang->nama_barang }}
</div>
<div style="font-size:11px; display:flex; justify-content:space-between;">
    <span>{{ $detail->kuantitas }} × {{ number_format($harga_satuan_saat_transaksi, 0, ',', '.') }}</span>
    <span>Rp {{ number_format($detail->sub_total, 0, ',', '.') }}</span>
</div>
@endforeach

<hr>

<div style="font-size:12px;">
    <div style="display:flex; justify-content:space-between;" class="bold">
        <span>TOTAL BELANJA</span>
        <span>Rp {{ number_format($penjualan->total_harga_penjualan, 0, ',', '.') }}</span>
    </div>

    @if($penjualan->diskon_penjualan > 0)
    <div style="display:flex; justify-content:space-between;">
        <span>Diskon {{ $penjualan->diskon_penjualan }}%</span>
        <span>- Rp {{ number_format($penjualan->total_harga_penjualan * $penjualan->diskon_penjualan / 100, 0, ',', '.') }}</span>
    </div>
    @endif

    @if($penjualan->pengiriman && $penjualan->pengiriman->biaya_pengiriman > 0)
    <div style="display:flex; justify-content:space-between;">
        <span>Ongkir</span>
        <span>Rp {{ number_format($penjualan->pengiriman->biaya_pengiriman, 0, ',', '.') }}</span>
    </div>
    @endif

    <div style="display:flex; justify-content:space-between; font-size:14px; font-weight:bold; border-top:1px dashed #000; padding-top:4px; margin-top:4px;">
        <span>GRAND TOTAL</span>
        <span>Rp {{ number_format($penjualan->total_harga_penjualan + ($penjualan->pengiriman?->biaya_pengiriman ?? 0), 0, ',', '.') }}</span>
    </div>
</div>

@if($penjualan->jenis_pembayaran === 'tunai')
<div style="font-size:11px; margin-top:8px;">
    <div style="display:flex; justify-content:space-between;">
        <span>Dibayar</span>
        <span>Rp {{ number_format($penjualan->uang_diterima ?? 0, 0, ',', '.') }}</span>
    </div>
    <div style="display:flex; justify-content:space-between;">
        <span>Kembali</span>
        <span>Rp {{ number_format(($penjualan->uang_diterima ?? 0) - ($penjualan->total_harga_penjualan + ($penjualan->pengiriman?->biaya_pengiriman ?? 0)), 0, ',', '.') }}</span>
    </div>
</div>
@endif

<hr>

<div class="text-center" style="font-size:10px; margin-top:8px;">
    *** TERIMA KASIH ***<br>
    Barang yang sudah dibeli<br>
    tidak dapat ditukar/dikembalikan<br>
    {{ now()->format('d-m-Y H:i') }}
</div>

</body>
</html>