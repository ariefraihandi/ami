<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_uuid',
        'invoice_number',
        'invoice_name',
        'type',
        'status',
        'total_amount',
        'due_date',
        'additional_notes',
        'payment_method',
    ];
        
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_uuid', 'uuid');
    }
}
