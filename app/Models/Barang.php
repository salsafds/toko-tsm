<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_barang',
        'id_kategori_barang',
        'id_supplier',
        'nama_barang',
        'id_satuan',
        'merk_barang',
        'berat',
        'harga_beli',
        'stok',
        'retail',
    ];

    public function kategoriBarang() { return $this->belongsTo(KategoriBarang::class, 'id_kategori_barang'); }
    public function supplier() { return $this->belongsTo(Supplier::class, 'id_supplier'); }
    public function satuan() { return $this->belongsTo(Satuan::class, 'id_satuan'); }
}
