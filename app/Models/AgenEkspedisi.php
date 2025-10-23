<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgenEkspedisi extends Model
{
    use HasFactory;

    protected $table = 'agen_ekspedisi';
    protected $primaryKey = 'id_ekspedisi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_ekspedisi',
        'nama_ekspedisi',
        'id_negara',
        'id_provinsi',
        'id_kota',
        'telepon_ekspedisi',
        'email_ekspedisi',
    ];

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'id_negara', 'id_negara');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }

    public function kota()
    {
        return $this->belongsTo(Kota::class, 'id_kota', 'id_kota');
    }
}
