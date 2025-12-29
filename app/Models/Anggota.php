<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;
    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_anggota',
        'username_anggota',
        'password_anggota',
        'nama_anggota',
        'jenis_kelamin',
        'alamat_anggota',
        'kota_anggota',
        'tempat_lahir',
        'tanggal_lahir',
        'departemen',
        'pekerjaan',
        'jabatan',
        'agama',
        'status_perkawinan',
        'tanggal_registrasi',
        'tanggal_keluar',
        'no_telepon',
        'status_anggota',
        'foto',
    ];

    public $timestamps = false;
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_registrasi' => 'date',
        'tanggal_keluar' => 'date',
    ];

}
