<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Negara extends Model
{
    use HasFactory;

    protected $table = 'negara';
    protected $primaryKey = 'id_negara';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_negara',
        'nama_negara',
    ];
}
