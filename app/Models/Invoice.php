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

}
