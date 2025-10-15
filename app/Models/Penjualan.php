<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_penjualan',
        'id_pelanggan',
        'tanggal_penjualan',
        'diskon_penjualan',
        'total_harga_penjualan',
        'jenis_pembayaran',
    ];

    public function pelanggan() { return $this->belongsTo(Pelanggan::class, 'id_pelanggan'); }
}