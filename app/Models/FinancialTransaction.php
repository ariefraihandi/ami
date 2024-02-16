<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;
    
    protected $table = 'financial_transactions';

    protected $fillable = [
        'transaction_date',
        'source_receiver',
        'description',
        'transaction_amount',
        'payment_method',
        'reference_number',
        'status',
        'lunas',
    ];
}
