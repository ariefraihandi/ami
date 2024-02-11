<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ItemInvoice;
use App\Models\FinancialTransaction;
use App\Models\Menu;
use App\Models\MenuSub;
use App\Models\MenuSubsChild;
use App\Models\UserRole;
use App\Models\AccessMenu;
use App\Models\AccessSub;
use App\Models\AccessSubChild;
use Carbon\Carbon;
use PDF;


class KeuanganController extends Controller
{
    public function getAllKeuangan()
    {
        $keuangan = FinancialTransaction::orderBy('created_at', 'desc')->get();
    
        $formattedKeuangan = $keuangan->map(function ($transaction) {
            return [
                'id'                    => $transaction->id,
                'reference_number'      => $transaction->reference_number,
                'source_receiver'       => $transaction->source_receiver,
                'status'                => $transaction->status,
                'transaction_amount'    => number_format($transaction->transaction_amount),
                'payment_method'        => $transaction->payment_method,  
                'created_at'            => Carbon::parse($transaction->transaction_date)->isoFormat('D MMM YY'), // Change 'transaction_date' to 'created_at'
                'description'           => $transaction->description,  
            ];
        });
    
        return response()->json(['data' => $formattedKeuangan]);
    }

    public function showKeuanganIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
          
            return redirect('/login');
        }

       
        $today                  = Carbon::today();
        $yesterday              = Carbon::yesterday();

        $qincomeTotal           = FinancialTransaction::whereIn('status', [1, 2, 3])->get();
        $qoutcomeTotal          = FinancialTransaction::whereNotIn('status', [1, 2, 3])->get();
        $kasToday               = FinancialTransaction::whereIn('status', [6])->get();
        $totalkas               = $kasToday->sum('transaction_amount'); //ini
        $transaction            = FinancialTransaction::all();
        // dd($kasToday);
        
        $incomeToday            = FinancialTransaction::whereDate('transaction_date', $today)->whereIn('status', [1, 2, 3])->get();
        $incomeYesterday        = FinancialTransaction::whereDate('transaction_date', $yesterday)->whereIn('status', [1, 2, 3])->get();
        
        $outcomeToday           = FinancialTransaction::whereDate('transaction_date', $today)->whereIn('status', [4, 5])->get();
        $outcomeYesterday       = FinancialTransaction::whereDate('transaction_date', $yesterday)->whereIn('status', [4, 5])->get();        
        
        
        //SUM Amount
        $totalIncomeToday       = $incomeToday->sum('transaction_amount');
        $totalIncomeYesterday   = $incomeYesterday->sum('transaction_amount');
        
        $totalOutcomeToday      = $outcomeToday->sum('transaction_amount');
        $totalOutcomeYesterday  = $outcomeYesterday->sum('transaction_amount');
        
       // Total Income and Total Outcome for all transactions
        $incomeTotal = $qincomeTotal->sum('transaction_amount');
        $outcomeTotal = $qoutcomeTotal->sum('transaction_amount');
        
        $marginToday            = $totalIncomeToday - $totalOutcomeToday;      
        $marginYesterday        = $totalIncomeYesterday - $totalOutcomeYesterday;      
        
        $sisaTidakStor          = $marginToday - $totalkas;        
        
        // Pesentage SUM
        $percentageIncome = 0;

        if ($totalIncomeYesterday != 0) {
            $percentageIncome = (($totalIncomeToday - $totalIncomeYesterday) / $totalIncomeYesterday) * 100;
        }

        $percentageOutcome = 0;
        
        if ($totalOutcomeYesterday != 0) {
            $percentageOutcome = (($totalOutcomeToday - $totalOutcomeYesterday) / $totalOutcomeYesterday) * 100;
        }
        
        $percentageMargin = 0;
        
        if ($marginYesterday != 0) {
            $percentageMargin = (($marginToday - $marginYesterday) / $marginYesterday) * 100;
        }
        
        $percentageTotal = 0;
        
        if ($outcomeTotal != 0) {
            $percentageTotal = (($incomeTotal - $outcomeTotal) / $outcomeTotal) * 100;
        }

        $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        // Mengambil data berdasarkan hak akses
        $menus = Menu::whereIn('id', $accessMenus)->get();
        $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData = UserRole::where('id', $user->role)->first();

        $additionalData = [
            'title'                     => 'Bisnis',
            'subtitle'                  => 'Keuangan',
            'user'                      => $user,
            'role'                      => $roleData,
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'transaction'               => $transaction,

            'totalToday'                => $totalIncomeToday,
            'totalYesterday'            => $totalIncomeYesterday,
            'percentageIncome'          => $percentageIncome,
            'totalOutcomeToday'         => $totalOutcomeToday,
            'totalOutcomeYesterday'     => $totalOutcomeYesterday,
            'percentageOutcome'         => $percentageOutcome,
            'marginToday'               => $marginToday,
            'marginYesterday'           => $marginYesterday,
            'percentageMargin'          => $percentageMargin,
            'totalIncome'               => $incomeTotal,
            'totalOutcome'              => $outcomeTotal,
            'percentageTotal'           => $percentageTotal,
            'totalkas'                  => $totalkas,
            'sisaTidakStor'             => $sisaTidakStor,
        ];

        return view('Konten/Keuangan/keuangan', $additionalData);
    }   

    public function addNewTransaction(Request $request)
    {
        try {
            // Validasi request sesuai kebutuhan Anda
            $request->validate([
                'transactionAmount' => 'required',
                'description' => 'required|string',
                'paymentMethod' => 'required|string',
                'status' => 'required|integer',
                'transactionDate' => 'required|date',
            ]);
    
            // Membersihkan nilai transactionAmount
            $cleanedAmount = $this->cleanNumericInput($request->input('transactionAmount'));
    
            // Mengatur nilai source_receiver berdasarkan status
            $sourceReceiver = $this->getSourceReceiver($request->input('status'));
    
            // Membuat reference_number dengan 5 karakter random
            $referenceNumber = Str::random(5);
    
            // Lakukan operasi penyimpanan atau manipulasi data sesuai kebutuhan
    
            // Contoh menyimpan data ke database
            $newTransaction = new FinancialTransaction([
                'transaction_amount' => $cleanedAmount,
                'description' => $request->input('description'),
                'payment_method' => $request->input('paymentMethod'),
                'status' => $request->input('status'),
                'source_receiver' => $sourceReceiver,
                'reference_number' => $referenceNumber,
                'transaction_date' => $request->input('transactionDate'),
                // Tambahkan field lain sesuai kebutuhan
            ]);
    
            $newTransaction->save();
            $response = [
                    'success' => true,
                    'message' => 'Transaksi berhasil disimpan.',
                ];
                return back()->with('response', $response)->withInput();
            //    return response()->json(['message' => 'Transaction added successfully'], 200);
        } catch (\Exception $e) {
            // Tangani error jika terjadi
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function editTransaction(Request $request)
    {
        $request->validate([
            'transactionDate' => 'required|date',
            'id' => 'required|exists:financial_transactions,id',
        ]);

        try {
            // Retrieve the transaction based on the provided ID
            $transaction = FinancialTransaction::findOrFail($request->input('id'));
            $amount = $this->cleanNumericInput($request->input('amount'));
            $transaction->update([
                'transaction_date' => $request->input('transactionDate'),
                'transaction_amount' => $amount,
            ]);

            $invoiceId = $request->input('invoice_number');
            $invoice = Invoice::where('invoice_number', $invoiceId)->first();

            // Update panjar amount only if invoice is found
            if ($invoice) {
                $remainingItems = FinancialTransaction::where('reference_number', $invoiceId)->get();
                $panjarAmount = 0;

                foreach ($remainingItems as $remainingItem) {
                    // Calculate total amount by summing up transaction amounts
                    $panjarAmount += $remainingItem->transaction_amount;
                }

                // Update the total amount for the invoice
                $invoice->panjar_amount = $panjarAmount;
                $invoice->save();
            }

            $response = [
                'success' => true,
                'title' => 'Berhasil',
                'message' => 'Transaksi Berhasil Diupdate'
            ];

            return redirect()->back()->with('response', $response);
        } catch (\Exception $e) {
            // Handle the exception if the update fails
            $response = [
                'success' => false,
                'title' => 'Error',
                'message' => $e->getMessage(),
            ];

            return redirect()->back()->with('response', $response);
        }
    }

    public function delTrans(Request $request)
    {
        $id = $request->query('id');
    
        try {
            // Ambil data transaksi berdasarkan id
            $transaction = FinancialTransaction::findOrFail($id);
            
            // Hapus transaksi
            $transaction->delete();
    
            // Cari faktur terkait
            $invoice = Invoice::where('invoice_number', $transaction->reference_number)->first();       
    
            if ($invoice) {
                $remainingItems = FinancialTransaction::where('reference_number', $invoice->invoice_number)->get();
                $panjarAmount = 0;

                foreach ($remainingItems as $remainingItem) {
                    // Calculate total amount by summing up transaction amounts
                    $panjarAmount += $remainingItem->transaction_amount;
                }

                // Update the total amount for the invoice
                $invoice->panjar_amount = $panjarAmount;
                $invoice->save();
            }

            $response = [
                'success' => true,
                'title' => 'Berhasil',
                'message' => 'Transaksi berhasil dihapus'
            ];
    
            return redirect()->back()->with('response', $response);
        } catch (\Exception $e) {
            // Tangani exception jika terjadi kesalahan
            $response = [
                'success' => false,
                'title' => 'Error',
                'message' => $e->getMessage(),
            ];
    
            return redirect()->back()->with('response', $response);
        }
    }
    
    public function generatePDF()
{
    try {
        $pdf = PDF::loadView('Konten.Keuangan.report');
        return $pdf->download('report.pdf');
    } catch (\Exception $e) {
        // Print error message
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    private function getSourceReceiver($status)
    {
        switch ($status) {
            case 4:
                return 'Operational';
            case 5:
                return 'Ambilan';
            case 6:
                return 'Setoran Kas';
            case 7:
                return 'Top Up';
            default:
                return '';
        }
    }

    private function cleanNumericInput($input)
    {
        // Menghapus titik (.) dan koma (,)
        $cleanedInput = str_replace(['.', ','], '', $input);

        // Menghapus dua digit nol di belakang koma
        $cleanedInput = preg_replace('/,00$/', '', $cleanedInput);

        return $cleanedInput;
    }
}
