<?php

namespace App\Models; // Update the namespace to App\Models

use Illuminate\Database\Eloquent\Model;

class MenuSub extends Model
{
    protected $fillable = ['menu_id', 'title', 'order', 'url', 'icon', 'itemsub', 'is_active'];

    // Sesuaikan jika Anda ingin menambahkan relasi atau metode lain
}
