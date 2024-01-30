<?php

namespace App\Models; // Update the namespace to App\Models

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['menu_name', 'order', 'status'];
    
}
