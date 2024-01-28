<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;


class SalesController extends Controller
{
    public function showCustIndex(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::all();
    
            // Tambahkan kolom total_spent dan total_orders ke dalam data customer
            $customers->each(function ($customer) {
                // Hitung total_spent
                $totalSpent = Invoice::where('customer_uuid', $customer->uuid)->sum('total_amount');
                $customer->total_spent = $totalSpent;
    
                // Hitung total_orders
                $totalOrders = Invoice::where('customer_uuid', $customer->uuid)->count();
                $customer->total_orders = $totalOrders;
            });
    
            return response()->json(['data' => $customers]);
        }
    
        return view('Konten/Sales/custlist');
    }
    

    public function addCustomer(Request $request)
    {
        // Validasi data input jika diperlukan
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            // Tambahkan aturan validasi lainnya sesuai kebutuhan
        ]);

        // Ambil data dari formulir
        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'country' => $request->input('country'),
            'address' => $request->input('address'),
            'customer_type' => $request->input('customer_type'),
            'country_code' => $request->input('country_code'),
            // Tambahkan kolom lain sesuai kebutuhan
        ];

        // Ambil data negara dari API
        // $response = Http::get('https://restcountries.com/v3.1/all');
        // $countries = $response->json();

        // // Iterasi melalui data negara untuk mencari country_code berdasarkan name yang dipilih
        // $selectedCountry = collect($countries)->firstWhere('name.common', $data['country']);

        // // Cek apakah negara ditemukan
        // if ($selectedCountry) {
        //     // Ambil country_code
        //     $countryCode = $selectedCountry['cca2']; // Sesuaikan dengan key yang benar dari API
        //     $data['country_code'] = $countryCode;
        // } else {
        //     // Jika negara tidak ditemukan, berikan nilai default atau tindakan lainnya sesuai kebutuhan
        //     $data['country_code'] = 'default';
        // }

        // Tambahkan pelanggan baru
        $newCustomer = Customer::createCustomer($data);

        // Jika berhasil menambahkan pelanggan, beri respons JSON
        if ($newCustomer) {
            return response()->json(['success' => true, 'message' => 'Customer added successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to add customer']);
        }
    }

    public function getCustomerByUUID($uuid)
    {
        $customer = Customer::where('uuid', $uuid)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json(['data' => $customer]);
    }
    
    
    public function showInvoiceIndex()
    {
        return view('Konten/Sales/invoicelist'); 
    }

}
