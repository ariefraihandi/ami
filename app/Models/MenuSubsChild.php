<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuSubsChild extends Model
{
    protected $fillable = ['id_submenu', 'title', 'order', 'url', 'is_active'];

    // Sesuaikan jika Anda ingin menambahkan relasi atau metode lain
}
