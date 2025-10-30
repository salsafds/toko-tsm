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
        'total_berat_bruto',
        'nama_penerima',
        'telepon_penerima',
        'alamat_penerima',
        'kode_pos',
        'catatan',
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
