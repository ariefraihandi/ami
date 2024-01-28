<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessSubChild extends Model
{
    protected $fillable = ['role_id', 'childsubmenu_id'];

    // Sesuaikan jika Anda ingin menambahkan relasi atau metode lain
}
