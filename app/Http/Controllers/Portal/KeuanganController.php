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
use App\Models\User;
use App\Models\UserActivity;
use App\Models\AccessMenu;
use App\Models\AccessSub;
use App\Models\AccessSubChild;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
// use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;


class KeuanganController extends Controller
{
    public function getAllKeuangan()
    {
        $keuangan = FinancialTransaction::orderBy('created_at', 'desc')->get();
    
        $formattedKeuangan = $keuangan->map(function ($transaction) {

            $invoice = Invoice::where('invoice_number', $transaction->reference_number)->first();

            // Jika invoice ditemukan, gunakan customer_uuid dari invoice
            if ($invoice) {
                $customer = $invoice->customer_uuid;
            } else {
                $customer = '';
            }

            return [
                'id'                    => $transaction->id,
                'reference_number'      => $transaction->reference_number,
                'source_receiver'       => $transaction->source_receiver,
                'customer'              => $customer,
                'status'                => $transaction->status,
                'transaction_amount'    => number_format($transaction->transaction_amount),
                'payment_method'        => $transaction->payment_method,  
                'created_at'            => Carbon::parse($transaction->transaction_date)->isoFormat('D MMM YY'), // Change 'transaction_date' to 'created_at'
                'description'           => $transaction->description,  
            ];
        });
    
        return response()->json(['data' => $formattedKeuangan]);
    }
    
//KeuanganIndex
    public function showKeuanganIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
          
            return redirect('/login');
        }
        
        $accessMenus            = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus         = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren         = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        $menus                  = Menu::whereIn('id', $accessMenus)->get();
        $subMenus               = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus          = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData               = UserRole::where('id', $user->role)->first();
       
        $today                  = Carbon::today();
        $yesterday              = Carbon::yesterday();

        $qincomeTotal           = FinancialTransaction::whereIn('status', [1, 2, 3])->get();
        $qoutcomeTotal          = FinancialTransaction::whereIn('status', [4, 5])->get();
        $saldoKas               = FinancialTransaction::whereIn('status', [6])->get();
        $topupKas               = FinancialTransaction::whereIn('status', [7])->get();
        $allIncome              = $qincomeTotal->sum('transaction_amount');
        $totalkas               = $qincomeTotal->sum('transaction_amount') - $qoutcomeTotal->sum('transaction_amount') - $saldoKas->sum('transaction_amount') + $topupKas->sum('transaction_amount') ;     
        $udahStor               = $saldoKas->sum('transaction_amount') - $topupKas->sum('transaction_amount');
        
        // dd($totalkas);
        $transaction            = FinancialTransaction::all();
        $users                  = User::all();
        
        $incomeToday            = FinancialTransaction::whereDate('transaction_date', $today)->whereIn('status', [1, 2, 3])->get();
        $incomeYesterday        = FinancialTransaction::whereDate('transaction_date', $yesterday)->whereIn('status', [1, 2, 3])->get();
        
        $outcomeToday           = FinancialTransaction::whereDate('transaction_date', $today)->whereIn('status', [4, 5])->get();
        $outcomeYesterday       = FinancialTransaction::whereDate('transaction_date', $yesterday)->whereIn('status', [4, 5])->get();        
        
        
        // Dayli Income Amount
            $totalIncomeToday       = $incomeToday->sum('transaction_amount');
            $totalIncomeYesterday   = $incomeYesterday->sum('transaction_amount');
            $percentageIncome = 0;

            if ($totalIncomeYesterday != 0) {
                $percentageIncome = (($totalIncomeToday - $totalIncomeYesterday) / $totalIncomeYesterday) * 100;
            }
        //Dayli Income Amount
        
        //Dayli Outcome Amount
            $totalOutcomeToday      = $outcomeToday->sum('transaction_amount');
            $totalOutcomeYesterday  = $outcomeYesterday->sum('transaction_amount');
            $percentageOutcome = 0;
            
            if ($totalOutcomeYesterday != 0) {
                $percentageOutcome = (($totalOutcomeToday - $totalOutcomeYesterday) / $totalOutcomeYesterday) * 100;
            }
        //Dayli Outcome Amount

        //Margin Amount
            $marginToday            = $totalIncomeToday - $totalOutcomeToday;      
            $marginYesterday        = $totalIncomeYesterday - $totalOutcomeYesterday;      
            $percentageMargin = 0;
            
            if ($marginYesterday != 0) {
                $percentageMargin = (($marginToday - $marginYesterday) / $marginYesterday) * 100;
            }
        //Margin Amount
        

       // Total Income and Total Outcome for all transactions
        $incomeTotal = $qincomeTotal->sum('transaction_amount');
        $outcomeTotal = $qoutcomeTotal->sum('transaction_amount');
        
        $sisaTidakStor          = $marginToday - $totalkas;        
        
       

       
        
        
        
        $percentageTotal = 0;
        
        if ($outcomeTotal != 0) {
            $percentageTotal = (($incomeTotal - $outcomeTotal) / $outcomeTotal) * 100;
        }

        



        $additionalData = [
            'title'                     => 'Bisnis',
            'subtitle'                  => 'Keuangan',
            'user'                      => $user,
            'users'                     => $users,
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
            'sisaTidakStor'             => $udahStor,
        ];

        return view('Konten/Keuangan/keuangan', $additionalData);
    }   
//KeuanganIndex

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
                'lunas' => 'in:on',
            ]);

            // Membersihkan nilai transactionAmount
            $cleanedAmount = $this->cleanNumericInput($request->input('transactionAmount'));

            // Mengatur nilai source_receiver berdasarkan status
            $sourceReceiver = $this->getSourceReceiver($request->input('status'));

    
            if ($request->input('status') == 5) {
                $referenceNumber = 'ab' . $request->input('karyawan') . '_' . Str::random(3); // Menambahkan ID pengguna ke reference number
            } elseif ($request->input('status') == 8) {
                $referenceNumber = 'bs' . $request->input('karyawan') . '_' . Str::random(3); // Menambahkan ID pengguna ke reference number
            } else {
                $referenceNumber = Str::random(5);
            }


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
                'lunas' => $request->input('lunas') ? 1 : 0,
            ]);

            $newTransaction->save();

            if ($request->input('status') == 5) {
                $adminActv  = 'Approval Ambilan';
                $userActv   = 'Request Ambilan';
                $adminType  = 'ab_approval';                
                $userType   = 'ab_request';
            } elseif ($request->input('status') == 8) {
                $adminActv  = 'Approval Bonus';
                $userActv   = 'Get Bonus';
                $adminType  = 'bs_approval';                
                $userType   = 'bs_get';
            } elseif ($request->input('status') == 4) {
                $adminActv  = 'Inputasi Operasional'; 
                $adminType  = 'op_inputation';   
                $userActv  = '';   
                $userType   = ''; // Definisikan $userType di sini
            } elseif ($request->input('status') == 6) {
                $adminActv  = 'Inputasi Kas'; 
                $adminType  = 'kas_inputation';   
                $userActv  = '';
                $userType   = ''; // Definisikan $userType di sini
            } elseif ($request->input('status') == 7) {
                $adminActv  = 'Inputasi TopUp'; 
                $adminType  = 'tu_inputation';   
                $userActv  = '';
                $userType   = ''; // Definisikan $userType di sini
            } else {
                // Tambahkan logika untuk jenis transaksi lainnya jika diperlukan
            }
            
            // Menambahkan aktivitas admin
            $adminActivity = new UserActivity([
                'user_id' => auth()->id(), // ID admin yang melakukan aktivitas
                'activity' => $adminActv, // Aktivitas admin
                'ip_address' => 'Jumlah Rp' . $cleanedAmount . ',-', // Menyimpan jumlah transaksi
                'device_info' => $adminType, // Informasi perangkat
            ]);
            $adminActivity->save();
            
            if ($userActv !== null) {
                $userActivity = new UserActivity([
                    'user_id' => $request->input('karyawan'), // ID pengguna yang melakukan aktivitas
                    'activity' => $userActv, // Jenis aktivitas pengguna
                    'ip_address' => 'Jumlah Rp ' . $cleanedAmount . ',-', // Menyimpan jumlah transaksi
                    'device_info' => $userType, // Informasi perangkat
                ]);
                $userActivity->save();
            }
            
            
            $response = [
                'title' => 'Berhasil',
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

                if ($panjarAmount == 0) {
                    // Jika status sebelumnya adalah 2 (lunas) atau 1 (sebagian lunas), ubah menjadi 0 (belum lunas)
                    $invoice->status = 0;
                } else {
                    // Jika masih ada sisa panjar, ubah status menjadi 1 (sebagian lunas)
                    $invoice->status = 1;
                }
                
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

// Laporan
    public function showLaporan(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        // Mengambil ID dari menu, submenu, dan sub-child menu yang diakses oleh pengguna
        $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');

        // Mengambil data menu, submenu, dan sub-child menu berdasarkan ID yang diakses oleh pengguna
        $menus = Menu::whereIn('id', $accessMenus)->get();
        $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();

        // Mendapatkan data peran pengguna
        $roleData = UserRole::where('id', $user->role)->first();

        $startDate      = $request->input('startDate');
        $endDate        = $request->input('endDate');
        $users          = User::all();     

        if (!$startDate || !$endDate) {
            // Jika salah satu atau kedua parameter kosong, atur tanggal mulai dan akhir menjadi tanggal hari ini
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } else {
            // Jika waktu tidak disertakan dalam URL, tambahkan waktu default
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        // Menentukan Jenis Laporan
            $diffInDays         = $endDate->diffInDays($startDate);
            $diffInWeeks        = $endDate->diffInWeeks($startDate);
            $diffInMonths       = $endDate->diffInMonths($startDate);
            $diffInYears        = $endDate->diffInYears($startDate);

            if ($diffInDays == 0) {
                $jenis = 'Harian';
            } elseif ($diffInDays > 0 && $diffInDays <= 6) {
                $jenis = 'Mingguan';
            } elseif ($diffInDays >= 28 && $diffInDays <= 31) {
                $jenis = 'Bulanan';
            } elseif ($diffInDays >= 365) {
                $jenis = 'Tahunan';
            } else {
                $jenis = 'Custom';
            }
        // !Menentukan Jenis Laporan       

        $invoices           = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '!=', 0.00)
                            ->get(); 

        $invoicesBB         = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '!=', 0.00)
                            ->where('panjar_amount', 0.00)
                            ->get();  

        $invoicesPJ         = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '>', DB::raw('panjar_amount'))
                            ->where('panjar_amount', '!=', 0.00)
                            ->get();

        $invoicesLUN        = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '<=', DB::raw('panjar_amount'))
                            ->where('panjar_amount', '!=', 0.00)
                            ->get();

        $income             = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [1, 2, 3])
                            ->get();
        
        $outcome            = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [4,5])
                            ->get();
        
        $tagihan            = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [4, 5])
                            ->where('lunas', 0)
                            ->get();
                        
        $setorKas           = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [6])
                            ->get();
        
        $topup              = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [7])
                            ->get();
                               
                            
        $totalInvoices      = $invoices->count();
        $totalInvoicesBB    = $invoicesBB->count();
        $totalInvoicesPJ    = $invoicesPJ->count();
        $invoicesLN         = $invoicesLUN->count();
        
        $totalincome        = $income->count();
        $incomeTotal        = $income->sum('transaction_amount');
        $totaloutcome       = $outcome->count();
        $outcomeTotal       = $outcome->sum('transaction_amount');
        $saldoKas           = $setorKas->sum('transaction_amount');
        $saldoTopup         = $topup->sum('transaction_amount');
        $totalTagih         = $tagihan->sum('transaction_amount');


        $additionalData = [
            'title'             => 'Bisnis',
            'subtitle'          => 'Keuangan / Laporan',
            'user'              => $user,
            'users'             => $users,
            'role'              => $roleData,
            'menus'             => $menus,
            'subMenus'          => $subMenus,
            'childSubMenus'     => $childSubMenus,
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'jenis'             => $jenis,
            
            'invoices'          => $invoices,
            'invoicesBB'        => $invoicesBB,
            'invoicesPJ'        => $invoicesPJ,
            'invoicesLUN'       => $invoicesLUN,
            'invoicesLN'        => $invoicesLN,
            'totalInvoices'     => $totalInvoices,
            'totalInvoicesBB'   => $totalInvoicesBB,
            'totalInvoicesPJ'   => $totalInvoicesPJ,
            
            'income'            => $income,
            'outcome'           => $outcome,
            'setorKas'          => $setorKas,
            'tagihan'           => $tagihan,
            'top'               => $topup,
            'totalincome'       => $totalincome,
            'incomeTotal'       => $incomeTotal,
            'totaloutcome'      => $totaloutcome,
            'outcomeTotal'      => $outcomeTotal,
            'saldoKas'          => $saldoKas,
            'totalTagih'          => $totalTagih,
            'topup'             => $saldoTopup,

        ];

        return view('Konten/Keuangan/laporan', $additionalData);
    } 
//! Laporan

    public function generatePDF(Request $request)
    {
        try {

        $startDate      = $request->input('startDate');
        $endDate        = $request->input('endDate');        

        if (!$startDate || !$endDate) {
            // Jika salah satu atau kedua parameter kosong, atur tanggal mulai dan akhir menjadi tanggal hari ini
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
        } else {
            // Jika waktu tidak disertakan dalam URL, tambahkan waktu default
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        // Menentukan Jenis Laporan
            $diffInDays         = $endDate->diffInDays($startDate);
            $diffInWeeks        = $endDate->diffInWeeks($startDate);
            $diffInMonths       = $endDate->diffInMonths($startDate);
            $diffInYears        = $endDate->diffInYears($startDate);

            if ($diffInDays == 0) {
                $jenis = 'Harian';
                $image = 'lap-harian.png';
            } elseif ($diffInDays > 0 && $diffInDays <= 6) {
                $jenis = 'Mingguan';
                $image = 'lap-mingguan.png';
            } elseif ($diffInDays >= 28 && $diffInDays <= 31) {
                $jenis = 'Bulanan';
                $image = 'lap-bulanan.png';
            } elseif ($diffInDays >= 365) {
                $jenis = 'Tahunan';
                $image = 'lap-tahunan.png';
            } else {
                $jenis = 'Custom';
                $image = 'lap.png';
            }
        // !Menentukan Jenis Laporan       

        $invoices           = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '!=', 0.00)
                            ->get(); 

        $invoicesBB         = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '!=', 0.00)
                            ->where('panjar_amount', 0.00)
                            ->get();  

        $invoicesPJ         = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '>', DB::raw('panjar_amount'))
                            ->where('panjar_amount', '!=', 0.00)
                            ->get();

        $invoicesLUN        = Invoice::whereBetween('created_at', [$startDate, $endDate])
                            ->where('total_amount', '<=', DB::raw('panjar_amount'))
                            ->where('panjar_amount', '!=', 0.00)
                            ->get();

        $income             = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [1, 2, 3])
                            ->get();
        
        $outcome            = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [4,5])
                            ->get();
        
        $tagihan            = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [4, 5])
                            ->where('lunas', 0)
                            ->get();
                        
        $setorKas           = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [6])
                            ->get();
        
        $topup              = FinancialTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                            ->whereIn('status', [7])
                            ->get();
                  
        $background         = 'bg-report.png';
        $link               = public_path('assets/img/report/kop.png');                
        $imagePath          = public_path('assets/img/report/' . $image);   
        $bgImage            = public_path('assets/img/report/' . $background);        

        $totalInvoices      = $invoices->count();
        $totalInvoicesBB    = $invoicesBB->count();
        $totalInvoicesPJ    = $invoicesPJ->count();
        $invoicesLN         = $invoicesLUN->count();
        
        $totalincome        = $income->count();
        $incomeTotal        = $income->sum('transaction_amount');
        $totaloutcome       = $outcome->count();
        $outcomeTotal       = $outcome->sum('transaction_amount');
        $saldoKas           = $setorKas->sum('transaction_amount');
        $saldoTopup         = $topup->sum('transaction_amount');
        $totalTagih         = $tagihan->sum('transaction_amount');


        $additionalData = [
            'title'             => 'Laporan ' . $jenis,            
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'jenis'             => $jenis,
            
            'invoices'          => $invoices,
            'invoicesBB'        => $invoicesBB,
            'invoicesPJ'        => $invoicesPJ,
            'invoicesLUN'       => $invoicesLUN,
            'invoicesLN'        => $invoicesLN,
            'totalInvoices'     => $totalInvoices,
            'totalInvoicesBB'   => $totalInvoicesBB,
            'totalInvoicesPJ'   => $totalInvoicesPJ,
            
    
            'logoPath'          => $link,
            'imagePath'         => $imagePath,            
            'bgImage'                => $bgImage,            

            'income'            => $income,
            'outcome'           => $outcome,
            'setorKas'          => $setorKas,
            'tagihan'           => $tagihan,
            'top'               => $topup,
            'totalincome'       => $totalincome,
            'incomeTotal'       => $incomeTotal,
            'totaloutcome'      => $totaloutcome,
            'outcomeTotal'      => $outcomeTotal,
            'saldoKas'          => $saldoKas,
            'totalTagih'          => $totalTagih,
            'topup'             => $saldoTopup,

        ];

        $pdf = PDF::loadView('Konten.Keuangan.report', $additionalData);
        return $pdf->download('laporan '.$jenis.'.pdf');
        
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
            case 8:
                return 'Bonus';
            default:
                return '';
        }
    }

    private function determineJenisLaporan($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffDays = $start->diffInDays($end);

        if ($diffDays === 0) {
            return 'Harian';
        } else if ($diffDays > 0 && $diffDays <= 7) {
            return 'Mingguan';
        } else if ($diffDays > 7 && $diffDays <= 30) {
            return 'Bulanan';
        } else {
            return 'Tahunan';
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
