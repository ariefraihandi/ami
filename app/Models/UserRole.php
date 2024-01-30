<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'users_role';

    protected $fillable = [
        'role',
    ];

    // Timestamps tidak diperlukan jika Anda tidak memiliki kolom 'created_at' dan 'updated_at' pada tabel
    public $timestamps = true;
}
