<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['menu_name', 'order'];

    // Sesuaikan jika Anda ingin menambahkan relasi atau metode lain
}
