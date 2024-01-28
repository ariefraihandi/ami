<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'kode',
        'deskripsi',
        'stock',
        'harga_beli',
        'harga_jual_individu',
        'harga_jual_biro',
        'harga_jual_instansi',
        'gambar',
        'status',
        'category',
    ];

    // Your model methods go here
}
