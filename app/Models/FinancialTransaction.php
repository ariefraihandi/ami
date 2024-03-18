<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'reference_number', 'invoice_number');
    }

    public static function getIncomeForReport($startDate, $endDate)
    {
        return self::join('invoices', 'financial_transactions.reference_number', '=', 'invoices.invoice_number')
            ->whereBetween('financial_transactions.transaction_date', [$startDate, $endDate])
            ->whereIn('financial_transactions.status', [1, 2, 3])
            ->select('financial_transactions.*', 'invoices.customer_uuid', 'invoices.invoice_name', 'invoices.type', 'invoices.status', 'invoices.total_amount', 'invoices.panjar_amount', 'invoices.due_date')
            ->get();
    }
    
    
//Sum
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
    
    public static function getIncomeRangeAmount($startDate, $endDate)
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
    
    public static function getRangeOutTransonAmount($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                   ->whereIn('status', [4, 5])
                   ->sum('transaction_amount');
    }  
   
    public static function getMarginByRange($startDate, $endDate)
    {
        // Menghitung jumlah pendapatan dari transaksi dengan status 1, 2, atau 3
        $income = self::whereBetween('transaction_date', [$startDate, $endDate])
                    ->whereIn('status', [1, 2, 3])
                    ->sum('transaction_amount');

        // Menghitung jumlah biaya dari transaksi dengan status 4 atau 5
        $expenses = self::whereBetween('transaction_date', [$startDate, $endDate])
                        ->whereIn('status', [4, 5])
                        ->sum('transaction_amount');

        // Menghitung margin
        $margin = $income - $expenses;

        return $margin;
    }
//!Sum 

    public static function getIncomeByRange($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                ->whereIn('status', [1, 2, 3])
                ->get();
    }
  
    public static function getOutcomeByRange($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                ->whereIn('status', [4, 5])
                ->get();
    }

    public static function getSetor($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                ->whereIn('status', [6])
                ->get(); 
    }
    public static function getTopup($startDate, $endDate)
    {
        return self::whereBetween('transaction_date', [$startDate, $endDate])
                ->whereIn('status', [7])
                ->get(); 
    }
}
