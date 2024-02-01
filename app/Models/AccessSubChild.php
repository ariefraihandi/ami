<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessSubChild extends Model
{
    protected $table = 'access_sub_childs';
    protected $fillable = ['role_id', 'childsubmenu_id'];

    public function accessChildren()
    {
        return $this->hasMany(AccessChild::class, 'childsubmenu_id');
    }
}