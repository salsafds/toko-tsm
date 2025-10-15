<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $primaryKey = 'id_supplier';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_supplier',
        'nama_supplier',
        'alamat',
        'id_negara',
        'id_provinsi',
        'id_kota',
        'telepon_supplier',
        'email_supplier',
    ];

    public function negara() { return $this->belongsTo(Negara::class, 'id_negara'); }
    public function provinsi() { return $this->belongsTo(Provinsi::class, 'id_provinsi'); }
    public function kota() { return $this->belongsTo(Kota::class, 'id_kota'); }
}
