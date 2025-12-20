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
            line-height: 1.4;
        }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        hr.dashed { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        hr.solid { border: none; border-top: 2px solid #000; margin: 10px 0; }
        .flex-between {
            display: flex;
            justify-content: space-between;
        }
        .item-name {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .item-line {
            margin-bottom: 2px;
        }
        @media print {
            body { width: 80mm; margin: 0; padding: 5mm; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="text-center">
    <h3 style="margin:0; font-size:14px; letter-spacing:0.5px;">Koperasi Tunas Sejahtera Mandiri</h3>
    <div style="font-size:10px;">
        Jl. Karah Agung 45, Surabaya<br>
        Telp: 0812-3456-7890
    </div>
    <hr class="dashed">
    <div style="font-size:10px;">
        {{ now()->format('d-m-Y H:i') }}
    </div>
</div>

<div style="font-size:10px; margin-top:8px;">
    <div class="flex-between">
        <span>Kasir</span>
        <span>: {{ $penjualan->user->nama_lengkap ?? 'Admin' }}</span>
    </div>
    <div class="flex-between">
        <span>No. Nota</span>
        <span>: {{ $penjualan->id_penjualan }}</span>
    </div>
    <div class="flex-between">
        <span>Tanggal</span>
        <span>: {{ $penjualan->tanggal_order->format('d-m-Y H:i') }}</span>
    </div>
    @if($penjualan->pelanggan || $penjualan->anggota)
    <div class="flex-between">
        <span>Pembeli</span>
        <span>: {{ $penjualan->pelanggan?->nama_pelanggan ?? $penjualan->anggota?->nama_anggota ?? 'Umum' }}</span>
    </div>
    @endif
</div>

<hr class="dashed">

{{-- Daftar Barang --}}
@foreach($penjualan->detailPenjualan as $detail)
<div class="item-line" style="font-size:11px;">
    <div class="item-name">{{ $detail->barang->nama_barang }}</div>
    <div class="flex-between">
        <span>{{ $detail->kuantitas }} x {{ number_format($detail->sub_total / $detail->kuantitas, 0, ',', '.') }}</span>
        <span>{{ number_format($detail->sub_total, 0, ',', '.') }}</span>
    </div>
</div>
@endforeach

<hr class="dashed">

{{-- Perhitungan Total --}}
<div style="font-size:11px;">

    @php
        $subtotalSebelumPPN = $penjualan->total_dpp + $penjualan->total_non_ppn;
        $nilaiDiskon = ($penjualan->total_dpp + $penjualan->total_non_ppn + 
                       ($penjualan->pengiriman?->biaya_pengiriman ?? 0)) * 
                       $penjualan->diskon_penjualan / 100;
    @endphp

    {{-- Sub Total --}}
    <div class="flex-between">
        <span>Sub Total</span>
        <span>: {{ number_format($subtotalSebelumPPN, 0, ',', '.') }}</span>
    </div>

    {{-- TOTAL HEMAT --}}
    @if($penjualan->diskon_penjualan > 0)
    <div class="flex-between bold">
        <span>TOTAL HEMAT ----></span>
        <span>- {{ number_format($nilaiDiskon, 0, ',', '.') }}</span>
    </div>
    @endif

    {{-- Biaya Kirim --}}
    @if($penjualan->pengiriman && $penjualan->pengiriman->biaya_pengiriman > 0)
    <div class="flex-between">
        <span>Biaya Kirim</span>
        <span>: {{ number_format($penjualan->pengiriman->biaya_pengiriman, 0, ',', '.') }}</span>
    </div>
    @endif

    {{-- PPN --}}
    @if($penjualan->tarif_ppn > 0)
    <div class="flex-between">
        <span>PPN ({{ number_format($penjualan->tarif_ppn, 2) }}%)</span>
        <span>: {{ number_format($penjualan->total_ppn, 0, ',', '.') }}</span>
    </div>
    @endif

    <hr class="solid">

    {{-- Total Item & Grand Total --}}
    <div class="flex-between bold" style="font-size:14px;">
        <span>Total Item : {{ $penjualan->detailPenjualan->count() }}</span>
        <span>Rp {{ number_format($penjualan->total_harga_penjualan, 0, ',', '.') }}</span>
    </div>
</div>

{{-- Pembayaran --}}
@if($penjualan->jenis_pembayaran === 'tunai')
<div style="font-size:11px; margin-top:8px;">
    <hr class="dashed">
    <div class="flex-between">
        <span>Pembayaran</span>
        <span>: TUNAI</span>
    </div>
    <div class="flex-between">
        <span>Uang Diterima</span>
        <span>: {{ number_format($penjualan->uang_diterima ?? 0, 0, ',', '.') }}</span>
    </div>
    <div class="flex-between bold">
        <span>Kembalian</span>
        <span>: {{ number_format(($penjualan->uang_diterima ?? 0) - $penjualan->total_harga_penjualan, 0, ',', '.') }}</span>
    </div>
</div>
@else
<div style="font-size:11px; margin-top:8px;">
    <hr class="dashed">
    <div class="flex-between">
        <span>Pembayaran</span>
        <span>: KREDIT</span>
    </div>
</div>
@endif

<hr class="dashed">

<div class="text-center" style="font-size:10px; margin-top:10px;">
    *** TERIMA KASIH ATAS KUNJUNGAN ANDA ***<br>
    Barang yang sudah dibeli<br>
    tidak dapat ditukar atau dikembalikan<br><br>
    {{ now()->format('d-m-Y H:i') }}
</div>

</body>
</html>