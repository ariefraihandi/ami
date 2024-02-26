<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'id_tagih', 'nama_tagihan', 'jenis_tagihan', 'jumlah_tagihan', 'start_tagihan', 'masa_kerja', 'tagihan_ke', 'sampai_ke', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}

