<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

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
        'sku',
        'id_supplier',
        'nama_barang',
        'id_satuan',
        'merk_barang',
        'berat',
        'harga_beli',
        'stok',
        'retail',
        'margin',
    ];

    public function kategoriBarang() { return $this->belongsTo(KategoriBarang::class, 'id_kategori_barang'); }
    public function supplier() { return $this->belongsTo(Supplier::class, 'id_supplier'); }
    public function satuan() { return $this->belongsTo(Satuan::class, 'id_satuan'); }
    public function getStokTersediaAttribute()
    {
        $hold = DB::table('detail_penjualan')
            ->join('penjualan', 'detail_penjualan.id_penjualan', '=', 'penjualan.id_penjualan')
            ->where('detail_penjualan.id_barang', $this->id_barang)
            ->whereNull('penjualan.tanggal_selesai')
            ->sum('detail_penjualan.kuantitas');

        return max(0, $this->stok - (int)$hold);
    }
}