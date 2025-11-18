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
        'id_anggota',
        'id_user', 
        'sumber_transaksi',
        'tanggal_order',
        'tanggal_selesai',
        'diskon_penjualan',
        'total_harga_penjualan',
        'jenis_pembayaran',
        'uang_diterima',
        'catatan',
    ];

    protected $casts = [
        'tanggal_order' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'id_penjualan', 'id_penjualan');
    }
}
