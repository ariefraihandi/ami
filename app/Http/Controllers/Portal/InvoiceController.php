<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ItemInvoice;
use App\Models\FinancialTransaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;


class InvoiceController extends Controller
{

    public function getAllInvoices()
    {
        $invoices = Invoice::with('customer')->get();
    
            $formattedInvoices = $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'customer' => [
                        'name' => $invoice->customer->name,
                        'email' => $invoice->customer->email,
                        'customer_type' => $invoice->customer->customer_type,
                    ],
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_name' => $invoice->invoice_name,
                    'type' => $invoice->type,
                    'status' => $invoice->status,
                    'total_amount' => $invoice->total_amount,
                    'panjar_amount' => $invoice->panjar_amount,
                    'due_date' => $invoice->due_date,
                    'customer_uuid' => $invoice->customer_uuid,
                    'additional_notes' => $invoice->additional_notes,
                ];
            });
    
        return response()->json(['data' => $formattedInvoices]);
    }

    public function getInvoiceItems($invNumber)
{
    try {
        // Cari invoice berdasarkan nomor invoice
        $invoice = ItemInvoice::where('invoice_id', $invNumber)->first();

        if (!$invoice) {
            return response()->json(['error' => 'Invoice not found.'], 404);
        }

        // Ambil data item dari invoice
        $items = $invoice->items;

        return response()->json(['data' => $items]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public function showInvoiceIndex(Request $request)
    {     
    
        $invoices               = Invoice::with('customer')->get();
        $invoicesToday          = Invoice::with('customer')->whereDate('created_at', Carbon::today())->get();
        $invoicesYesterday      = Invoice::with('customer')->whereDate('created_at', Carbon::yesterday())->get();
        $totalAmount            = Invoice::with('customer')->whereDate('created_at', '<=', Carbon::today())->sum('total_amount');
        $totalAmountYest        = Invoice::with('customer')->whereDate('created_at', '<=', Carbon::yesterday())->sum('total_amount');
        $invoicesDueToday       = Invoice::with('customer')->whereDate('due_date', '<=', Carbon::today())->where('status', '!=', 2)->where(function ($query) {$query->where('total_amount', '>', 0)->orWhere('panjar_amount', '>', 0);})->get();
        $invoicesDueYesterday   = Invoice::with('customer')->whereDate('due_date', '<=', Carbon::yesterday())->where('status', '!=', 2)->where(function ($query) {$query->where('total_amount', '>', 0)->orWhere('panjar_amount', '>', 0);})->get();
        $totalUnAmount          = Invoice::with('customer')->whereDate('created_at', '<=', Carbon::today())->where('status', '!=', 2)->selectRaw('SUM(total_amount - panjar_amount) as total_unamount')->first()->total_unamount;
        $totalUnAmountYest      = Invoice::with('customer')->whereDate('created_at', '<=', Carbon::yesterday())->where('status', '!=', 2)->selectRaw('SUM(total_amount - panjar_amount) as total_unamount')->first()->total_unamount;


        $totalInvoices          = $invoices->count();
        $totalInvToday          = $invoicesToday->count();
        $totalInvYesterday      = $invoicesYesterday->count();
        $totalInvDueToday       = $invoicesDueToday->count();
        $totalInvDueYesterday   = $invoicesDueYesterday->count();
        $totalDueToday          = $invoicesDueToday->sum(function ($invoice) {return $invoice->total_amount - $invoice->panjar_amount;});
        $totalDueYesterday      = $invoicesDueYesterday->sum(function ($invoice) {return $invoice->total_amount - $invoice->panjar_amount;});                
        


        $percentageIncrease     = 0;

        if ($totalInvYesterday > 0) {
            $percentageIncrease = (($totalInvToday - $totalInvYesterday) / $totalInvYesterday) * 100;
        }

        $percentageDue          = 0;
        if ($totalInvDueYesterday > 0) {
            $percentageDue       = (($totalDueToday - $totalDueYesterday) / $totalDueYesterday) * 100;
        }
        
        $totalPercentage        = 0;
        if ($totalInvDueYesterday > 0) {
            $totalPercentage       = (($totalAmount - $totalAmountYest) / $totalAmountYest) * 100;
        }
        
        $unTotalPercentage        = 0;
        if ($totalUnAmountYest > 0) {
            $unTotalPercentage       = (($totalUnAmount - $totalUnAmountYest) / $totalUnAmountYest) * 100;
        }
        
        // dd($percentageIncrease);
            
        $additionalData = [
            'title'                     => 'Invoice List',
            'subtitle'                  => 'Dashboard',
            'invoices'                  => $invoices,
            'totalInvoices'             => $totalInvoices,
            'totalInvoicesToday'        => $totalInvToday,
            'totalInvoicesYesterday'    => $totalInvYesterday,
            'percentageIncrease'        => $percentageIncrease,
            'totalInvDueToday'          => $totalInvDueToday,
            'DueToday'                  => $totalDueToday,
            'percentageDue'             => $percentageDue,
            'totalAmount'               => $totalAmount,
            'totalPercentage'           => $totalPercentage,
            'totalUnAmount'             => $totalUnAmount,
            'unTotalPercentage'         => $unTotalPercentage,
        ];
    
        return view('Konten/Invoice/invoicelist', $additionalData);
    }
    
    public function addItem(Request $request)
    {
        $invoiceNumber  = $request->query('invoiceNumber');
        $customerUuid   = $request->query('customerUuid');
        
        $customer       = Customer::where('uuid', $customerUuid)->first();
        $invoice        = Invoice::where('invoice_number', $invoiceNumber)->first();       
        $itemInvoice    = ItemInvoice::where('invoice_id', $invoiceNumber)->get();
        $invoiceData    = Invoice::where('invoice_number', $invoiceNumber)->get();
        $transaction    = FinancialTransaction::where('reference_number', $invoiceNumber)->get();
        $products       = Product::all();

        if (!$customer) {
            return redirect()->route('invoices.index')->with('response', [
                'success' => false,
                'message' => 'Customer tidak ditemukan.',
            ]);
        }
        if (!$invoice) {
            return redirect()->route('invoices.index')->with('response', [
                'success' => false,
                'message' => 'Invoice tidak ditemukan.',
            ]);
        }       

        $total_amount       = $invoice->total_amount - $invoice->panjar_amount;
        $panjar_amount      = $invoice->panjar_amount;
        $dueDate            = $invoice->due_date;
        $created_at         = $invoice->created_at;

        $formatDueDate      = Carbon::createFromFormat('Y-m-d H:i:s', $dueDate)->format('Y-m-d');
        $formatCreated_at   = Carbon::createFromFormat('Y-m-d H:i:s', $created_at)->format('Y-m-d');
        $format_amount      = "Rp. " . number_format($total_amount, 2);
        $format_panjar      = "Rp. " . number_format($panjar_amount, 2);
        $subtotal           = $itemInvoice->sum(function ($item) {return $item->harga_satuan * $item->qty * $item->ukuran;});
        $discount           = $itemInvoice->sum(function ($item) {return $item->discount;});
        $format_subtotal    = "Rp. " . number_format($subtotal, 2);
        $format_discount    = "Rp. " . number_format($discount, 2);
        $firstItem = ItemInvoice::where('invoice_id', $invoiceNumber)->first();

        if ($firstItem) {
            $tax = $firstItem->tax . "%";
        } else {
            // Handle the case when no item with the given invoice_id is found
            // You can set a default tax value or handle it based on your requirements
            $tax = "0%";
        }


        $additionalData = [
            'title'         => 'Add Item Invoice #' . $invoice->invoice_number,
            'subtitle'      => 'Invoice',
            'invoiceNumber' => $invoiceNumber,
            'customerUuid'  => $customerUuid,
            'invoiceData'   => $invoice,
            'invoices'      => $invoiceData,
            'itemInvoice'   => $itemInvoice,
            'dueDate'       => $formatDueDate,
            'transactions'  => $transaction,
            'created_at'    => $formatCreated_at,
            'total_amount'  => $format_amount,
            'customerData'  => $customer,
            'note'          => $invoice->additional_notes,
            'products'      => $products,
            'subtotal'      => $format_subtotal,
            'discount'      => $format_discount,
            'panjar'        => $format_panjar,
            'tax'           => $tax,
            'message' => 0, // Ubah sesuai dengan kondisi atau logika Anda
            'messageText' => 'Pesan Anda di sini', // Ubah sesuai dengan pesan yang ingin ditampilkan
        ];

       
        return view('Konten/Invoice/addItem', $additionalData);
    }

    public function create()
    {
        return view('invoices.create');
    }


    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'customer_uuid' => 'required',
                'invoiceName' => 'required',
                'type' => 'required',
                // Add other validation rules as needed
            ]);

            // Check if the customer exists based on the provided UUID
            $customer = Customer::where('uuid', $request->input('customer_uuid'))->first();
            
            if (!$customer) {
                // Handle the error, maybe redirect the user or show an error message
                return redirect()->back()->with('error', 'Customer not found.');
            }

            // Create a new invoice number
            $prefix = ($request->input('type') == 'Sales') ? 'S' : 'P';
            $invoiceNumber = $prefix . (Invoice::max('id') + 1000);

            // Set total_amount default to 0
            $totalAmount = 0;

            // Set due_date one week from the invoice creation date
            $dueDate = now()->addWeek();

            // Create an instance of the Invoice model
            $invoice = new Invoice([
                'customer_uuid' => $request->input('customer_uuid'),
                'invoice_number' => $invoiceNumber,
                'invoice_name' => $request->input('invoiceName'),
                'type' => $request->input('type'),
                'status' => '0',
                'total_amount' => $totalAmount,
                'due_date' => $dueDate,
                'payment_method' => '0',
                'additional_notes' => 'Mohon diperhatikan, kami tidak menyediakan layanan tukar atau pengembalian. Kami berharap produk ini memenuhi harapan Anda. Selamat menikmati pembelian Anda!',
            ]);

            $invoice->save();

            $customerUuid = $request->input('customer_uuid');
    
            // SweetAlert response
            $response = [
                'success' => true,
                'title' => 'Berhasil',
                'message' => 'Invoice Berhasil Dibuat, Silahkan Tambahkan Item'
            ];
    
            return redirect()
                ->to('invoice/item?invoiceNumber=' . $invoiceNumber . '&customerUuid=' . $customerUuid)
                ->with('response', $response);
        } catch (ValidationException $e) {
            // Capture validation error messages
            $errors = $e->validator->errors()->all();
    
            // Redirect back with error messages
            $response = [
                'success' => false,
                'title' => 'Error',
                'message' => implode(', ', $errors),
            ];
    
            return back()->with('response', $response)->withInput();
        }
    }

    public function itemStore(Request $request)
    {
        $validatedData = validator($request->all(), [
            'kode_barang' => 'required|string',
            'barang' => 'required|string',
            'deskripsi' => 'required|string',
            'ukurana' => 'required|string',
            'ukuranb' => 'required|string', // Tambahkan validasi untuk ukuranb
            'qty' => 'required|integer',
            'harga_satuan' => 'required',
            'discount' => 'required',
            'tax' => 'required|numeric',
            'invoice_id' => 'required|string',
        ]);

        // If validation fails, throw an exception
        if ($validatedData->fails()) {
            throw new \Exception($validatedData->errors()->first());
        }

        try {         
    
            \DB::beginTransaction();
    
            try {
                $hargaSatuan = $this->cleanNumericInput($request->input('harga_satuan'));
                $discount = $this->cleanNumericInput($request->input('discount'));
                $ukuranaInput = $this->bulatkanUkuran($request->input('ukurana'));
                $ukuranbInput = $this->bulatkanUkuran($request->input('ukuranb'));
    
                $volume = ($ukuranaInput / 100) * ($ukuranbInput / 100);
    
                $item = new ItemInvoice([
                    'kode_barang' => $request->input('kode_barang'),
                    'barang' => $request->input('barang'),
                    'deskripsi' => $request->input('deskripsi'),
                    'ukuran' => $volume,
                    'ukurana' => $request->input('ukurana'),
                    'ukuranb' => $request->input('ukuranb'),
                    'bulata' => $ukuranaInput,
                    'bulatb' => $ukuranbInput,
                    'qty' => $request->input('qty'),
                    'harga_satuan' => $hargaSatuan,
                    'discount' => $discount,
                    'tax' => $this->cleanNumericInput($request->input('tax')),
                    'invoice_id' => $request->input('invoice_id'),
                   
                ]);
    
                // Simpan item ke database
                $item->save();
    
                // Update total amount in the associated invoice
                $invoice = Invoice::where('invoice_number', $request->input('invoice_id'))->first();
    
                if (!$invoice) {
                    throw new \Exception('Invoice not found.');
                }
                
                // Inisialisasi totalAmountWithTax
                $totalAmountWithTax = 0;
                
                // Ambil semua item invoice yang terkait dengan invoice
                $items = ItemInvoice::where('invoice_id', $request->input('invoice_id'))->get();
                
                // Loop melalui setiap item dan tambahkan ke totalAmountWithTax
                foreach ($items as $item) {
                    $totalAmountWithTax += ($item->harga_satuan * $item->qty * $item->ukuran) - $item->discount + (($item->harga_satuan * $item->qty * $item->ukuran - $item->discount ) * ($item->tax / 100));
                }

                $invoice->total_amount = $totalAmountWithTax;
                $invoice->save();
    
                // Commit the transaction
                \DB::commit();
    
                $response = [
                    'success' => true,
                    'message' => 'Item updated successfully',
                ];
    
                // Ambil data invoiceNumber dan customerUuid dari request
                $invoiceNumber = $request->input('invoice_id');
                $customerUuid = $request->input('uuid');
    
                // Buat URL dengan parameter invoiceNumber dan customerUuid
                $url = url("/invoice/item?invoiceNumber=$invoiceNumber&customerUuid=$customerUuid");
    
                // Redirect dengan menyertakan pesan sukses
                return redirect($url)->with('response', $response);
            } catch (\Exception $e) {
                // Rollback the transaction in case of an error
                \DB::rollBack();
    
                // Handle exceptions
                return response()->json(['success' => false, 'message' => 'Failed to create item', 'error' => $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            // Handle exceptions for other errors
            return response()->json(['success' => false, 'message' => 'Failed to create item', 'error' => $e->getMessage()], 500);
        }
    }
    
       

    public function show(Invoice $invoice)
    {
        // Tampilkan detail invoice
        return view('invoices.show', compact('invoice'));
    }

    public function deleteItem($id)
    {
        $item = ItemInvoice::find($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found');
        }
    
        $totalTransaction = ($item->harga_satuan * $item->qty) - $item->discount * ( $item->tax / 100);

        // Temukan invoice yang memiliki invoice_id yang sama dengan item invoice
        $invoice = Invoice::where('invoice_number', $item->invoice_id)->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found');
        }

        // Kurangi total transaksi dari total_amount di invoice
        $invoice->total_amount -= $totalTransaction;
        $invoice->save();

        // Hapus item invoice
        $item->delete();

        return redirect()->back()->with('success', 'Item deleted successfully');
    }

    public function updateInvoiceDates(Request $request)
    {
        // Validasi request sesuai kebutuhan Anda
        $request->validate([
            'invoice_id' => 'required|exists:invoices,invoice_number',
            'new_date' => 'required|date',
            'date_type' => 'required|in:created_at,due_date',
        ]);       

        // Dapatkan data dari request
        $invoiceId = $request->input('invoice_id');
        $newDate = $request->input('new_date');
        $dateType = $request->input('date_type');

        // Temukan invoice berdasarkan ID
        $invoice = Invoice::where('invoice_number', $invoiceId)->first();

        if ($invoice) {
            // Tambahkan waktu saat mengupdate tanggal
            $newDateTime = Carbon::parse($newDate);
            $newDateTime->setTime(now()->hour, now()->minute, now()->second);
        
            // Update tanggal sesuai dengan date_type
            $invoice->{$dateType} = $newDateTime;
            $invoice->save();
        
            return response()->json(['success' => true, 'message' => 'Tanggal berhasil diubah']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invoice tidak ditemukan']);
        }
    }

    public function getInvoiceItemData($itemId)
    {
        try {
            // Mengambil data item berdasarkan ID
            $item = ItemInvoice::findOrFail($itemId);

            // Mengembalikan data dalam bentuk JSON
            return response()->json([
                'success' => true,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            // Mengembalikan respon kesalahan jika terjadi masalah
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateItem(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $item = ItemInvoice::findOrFail($id);

            // Save old values for potential rollback
            $oldHargaSatuan = $item->harga_satuan;
            $oldQty = $item->qty;
            $oldDiscount = $item->discount;
            $oldTax = $item->tax;
            $oldInvoiceId = $item->invoice_id;

            // Set discount and tax from request
            $item->discount = $request->input('discount');
            $item->tax = $request->input('tax');

            // Update item invoice with new values
            $item->fill($request->all());
            $item->save();

            // Recalculate total_amount
            $invoiceId = $item->invoice_id;
            $itemsInInvoice = ItemInvoice::where('invoice_id', $invoiceId)->get();
            $totalAmount = 0;

            foreach ($itemsInInvoice as $invoiceItem) {
                $totalAmount += ($invoiceItem->harga_satuan * $invoiceItem->qty) - $invoiceItem->discount + (($invoiceItem->harga_satuan * $invoiceItem->qty) - $invoiceItem->discount) * ($invoiceItem->tax / 100);
            }

            // Update total_amount in the invoice table
            $invoice = Invoice::where('invoice_number', $invoiceId)->first();

            if (!$invoice) {
                // Handle the case where the invoice is not found
                DB::rollBack();
                return response()->json(['error' => 'Invoice not found.'], 404);
            }

            $invoice->total_amount = $totalAmount;
            $invoice->save();

            DB::commit();

            $invoiceNumber  = $request->input('invoice_id');
            $customerUuid   = $request->input('uuid');

            $response = [
                'success' => true,
                'message' => 'Item updated successfully',
            ];

            // return response()->json(['message' => 'Item updated successfully']);
            $url = url("/invoice/item?invoiceNumber=$invoiceNumber&customerUuid=$customerUuid");
            return redirect($url)->with('response', $response);            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Failed to update item: ' . $e->getMessage());

            // Rollback changes on any exception
            DB::rollBack();

            return response()->json(['error' => 'Failed to update item.'], 500);
        }
    }

    public function deleteInvoice(Request $request)
    {
        $invoiceNumber = $request->input('invoiceNumber');
     
        try {
            $deletedItemCount = ItemInvoice::where('invoice_id', $invoiceNumber)->delete();
            $deletedInvoiceCount = Invoice::where('invoice_number', $invoiceNumber)->delete();
            $deletedTransaction = FinancialTransaction::where('reference_number', $invoiceNumber)->delete();
    
            $deletedItemsInfo = $deletedItemCount > 0 ? "$deletedItemCount items" : "No Items";
            $deletedInvoiceInfo = $deletedInvoiceCount > 0 ? "$deletedInvoiceCount Invoice" : "No Invoice";
            $deletedTransactionInfo = $deletedTransaction > 0 ? "$deletedTransaction Transactions" : "No Transactions";
    
            $successMessage = "Deleted successfully: $deletedInvoiceInfo, $deletedItemsInfo, $deletedTransactionInfo";
    
            if ($deletedItemCount || $deletedInvoiceCount || $deletedTransaction) {
                $response = [
                    'success' => true,
                    'title' => 'Success',
                    'message' => $successMessage,
                ];
                return redirect()->route('invoices.index')->with('response', $response);
            } else {
                throw new \Exception('Failed to delete Invoice & Item');
            }
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'title' => 'Error',
                'message' => $e->getMessage(),
            ];
            return redirect()->back()->with('response', $response);
        }
    }
    

    public function bayarInvoice(Request $request)
    {        
        try {
            $request->validate([
                'total_amount_input' => 'required',
                'invoice_number' => 'required|exists:invoices,invoice_number',
            ]);
            
          
            DB::beginTransaction();

            $invoice = Invoice::where('invoice_number', $request->input('invoice_number'))->firstOrFail();

            $totalAmountInput = $this->cleanNumericInput($request->input('total_amount_input'));
            $totalAmountDisplay = $this->cleanNumericInput($request->input('total_amount_display'));

            // dd($totalAmountInput,  $totalAmountDisplay);
            $method = $request->input('methode');

            $sisa = $totalAmountInput - $totalAmountDisplay;

            if ($sisa < 0) {
                if ($invoice->panjar_amount == 0) {
                    $invoice->status = 1;
                    $invoice->payment_method = $method;
                    $invoice->panjar_amount = $totalAmountInput;

                    $financialTransaction = new FinancialTransaction();

                    $financialTransaction->transaction_date = Carbon::now()->toDateString();
                    $financialTransaction->source_receiver = "Tagihan Invoice";
                    $financialTransaction->description = "Pembayaran Panjar Invoice Nomor " . $invoice->invoice_number;
                    $financialTransaction->transaction_amount = $totalAmountInput;
                    $financialTransaction->payment_method = ($method == 1) ? 'Cash' : 'Transfer';
                    $financialTransaction->reference_number = $invoice->invoice_number;
                    $financialTransaction->status = 1;

                    $financialTransaction->save();

                    $response = [
                        'success' => true,
                        'message' => 'Pembayaran Panjar Berhasil',
                    ];
                } else {
                    $invoice->status = 1;
                    $invoice->payment_method = $method;
                    $invoice->panjar_amount += $totalAmountInput;

                    $financialTransaction = new FinancialTransaction();

                    $financialTransaction->transaction_date = Carbon::now()->toDateString();
                    $financialTransaction->source_receiver = "Tagihan Invoice";
                    $financialTransaction->description = "Pembayaran Partial Invoice Nomor " . $invoice->invoice_number;
                    $financialTransaction->transaction_amount = $totalAmountInput;
                    $financialTransaction->payment_method = ($method == 1) ? 'Cash' : 'Transfer';
                    $financialTransaction->reference_number = $invoice->invoice_number;
                    $financialTransaction->status = 2;

                    $financialTransaction->save();

                    $response = [
                        'success' => true,
                        'message' => 'Pembayaran Partial Berhasil',
                    ];
                }
            } 
            
            if ($sisa >= 0) {
                $invoice->status = 2;
                $invoice->payment_method = $method;
                $invoice->panjar_amount = $totalAmountInput;

                $financialTransaction = new FinancialTransaction();

                $financialTransaction->transaction_date = Carbon::now()->toDateString();
                $financialTransaction->source_receiver = "Tagihan Invoice";
                $financialTransaction->description = "Pelunasan Invoice Nomor " . $invoice->invoice_number;
                $financialTransaction->transaction_amount = $totalAmountDisplay;
                $financialTransaction->payment_method = ($method == 1) ? 'Cash' : 'Transfer';
                $financialTransaction->reference_number = $invoice->invoice_number;
                $financialTransaction->status = 3;

                $financialTransaction->save();

                $response = [
                    'success' => true,
                    'message' => 'Pelunasan Invoice Berhasil',
                ];
            }

            $invoice->save();

            // Commit transaksi jika semua operasi berhasil
            DB::commit();

            // Redirect kembali ke halaman sebelumnya
            return redirect()->back()->with('response', $response);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            $response = [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ];

            // Redirect kembali ke halaman sebelumnya dengan pesan kesalahan
            return redirect()->back()->with('response', $response);
        }
    }

    public function generatePdf()
    {
        $data = ['example' => 'Hello, this is an example!'];
    
        // $pdf = PDF::loadView('Konten/Invoice/invoicePDF');
    
        // return $pdf->download('example.pdf');
        return view('Konten/Invoice/invoicePDF');
    }
        

private function cleanNumericInput($input)
{
    // Menghapus titik (.) dan koma (,)
    $cleanedInput = str_replace(['.', ','], '', $input);

    // Menghapus dua digit nol di belakang koma
    $cleanedInput = preg_replace('/,00$/', '', $cleanedInput);

    return $cleanedInput;
}

public function bulatkanUkuran($ukuran)
{
    // Pastikan bahwa $ukuran adalah angka
    if (!is_numeric($ukuran)) {
        throw new \InvalidArgumentException('Invalid input format. Expected a number.');
    }

    // Jika angka kurang dari atau sama dengan 100, bulatkan ke 100
    if ($ukuran <= 100) {
        return 100;
    } else {
        // Gunakan ceil untuk mendekatkan ke angka di atasnya dalam kelipatan 50
        return ceil(($ukuran - 5) / 50) * 50;
    }
}
    
}
