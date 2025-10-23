<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pelanggan',
        'nama_pelanggan',
        'nomor_telepon',
        'kategori_pelanggan',
        'email_pelanggan',
        'id_negara',
        'id_provinsi',
        'id_kota',
    ];

    // relasi ke tabel lain
    public function negara() { return $this->belongsTo(Negara::class, 'id_negara'); }
    public function provinsi() { return $this->belongsTo(Provinsi::class, 'id_provinsi'); }
    public function kota() { return $this->belongsTo(Kota::class, 'id_kota'); }
}
