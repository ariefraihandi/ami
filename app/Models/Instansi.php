<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Instansi extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'long_name',
        'alamat',
        'email',
        'wa',
        'logo',
        'kop_surat',
        'token',
        'zip_code',
        'country',
        'phone_number',
        'website',
        'description',
    ];
}
