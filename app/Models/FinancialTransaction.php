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

    public static function getTransactionAmount($date)
    {
        return self::whereDate('transaction_date', $date)
                   ->whereIn('status', [1, 2, 3])
                   ->sum('transaction_amount');
    }
    
    public static function getOutTransAmount($date)
    {
        return self::whereDate('transaction_date', $date)
                   ->whereIn('status', [4, 5])
                   ->sum('transaction_amount');
    }
    
    public static function getDayliSetorKasAmount($date)
    {
        return self::whereDate('transaction_date', $date)
                   ->whereIn('status', [6])
                   ->sum('transaction_amount');
    }
    
    public static function getDayliTopUpAmount($date)
    {
        return self::whereDate('transaction_date', $date)
                   ->whereIn('status', [7])
                   ->sum('transaction_amount');
    }
    
    public static function getWeeklyTransactionAmount($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                   ->whereIn('status', [1, 2, 3])
                   ->sum('transaction_amount');
    }   
    
    public static function getWeeklySetorKasAmount($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                   ->whereIn('status', [6])
                   ->sum('transaction_amount');
    }   
    
    public static function getWeeklyTopUpAmount($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                   ->whereIn('status', [7])
                   ->sum('transaction_amount');
    }   
    
    public static function getWeeklyOutTransonAmount($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                   ->whereIn('status', [4, 5])
                   ->sum('transaction_amount');
    }   
}
