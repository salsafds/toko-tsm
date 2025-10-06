<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    use HasFactory;

    protected $table = 'kota';
    protected $primaryKey = 'id_kota';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kota',
        'nama_kota',
        'id_negara',
        'id_provinsi',
    ];

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'id_negara', 'id_negara');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'id_provinsi', 'id_provinsi');
    }
}
