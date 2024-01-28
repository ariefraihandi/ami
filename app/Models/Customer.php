<?php

namespace App\Models;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [ 'uuid', 'name', 'email', 'phone', 'country', 'address', 'customer_type', 'country_code'];

    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($model) {
            $model->uuid = Str::random(5);
        });
    }
    public static function createCustomer($data)
    {
        return self::create($data);
    }

    public static function countCustomerTypes()
    {
        return self::groupBy('customer_type')
            ->selectRaw('customer_type, count(*) as total')
            ->pluck('total', 'customer_type');
    }

    public static function countIndividualCustomers()
    {
        return self::where('customer_type', 'individual')->count();
    }

    public static function countBiroCustomers()
    {
        return self::where('customer_type', 'biro')->count();
    }

    public static function countInstansiCustomers()
    {
        return self::where('customer_type', 'instansi')->count();
    }

    public static function todayIndividual($invoiceType)
    {
        // Hitung jumlah invoice hari ini dengan jenis tertentu
        $currentInvoiceCount = self::whereDate('created_at', now())->where('customer_type', $invoiceType)->count();

        return $currentInvoiceCount;
    }

    public static function individualPecentage($invoiceType)
    {
        // Hitung jumlah invoice hari ini dengan jenis tertentu
        $currentInvoiceCount = self::whereDate('created_at', now())->where('customer_type', $invoiceType)->count();

        // Hitung jumlah invoice kemarin dengan jenis tertentu
        $previousInvoiceCount = self::whereDate('created_at', now()->subDay())->where('customer_type', $invoiceType)->count();

        // Hindari pembagian oleh nol
        if ($previousInvoiceCount == 0) {
            return 0; // Tidak ada persentase peningkatan jika tidak ada data sebelumnya
        }

        // Hitung persentase peningkatan
        $percentageIncrease = (($currentInvoiceCount - $previousInvoiceCount) / $previousInvoiceCount) * 100;

        return $percentageIncrease;
    }

    // Metode untuk mengambil semua data pelanggan
    public static function getAllCustomers()
    {
        return self::all();
    }

    // Metode untuk mengambil data pelanggan berdasarkan ID
    public static function getCustomerById($customerId)
    {
        return self::find($customerId);
    }

    // Metode untuk memperbarui data pelanggan berdasarkan ID
    public static function updateCustomer($customerId, $data)
    {
        $customer = self::find($customerId);
        $customer->update($data);
        return $customer;
    }

    // Metode untuk menghapus data pelanggan berdasarkan ID
    public static function deleteCustomer($customerId)
    {
        return self::destroy($customerId);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_uuid', 'uuid');
    }

    // app/Models/Customer.php

    public static function countInactiveCustomer()
    {
        return self::whereDoesntHave('invoices', function ($query) {
            $lastMonth = now()->subMonth();
            $query->where('created_at', '>=', $lastMonth);
        })->count();
    }

}