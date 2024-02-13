<?php

namespace App\Http\Controllers\Portal;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ItemInvoice;
use App\Models\FinancialTransaction;
use App\Models\Product;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuSub;
use App\Models\MenuSubsChild;
use App\Models\UserRole;
use App\Models\AccessMenu;
use App\Models\AccessSub;
use App\Models\AccessSubChild;
use App\Models\UserActivity; 

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
          
            return redirect('/');
        }

        $accessMenus        = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus     = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren     = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        $menus              = Menu::whereIn('id', $accessMenus)->get();
        $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData           = UserRole::where('id', $user->role)->first();
        $userActivities     = UserActivity::where('user_id', $user->id)->get();

        $additionalData = [
            'title'                     => 'User',
            'subtitle'                  => 'Profile',
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'user'                      => $user,
            'role'                      => $roleData,
            'userActivities'            => $userActivities,
        ];
    
        return view('Konten/User/profile', $additionalData);
    }

    public function showUserAdminIndex(Request $request)
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
            'title'                     => 'Admin',
            'subtitle'                  => 'Users',
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
}
