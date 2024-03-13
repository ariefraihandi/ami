<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getInv($startDate, $endDate)
    {
        return self::whereBetween('created_at', [$startDate, $endDate])
                ->where('total_amount', '!=', 0.00)
                ->get(); 
    }
    
    public static function getInvPaid($startDate, $endDate)
    {
        $result = self::whereBetween('created_at', [$startDate, $endDate])
                ->where('total_amount', '!=', 0.00)
                ->where('panjar_amount', '!=', 0.00)
                ->selectRaw('SUM(CASE WHEN status = 2 THEN total_amount ELSE 0 END) AS total_paid,
                              SUM(CASE WHEN status = 1 THEN panjar_amount ELSE 0 END) AS total_panjar')
                ->first(); 
    
        return $result->total_paid + $result->total_panjar;
    }
    

    public static function getBon($startDate, $endDate)
    {
        return self::whereBetween('created_at', [$startDate, $endDate])
                    ->where('total_amount', '!=', 0.00)
                    ->whereColumn('total_amount', '>', 'panjar_amount')
                    ->sum(DB::raw('total_amount - panjar_amount'));
    }

    
    public static function getInvBB($startDate, $endDate)
    {
        return self::whereBetween('created_at', [$startDate, $endDate])
                ->where('total_amount', '!=', 0.00)
                ->where('panjar_amount', 0.00)
                ->get(); 
    }
    
    public static function getInvPJ($startDate, $endDate)
    {
        return self::whereBetween('created_at', [$startDate, $endDate])
            ->where('panjar_amount', '!=', 0.00)
            ->where('total_amount', '>', DB::raw('panjar_amount'))
            ->get(); 
    }
    
    public static function getInvLN($startDate, $endDate)
    {
        return self::whereBetween('created_at', [$startDate, $endDate])
        ->where('total_amount', '<=', DB::raw('panjar_amount'))
        ->where('panjar_amount', '!=', 0.00)
            ->get(); 
    }
    
    
    //Count
        public static function getCountInvLun($startDate, $endDate)
        {
            return self::whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('status', [2])
                    ->whereColumn('total_amount', '<=', 'panjar_amount')
                    ->count();
        }
        
        public static function getCountInvPan($startDate, $endDate)
        {
            return self::whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('status', [1])
                    ->whereColumn('total_amount', '>', 'panjar_amount')
                    ->count();
        }
        
        public static function getCountInvBon($startDate, $endDate)
        {
            return self::whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('status', [0])
                    ->where('total_amount', '!=', 0.00)
                    ->where('panjar_amount', '=', 0.00)
                    ->count();
        }
    //!Count
}
