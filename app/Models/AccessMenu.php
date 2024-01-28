<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessMenu extends Model
{
    protected $fillable = ['user_id', 'menu_id'];

    // Sesuaikan jika Anda ingin menambahkan relasi atau metode lain
}
