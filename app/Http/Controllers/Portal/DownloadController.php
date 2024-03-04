<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SendInvoice;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ItemInvoice;
use Carbon\Carbon;
use PDF;

class DownloadController extends Controller
{
    public function unduhInvoice(Request $request)
    {
        // Mengambil nilai invoice_number dan token dari URL
        $invoiceNumber = $request->query('invoice_number');
        $token = $request->query('token');

        // Cari data di database berdasarkan invoice_number dan token
        $invoice = SendInvoice::where('inv_number', $invoiceNumber)
                                ->where('token', $token)
                                ->first();

        // Jika data ditemukan, Anda dapat melakukan apa yang diperlukan
        if ($invoice) {
            $invoiceData = Invoice::where('invoice_number', $invoiceNumber)->first();

            if (!$invoiceData) {
                // Handle the case where the invoice with the provided number is not found
                abort(404); // You can customize this based on your application's error handling
            }

            // Retrieve customer data based on the customer_uuid in the invoice
            $customer = Customer::where('uuid', $invoiceData->customer_uuid)->first();

            // Retrieve item data based on the invoice_id in the ItemInvoice (assuming it might be multiple items)
            $items = ItemInvoice::where('invoice_id', $invoiceNumber)->get();

            // Set locale to Indonesian
            app()->setLocale('id');

            $formattedDate = Carbon::parse($invoiceData->created_at)->isoFormat('LL'); // Format "29 Januari 2024"

            $subtotal = $items->sum(function ($item) {
                return $item->harga_satuan * $item->qty * $item->ukuran;
            });

            $discount = $items->sum(function ($item) {
                return $item->discount;
            });

            $format_subtotal = "Rp. " . number_format($subtotal, 0) . ",-";
            $format_discount = "Rp. " . number_format($discount, 0) . ",-";

            $firstItem = $items->first();

            if ($firstItem) {
                $tax = $firstItem->tax . "%";
            } else {
                // Handle the case when no item with the given invoice_id is found
                // You can set a default tax value or handle it based on your requirements
                $tax = "0%";
            }

            $panjar_amount      = $invoiceData->panjar_amount;
            $total_amount       = $invoiceData->total_amount - $invoiceData->panjar_amount;
            $format_panjar      = "Rp. " . number_format($panjar_amount, 0) . ",-";
            $format_total       = "Rp. " . number_format($total_amount, 0) . ",-";
            $background         = 'bg-report.png';
            $kopSuratImage      = public_path('assets/img/report/kop.png');     
            $link               = public_path('assets/img/report/kop.png');                        
            $logo               = public_path('assets/img/icons/brands/logo-kecil.png');                        
            $bgImage            = public_path('assets/img/report/' . $background);        

            $data = [
                'title'          => 'Unduh Invoice',
                'subtitle'       => '#' . $invoiceNumber,
                'bgImage'        => $bgImage,
                'kopSuratImage'  => $kopSuratImage,
                'logo'           => $logo,
                'logoPath'       => $link,
                'formattedDate'  => $formattedDate,
                'subtotal'       => $format_subtotal,
                'panjar_amount'  => $format_panjar,
                'discount'       => $format_discount,
                'total'          => $format_total,
                'tax'            => $tax,
                'invoice'        => $invoiceData,
                'customer'       => $customer,
                'items'          => $items,
            ];

            $pdf = PDF::loadView('Konten.Invoice.pdfInvoice', $data);
            return $pdf->stream('Invoice.pdf');  
        } else {
            // Jika data tidak ditemukan, Anda dapat memberikan respons sesuai
            return response()->json(['success' => false, 'message' => 'Invoice tidak ditemukan'], 404);
        }
    }
}
