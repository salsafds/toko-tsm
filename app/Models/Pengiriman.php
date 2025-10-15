<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';
    protected $primaryKey = 'id_pengiriman';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengiriman',
        'id_agen_ekspedisi',
        'id_penjualan',
        'nomor_resi',
        'biaya_pengiriman',
        'status_pembayaran',
        'diskon_biaya_kirim',
        'total_berat_bruto',
    ];

    public function agenEkspedisi()
    {
        return $this->belongsTo(AgenEkspedisi::class, 'id_agen_ekspedisi', 'id_ekspedisi');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan', 'id_penjualan');
    }
}
