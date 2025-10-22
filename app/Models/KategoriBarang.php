<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    use HasFactory;

    protected $table = 'kategori_barang';
    protected $primaryKey = 'id_kategori_barang';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kategori_barang',
        'nama_kategori',
    ];
}
