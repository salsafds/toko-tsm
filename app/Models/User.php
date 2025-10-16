<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
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
        'id_role',       // foreign key ke tabel role
        'id_jabatan',    // foreign key ke tabel jabatan
        'id_pendidikan', // foreign key ke tabel pendidikan
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relasi ke tabel Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /**
     * Relasi ke tabel Jabatan
     */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    /**
     * Relasi ke tabel Pendidikan
     */
    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'id_pendidikan', 'id_pendidikan');
    }
}
