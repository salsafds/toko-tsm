<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'id_user';
    public $incrementing = false; // karena bukan auto increment
    protected $keyType = 'string';

    protected $fillable = [
        'id_user',
        'nama_lengkap',
        'jabatan_karyawan',
        'alamat_user',
        'telepon',
        'username',
        'password',
        'foto_user',
        'jenis_kelamin',
        'status',
        'tanggal_masuk',
        'tanggal_keluar',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}
