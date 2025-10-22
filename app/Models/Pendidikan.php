<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikan';
    protected $primaryKey = 'id_pendidikan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pendidikan',
        'tingkat_pendidikan',
    ];
}
