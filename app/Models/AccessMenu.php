<?php

namespace App\Models; // Update the namespace to App\Models

use Illuminate\Database\Eloquent\Model;

class AccessMenu extends Model
{
    protected $fillable = ['user_id', 'menu_id'];

    public function accessMenus()
    {
        return $this->hasMany(AccessMenu::class, 'menu_id');
    }
}
