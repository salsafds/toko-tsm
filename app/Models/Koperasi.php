<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Koperasi extends Model
{
    use HasFactory;

    protected $table = 'koperasi';
    protected $primaryKey = 'nama_koperasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_koperasi',
        'npwp',
        'alamat_koperasi',
        'telepon_koperasi',
        'email_koperasi',
        'fax_koperasi',
        'kode_pos',
        'website',
        'logo_koperasi',
        'nama_pimpinan',
    ];
}
