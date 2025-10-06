<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasFactory;

    protected $table = 'provinsi';
    protected $primaryKey = 'id_provinsi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_provinsi',
        'nama_provinsi',
        'id_negara',
    ];

    public function negara()
    {
        return $this->belongsTo(Negara::class, 'id_negara', 'id_negara');
    }
}
