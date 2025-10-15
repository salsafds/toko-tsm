<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPelanggan extends Model
{
    use HasFactory;

    protected $table = 'kategori_pelanggan';
    protected $primaryKey = 'id_kategori_pelanggan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kategori_pelanggan',
        'nama_kategori_pelanggan',
    ];
}
