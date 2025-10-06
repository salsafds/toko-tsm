<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikan';
    protected $primaryKey = 'id_barang';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_barang',
        'tingkat_pendidikan',
    ];
}
