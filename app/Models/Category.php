<?php

// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];
    
    // Jika Anda ingin menonaktifkan timestamp (created_at, updated_at)
    // public $timestamps = false;
}
