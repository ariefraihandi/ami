<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSubsChild extends Model
{
    protected $table = 'menu_subs_childs'; // Sesuaikan dengan nama tabel yang digunakan
    protected $fillable = ['id_submenu', 'title', 'order', 'url', 'is_active'];
}
