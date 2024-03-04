<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
use App\Models\Tagihan;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


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

    public function getAlltagihans()
    {
        try {
            // Ambil data tagihan beserta data pengguna yang sesuai
            $tagihans = DB::table('tagihan')
                ->join('users', 'tagihan.id_tagih', '=', 'users.id')
                ->select('tagihan.*', 'users.*')
                ->where('tagihan.status', '=', 0) // Klausa WHERE untuk status tagihan sama dengan 0
                ->where('users.status', '!=', 0) // Klausa WHERE untuk status pengguna tidak sama dengan 0
                ->get();

            // Loop melalui setiap tagihan untuk menambahkan data bonus dan ambilan
            foreach ($tagihans as $tagihan) {
                // Ambil bulan dari start_tagihan
                $bulanIni = Carbon::parse($tagihan->start_tagihan)->format('Y-m');

                // Hitung total bonus untuk tagihan saat ini
                $totalBonus = FinancialTransaction::where('source_receiver', 'Bonus')
                                ->where('reference_number', 'LIKE', 'bs' . $tagihan->id . '_%')
                                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$bulanIni])
                                ->sum('transaction_amount');

                // Hitung total ambilan untuk tagihan saat ini
                $totalAmbilan = FinancialTransaction::where('source_receiver', 'Ambilan')
                                ->where('reference_number', 'LIKE', 'ab' . $tagihan->id . '_%')
                                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$bulanIni])
                                ->sum('transaction_amount');

                // Tambahkan data bonus dan ambilan ke objek tagihan
                $tagihan->bonus = $totalBonus;
                $tagihan->ambilan = $totalAmbilan;
            }

            return response()->json(['data' => $tagihans]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

//KeuanganIndex
    public function showKeuanganIndex(Request $request)
    {
        //Check Access
            $requestedUrl   = $request->path();      
            $urlParts       = explode('/', $requestedUrl);
            $urlPart        = $urlParts[0]; // Ambil bagian pertama dari URL    
            $menuSub        = MenuSub::where('url', $urlPart)->first();
            
            if ($menuSub) {
            
                $menuSubId = $menuSub->id;
                $userRole = session('user_role');
                $accessSub = AccessSub::where('role_id', $userRole)
                                    ->where('submenu_id', $menuSubId)
                                    ->first();
                if (!$accessSub) {
                    return redirect()->route('user.profile')->with([
                        'response' => [
                            'success' => false,
                            'title' => 'Eror',
                            'message' => 'Anda Tidak Memiliki Akses!',
                        ],
                    ]);
                }                  
            } else {
                return redirect()->route('user.profile')->with([
                    'response' => [
                        'success' => false,
                        'title' => 'Eror',
                        'message' => 'URL tidak ditemukan!',
                    ],
                ]);
            }
        //!Check Access
        // Check Subs Child Access
            $requestedUrl = $request->path();       
            $routes = Route::getRoutes();
            $matchedRouteName = null;
                    
            foreach ($routes as $route) {
                if ($route->uri() == $requestedUrl) {
                    // Jika cocok, simpan nama rute dan keluar dari loop
                    $matchedRouteName = $route->getName();
                    break;
                }
            }

            if ($matchedRouteName) {
            $url            = $matchedRouteName;
            $menuChildSub   = MenuSubsChild::where('url', $url)->first();         
            $userRole       = session('user_role');
            $accChildSub    = AccessSubChild::where('role_id', $userRole)
                            ->where('childsubmenu_id', $menuChildSub->id)
                            ->first();

            if (!$accChildSub) {
                return redirect()->route('user.profile')->with([
                    'response' => [
                        'success' => false,
                        'title' => 'Eror',
                        'message' => 'Anda Tidak Memiliki Akses!',
                    ],
                ]);
            }       
            } else {
            return redirect()->route('user.profile')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Eror',
                    'message' => 'Anda Tidak Memiliki Akses!',
                ],
            ]);
            }
        //! Check Subs Child Access

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

        

        $additionalData = [
            'title'                     => 'Bisnis',
            'subtitle'                  => 'Keuangan',
            'user'                      => $user,
            // 'users'                     => $users,
            'role'                      => $roleData,
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            // 'transaction'               => $transaction,

            // 'totalToday'                => $totalIncomeToday,
            // 'totalYesterday'            => $totalIncomeYesterday,
            // 'percentageIncome'          => $percentageIncome,
            // 'totalOutcomeToday'         => $totalOutcomeToday,
            // 'totalOutcomeYesterday'     => $totalOutcomeYesterday,
            // 'percentageOutcome'         => $percentageOutcome,
            // 'marginToday'               => $marginToday,
            // 'marginYesterday'           => $marginYesterday,
            // 'percentageMargin'          => $percentageMargin,
            // 'totalIncome'               => $incomeTotal,
            // 'totalOutcome'              => $outcomeTotal,
            // 'percentageTotal'           => $percentageTotal,
            // 'totalkas'                  => $totalkas,
            // 'sisaTidakStor'             => $udahStor,
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
        //Check Access
            $requestedUrl   = $request->path();      
            $urlParts       = explode('/', $requestedUrl);
            $urlPart        = $urlParts[0]; // Ambil bagian pertama dari URL    
            $menuSub        = MenuSub::where('url', $urlPart)->first();
            
            if ($menuSub) {
            
                $menuSubId = $menuSub->id;
                $userRole = session('user_role');
                $accessSub = AccessSub::where('role_id', $userRole)
                                    ->where('submenu_id', $menuSubId)
                                    ->first();
                if (!$accessSub) {
                    return redirect()->route('user.profile')->with([
                        'response' => [
                            'success' => false,
                            'title' => 'Eror',
                            'message' => 'Anda Tidak Memiliki Akses!',
                        ],
                    ]);
                }                  
            } else {
                return redirect()->route('user.profile')->with([
                    'response' => [
                        'success' => false,
                        'title' => 'Eror',
                        'message' => 'URL tidak ditemukan!',
                    ],
                ]);
            }
        //!Check Access
        // Check Subs Child Access
            $requestedUrl = $request->path();       
            $routes = Route::getRoutes();
            $matchedRouteName = null;
                    
            foreach ($routes as $route) {
                if ($route->uri() == $requestedUrl) {
                    // Jika cocok, simpan nama rute dan keluar dari loop
                    $matchedRouteName = $route->getName();
                    break;
                }
            }

            if ($matchedRouteName) {
            $url            = $matchedRouteName;
            $menuChildSub   = MenuSubsChild::where('url', $url)->first();         
            $userRole       = session('user_role');
            $accChildSub    = AccessSubChild::where('role_id', $userRole)
                            ->where('childsubmenu_id', $menuChildSub->id)
                            ->first();

            if (!$accChildSub) {
                return redirect()->route('user.profile')->with([
                    'response' => [
                        'success' => false,
                        'title' => 'Eror',
                        'message' => 'Anda Tidak Memiliki Akses!',
                    ],
                ]);
            }       
            } else {
            return redirect()->route('user.profile')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Eror',
                    'message' => 'Anda Tidak Memiliki Akses!',
                ],
            ]);
            }
        //! Check Subs Child Access

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
        $JumlahBB           = $invoicesBB->sum('total_amount');
        $jumlahPanjar       = $invoicesPJ->sum('panjar_amount');
        $sisaPanjar         = $invoicesPJ->sum('total_amount');
        $sis     = $sisaPanjar - $jumlahPanjar;
        $hutangCustumer     = $sis + $JumlahBB;

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
            'hutangCustumer'    => $hutangCustumer,
            'totalTagih'          => $totalTagih,
            'topup'             => $saldoTopup,

        ];

        return view('Konten/Keuangan/laporan', $additionalData);
    } 
//! Laporan


//Tagihan
    public function showTagihanIndex(Request $request)
    {
        // System
            //Check Access
                $requestedUrl   = $request->path();      
                $urlParts       = explode('/', $requestedUrl);
                $urlPart        = $urlParts[0]; // Ambil bagian pertama dari URL    
                $menuSub        = MenuSub::where('url', $urlPart)->first();
                
                if ($menuSub) {
                
                    $menuSubId = $menuSub->id;
                    $userRole = session('user_role');
                    $accessSub = AccessSub::where('role_id', $userRole)
                                        ->where('submenu_id', $menuSubId)
                                        ->first();
                    if (!$accessSub) {
                        return redirect()->route('user.profile')->with([
                            'response' => [
                                'success' => false,
                                'title' => 'Eror',
                                'message' => 'Anda Tidak Memiliki Akses!',
                            ],
                        ]);
                    }                  
                } else {
                    return redirect()->route('user.profile')->with([
                        'response' => [
                            'success' => false,
                            'title' => 'Eror',
                            'message' => 'URL tidak ditemukan!',
                        ],
                    ]);
                }
            //!Check Access
            // Check Subs Child Access
                $requestedUrl = $request->path();       
                $routes = Route::getRoutes();
                $matchedRouteName = null;
                        
                foreach ($routes as $route) {
                    if ($route->uri() == $requestedUrl) {
                        // Jika cocok, simpan nama rute dan keluar dari loop
                        $matchedRouteName = $route->getName();
                        break;
                    }
                }

                if ($matchedRouteName) {
                $url            = $matchedRouteName;
                $menuChildSub   = MenuSubsChild::where('url', $url)->first();         
                $userRole       = session('user_role');
                $accChildSub    = AccessSubChild::where('role_id', $userRole)
                                ->where('childsubmenu_id', $menuChildSub->id)
                                ->first();

                if (!$accChildSub) {
                    return redirect()->route('user.profile')->with([
                        'response' => [
                            'success' => false,
                            'title' => 'Eror',
                            'message' => 'Anda Tidak Memiliki Akses!',
                        ],
                    ]);
                }       
                } else {
                return redirect()->route('user.profile')->with([
                    'response' => [
                        'success' => false,
                        'title' => 'Eror',
                        'message' => 'Anda Tidak Memiliki Akses!',
                    ],
                ]);
                }
            //! Check Subs Child Access
            //Sidebar
                $user = Auth::user();
                if (!$user) {
                
                    return redirect('/login');
                }
                $users                  = User::all();
                $accessMenus            = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
                $accessSubmenus         = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
                $accessChildren         = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
            
                $menus                  = Menu::whereIn('id', $accessMenus)->get();
                $subMenus               = MenuSub::whereIn('id', $accessSubmenus)->get();
                $childSubMenus          = MenuSubsChild::whereIn('id', $accessChildren)->get();
                $roleData               = UserRole::where('id', $user->role)->first();
            //!Sidebar
        //! System

        $tagihan = Tagihan::join('users', 'tagihan.id_tagih', '=', 'users.id')
                  ->where('tagihan.status', 0)
                  ->where('users.status', '<>', 0)
                  ->get();

        foreach ($tagihan as $item) {            
            $bulanIni = Carbon::parse($item->start_tagihan)->format('Y-m');
            // Mengambil ambilan untuk tagihan ini
            $ambilan = FinancialTransaction::where('source_receiver', 'Ambilan')
                ->where('reference_number', 'LIKE', 'ab' . $item->id_tagih . '_%')
                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$bulanIni])
                ->sum('transaction_amount');   
            // Mengambil total bonus untuk tagihan ini
            $totalBonus = FinancialTransaction::where('source_receiver', 'Bonus')
                ->where('reference_number', 'LIKE', 'bs' . $item->id_tagih . '_%')
                ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$bulanIni])
                ->sum('transaction_amount');
        
            // Menghitung jumlah tagihan dengan menambahkan bonus dan mengurangkan ambilan
            $jumlahTagihan = $item->jumlah_tagihan + $totalBonus - $ambilan;
        
            // Menetapkan nilai baru untuk jumlah tagihan setelah menambahkan bonus dan mengurangkan ambilan
            $item->jumlah_tagihan = $jumlahTagihan;
        }

        $additionalData = [
            'title'                     => 'Bisnis',
            'subtitle'                  => 'Tagihan',
            'user'                      => $user,
            'users'                     => $users,
            'role'                      => $roleData,
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'tagihan'                   => $tagihan,
            
        ];

        return view('Konten/Keuangan/tagihan', $additionalData);
    }
//!Tagihan

    public function bayarGaji(Request $request)
    {
        $jumlah_berhasil = 0;
        $jumlah_gagal = 0;

        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // Mendapatkan data dari permintaan
            $data_pembayaran = $request->input('data_pembayaran');

            // Cek apakah $data_pembayaran bukan null dan merupakan array
            if (!is_null($data_pembayaran) && is_array($data_pembayaran)) {
                // Iterasi melalui data_pembayaran
                foreach ($data_pembayaran as $data) {
                    $id_tagih = $data['id_tagih'];
                    $bonus = $data['bonus'];
                    $ambilan = $data['ambilan'];
                    $total = $data['total'];

                    // Memeriksa data dengan id_tagih yang diterima
                    $tagihan = Tagihan::where('id_tagih', $id_tagih)->where('status', 0)->first();

                    // Jika data tagihan ditemukan
                    if ($tagihan) {
                        // Buat data baru untuk bulan berikutnya
                        $nextMonth = date('Y-m-d', strtotime('+1 month', strtotime($tagihan->start_tagihan)));
                        $newTagihan = new Tagihan();
                        $newTagihan->id_tagih =  $tagihan->id_tagih;
                        $newTagihan->start_tagihan = $nextMonth;
                        $newTagihan->nama_tagihan = $tagihan->nama_tagihan;
                        $newTagihan->jenis_tagihan = $tagihan->jenis_tagihan;
                        $newTagihan->jumlah_tagihan = $tagihan->jumlah_tagihan;
                        $newTagihan->masa_kerja = '0';
                        $newTagihan->tagihan_ke = $tagihan->tagihan_ke;
                        $newTagihan->sampai_ke = $tagihan->sampai_ke;
                        $newTagihan->status = 0;

                        $newTagihan->save();

                        // Update status tagihan yang lama
                        $tagihan->status = 1;
                        $tagihan->save();

                        // Simpan transaksi keuangan
                        $transaction = new FinancialTransaction();
                        $transaction->transaction_date = now(); // Gunakan waktu saat ini
                        $transaction->source_receiver = 'Gaji';
                        $transaction->description = 'Membayar Gaji ' . $tagihan->nama_tagihan . '. Sebesar: Rp.' . number_format($total, 0) . ',- || ' . 'Detil: Gaji Pokok Sebesar: Rp' . number_format($tagihan->jumlah_tagihan, 0) . ',- | ' . ' Bonus Sebesar: Rp.' . number_format($bonus, 0) . ',- | ' . ' Ambilan Sebesar: Rp.'. number_format($ambilan, 0) . ',- .';
                        $transaction->transaction_amount = $total;
                        $transaction->payment_method = 'Transfer';
                        $transaction->reference_number = 'gj' . $id_tagih . '_' . Str::random(3); // Menggunakan Str::random untuk mendapatkan 3 karakter acak
                        $transaction->status = 9;
                        $transaction->lunas = 1;
                        $transaction->save();

                        // Catat aktivitas pengguna
                        $userActivity = new UserActivity();
                        $userActivity->user_id = auth()->id(); // ID admin yang melakukan aktivitas
                        $userActivity->activity = 'Membayar Gaji ' . $tagihan->nama_tagihan; // Aktivitas admin
                        $userActivity->ip_address = $request->ip(); // Alamat IP pengguna
                        $userActivity->device_info = 'gj_inputation'; // Informasi perangkat (jika diperlukan)
                        $userActivity->save();

                        // Tambahkan jumlah pembayaran yang berhasil
                        $jumlah_berhasil++;
                    }
                }

                // Commit transaksi database jika semua data berhasil diproses
                DB::commit();

                // Kirim respons JSON jika semua data berhasil diproses
                return response()->json([
                    'success' => true,
                    'message' => 'Semua pembayaran berhasil diproses',
                    'jumlah_berhasil' => $jumlah_berhasil,
                    'jumlah_gagal' => $jumlah_gagal
                ]);
            } else {
                // Jika $data_pembayaran null atau bukan array, kirim pesan yang sesuai
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembayaran tidak valid atau tidak ada',
                    'jumlah_berhasil' => $jumlah_berhasil,
                    'jumlah_gagal' => $jumlah_gagal
                ]);
            }
        } catch (\Exception $e) {
            // Tangani kesalahan jika terjadi
            DB::rollBack();

            // Kirim respons JSON dengan pesan kesalahan jika terjadi kesalahan
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()
            ], 500); // Kode status 500 untuk kesalahan server
        }
    }

    
    public function editMasaKerja(Request $request)
    {
        try {
            // Validasi input jika diperlukan
            $request->validate([
                'masa_kerja' => 'required', 
                'id' => 'required|integer', 
            ]);

            // Ambil data dari formulir
            $masa_kerja = $request->masa_kerja;
            $id = $request->id;

            // Lakukan operasi pengeditan pada model Anda
            $tagihan = Tagihan::find($id);
            if ($tagihan) {
                $tagihan->masa_kerja = $masa_kerja;
                $tagihan->save();

                $response = [
                    'success' => true,
                    'title' => 'Berhasil',
                    'message' => 'Masa Kerja Berhasil Diupdate'
                ];

                return redirect()->back()->with('response', $response);
            } else {
                // Jika tidak ditemukan, kembalikan dengan pesan error
                $response = [
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Data tidak ditemukan',
                ];

                return redirect()->back()->with('response', $response);
            }
        } catch (\Exception $e) {
            // Tangani error
            $response = [
                'success' => false,
                'title' => 'Error',
                'message' => $e->getMessage(),
            ];

            return redirect()->back()->with('response', $response);
        }
    }

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
            'bgImage'           => $bgImage,            

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
        return $pdf->stream('laporan '.$jenis.'.pdf');
        
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
