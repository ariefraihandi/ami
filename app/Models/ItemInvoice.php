<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'kode_barang',
        'barang',
        'deskripsi',
        'ukuran',        
        'qty',
        'harga_satuan',
        'tax',
        'discount',
        'ukurana',
        'ukuranb',
        'bulata',
        'bulatb',
        'sales',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function items()
    {
        return $this->hasMany(ItemInvoice::class, 'invoice_id', 'invoice_id');
    }
    
    
}

