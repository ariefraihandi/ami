<?php

namespace App\Models; // Update the namespace to App\Models

use Illuminate\Database\Eloquent\Model;

class AccessSub extends Model
{
    protected $fillable = ['role_id', 'submenu_id'];

    // Sesuaikan jika Anda ingin menambahkan relasi atau metode lain
}
