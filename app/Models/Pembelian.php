<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pembelian',
        'tanggal_pembelian',
        'tanggal_terima',
        'id_supplier',
        'id_user',
        'jenis_pembayaran',
        'diskon',
        'ppn',
        'biaya_pengiriman',
        'jumlah_bayar',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'datetime',
        'tanggal_terima' => 'datetime',
        'diskon' => 'decimal:2',
        'ppn' => 'decimal:2',
        'biaya_pengiriman' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function getTotalAttribute()
    {
        return $this->detailPembelian->sum('sub_total');
    }

    public function isSelesai()
    {
        return !is_null($this->tanggal_terima);
    }
}
