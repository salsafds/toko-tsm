<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

    protected $table = 'detail_pembelian';
    protected $primaryKey = 'id_detail_pembelian';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detail_pembelian',
        'id_pembelian',
        'id_barang',
        'harga_beli', // Tambah
        'kuantitas',
        'sub_total',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    // Otomatis hitung sub_total setiap kali menyimpan data
    protected static function booted()
    {
        static::saving(function ($detail) {
            $detail->sub_total = $detail->kuantitas * $detail->harga_beli;
        });
    }

    // Relasi ke tabel pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    // Relasi ke tabel barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}