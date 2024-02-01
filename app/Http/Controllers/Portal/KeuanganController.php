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

    public function showKeuanganIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
          
            return redirect('/login');
        }

        if ($request->ajax()) {
            $keuangan = FinancialTransaction::orderBy('created_at', 'desc')->get();

            $formattedKeuangan = $keuangan->map(function ($transaction) {
                return [
                    'id'                    => $transaction->id,
                    'reference_number'      => $transaction->reference_number,
                    'source_receiver'       => $transaction->source_receiver,
                    'status'                => $transaction->status,
                    'transaction_amount'    => number_format($transaction->transaction_amount),
                    'payment_method'        => $transaction->payment_method,  
                    'created_at'            => Carbon::parse($transaction->created_at)->isoFormat('D MMM YY'),
                    'description'           => $transaction->description,  
                ];
            });

            return response()->json(['data' => $formattedKeuangan]);
        }
       
        $today                  = Carbon::today();
        $yesterday              = Carbon::yesterday();

        $qincomeTotal           = FinancialTransaction::whereIn('status', [1, 2, 3])->get();
        $qoutcomeTotal          = FinancialTransaction::whereNotIn('status', [1, 2, 3])->get();
        $kasToday               = FinancialTransaction::whereIn('status', [6])->get();
        $totalkas               = $kasToday->sum('transaction_amount');
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
            
            'totalkas'           => $totalkas,
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


    
    public function create()
    {
        // Tampilkan form untuk membuat data baru
        return view('create_view');
    }


    public function edit($id)
    {
        // Ambil data berdasarkan ID untuk diedit
        $data = YourModel::findOrFail($id);

        return view('edit_view', compact('data'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data dan update ke database
        YourModel::findOrFail($id)->update($request->all());

        return redirect()->route('your.index')->with('success', 'Data berhasil diupdate!');
    }

    public function destroy($id)
    {
        // Hapus data dari database
        YourModel::findOrFail($id)->delete();

        return redirect()->route('your.index')->with('success', 'Data berhasil dihapus!');
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
