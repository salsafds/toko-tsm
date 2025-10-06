<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahasa extends Model
{
    use HasFactory;

    protected $table = 'bahasa';
    protected $primaryKey = 'id_bahasa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_bahasa',
        'nama_bahasa',
    ];
}
