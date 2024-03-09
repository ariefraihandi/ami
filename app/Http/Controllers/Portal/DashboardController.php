<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Date;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class DashboardController extends Controller
{
    public function showPortalPage(Request $request)
    {
        //Syistem
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

            $accessMenus    = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
            $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
            $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');

            $menus          = Menu::whereIn('id', $accessMenus)->get();
            $subMenus       = MenuSub::whereIn('id', $accessSubmenus)->get();
            $childSubMenus  = MenuSubsChild::whereIn('id', $accessChildren)->get();
            $roleData       = UserRole::where('id', $user->role)->first();
            $bulan          = Date::now()->locale('id')->monthName;
            $bulanLalu      = Date::now()->subMonth()->locale('id')->monthName;
            $startingYear   = Carbon::now()->startOfYear();
            $startingMonth  = Carbon::now()->startOfMonth();
            $endMonth       = Carbon::now()->endOfMonth();
            $startPastMonth = $startingMonth->copy()->subMonth()->startOfMonth();
            $endPastMonth   = $startPastMonth->copy()->endOfMonth();
        //!Syistem        
       
        //Date Configuration
            $yearStart      = $startingYear->toDateString();
            $today          = Carbon::now();
            $yesterday      = $today->copy()->subDay();
            $seninDate      = $today->copy()->startOfWeek();
            $selasaDate     = $seninDate->copy()->addDay();
            $rabuDate       = $seninDate->copy()->addDays(2);
            $kamisDate      = $seninDate->copy()->addDays(3);
            $jumatDate      = $seninDate->copy()->addDays(4);
            $sabtuDate      = $seninDate->copy()->addDays(5);
            $startLast      = $seninDate->copy()->subWeek();
            $endLast        = $sabtuDate->copy()->subWeek();
        //!Date Configuration        
        
        $incomeTotal        = FinancialTransaction::getTransactionAmount($today);
        $incomeTotalYes     = FinancialTransaction::getTransactionAmount($yesterday);
        $totIncomeSen       = FinancialTransaction::getTransactionAmount($seninDate);
        $totIncomeSel       = FinancialTransaction::getTransactionAmount($selasaDate);
        $totIncomeRab       = FinancialTransaction::getTransactionAmount($rabuDate);
        $totIncomeKam       = FinancialTransaction::getTransactionAmount($kamisDate);
        $totIncomeJum       = FinancialTransaction::getTransactionAmount($jumatDate);
        $totIncomeSab       = FinancialTransaction::getTransactionAmount($sabtuDate);
        $incomeWeekly       = FinancialTransaction::getWeeklyTransactionAmount($seninDate, $sabtuDate);
        $incomelastWeek     = FinancialTransaction::getWeeklyTransactionAmount($startLast, $endLast);
        
        
        // $incomelastWeek     = FinancialTransaction::getWeeklyTransactionAmount($startLast, $endLast);
        
        
        //Bulanan
            //Money
                $incomeMonthly      = FinancialTransaction::getWeeklyTransactionAmount($startingMonth, $today);
                $incomeLastMonth      = FinancialTransaction::getWeeklyTransactionAmount($startPastMonth, $endPastMonth);
                $totalInvMouthly    = Invoice::getInv($startingMonth, $today)->sum('total_amount');
                $totalBonMonthly    = Invoice::getBon($startingMonth, $today);
                $setorKasMonthly    = FinancialTransaction::getWeeklySetorKasAmount($startingMonth, $today);        
                $setorKasLastMonth  = FinancialTransaction::getWeeklySetorKasAmount($startPastMonth, $endPastMonth);     
                $topUpMonthly       = FinancialTransaction::getWeeklyTopUpAmount($startingMonth, $today);           
                $topUpLastMonth     = FinancialTransaction::getWeeklyTopUpAmount($startPastMonth, $endPastMonth);  
                
                $outcomeMountly     = FinancialTransaction::getRangeOutTransonAmount($startingMonth, $today);  
                $outcomeLastMount   = FinancialTransaction::getRangeOutTransonAmount($startPastMonth, $endPastMonth);  
                $marginMonthly      = $incomeMonthly-$outcomeMountly;
                $marginLastMonth    = $incomeLastMonth-$outcomeLastMount;
            //!Money
            //Count
                $getInvMonthly      = Invoice::getInv($startingMonth, $today)->count();
                $invBonMonthly      = Invoice::getCountInvBon($startingMonth, $today);
            //!Count
        //!Bulanan

        $setorKasWeek       = FinancialTransaction::getWeeklySetorKasAmount($seninDate, $sabtuDate);
        $topUpWeek          = FinancialTransaction::getWeeklyTopUpAmount($seninDate, $sabtuDate);

        $outcomeTotal       = FinancialTransaction::getOutTransAmount($today);
        $outcomeTotalYes    = FinancialTransaction::getOutTransAmount($yesterday);
        $totOutcomeSen      = FinancialTransaction::getOutTransAmount($seninDate);
        $totOutcomeSel      = FinancialTransaction::getOutTransAmount($selasaDate);
        $totOutcomeRab      = FinancialTransaction::getOutTransAmount($rabuDate);
        $totOutcomeKam      = FinancialTransaction::getOutTransAmount($kamisDate);
        $totOutcomeJum      = FinancialTransaction::getOutTransAmount($jumatDate);
        $totOutcomeSab      = FinancialTransaction::getOutTransAmount($sabtuDate);
        $outcomeWeekly      = FinancialTransaction::getRangeOutTransonAmount($seninDate, $sabtuDate);
        $outcomelastWeek    = FinancialTransaction::getRangeOutTransonAmount($startLast, $endLast);

        
        
        
        $invLunWeek         = Invoice::getCountInvLun($seninDate, $sabtuDate);
        $invLunLastWeek     = Invoice::getCountInvLun($startLast, $endLast);
        $invPanWeek         = Invoice::getCountInvPan($seninDate, $sabtuDate);
        $invPanLastWeek     = Invoice::getCountInvPan($startLast, $endLast);
        $invBonWeek         = Invoice::getCountInvBon($seninDate, $sabtuDate);
        

        $fixedTotal         = $incomeWeekly+$topUpWeek-$outcomeWeekly;
        $sisaKasTotal         = $fixedTotal-$setorKasWeek;
// dd($getSUmBonYear);
        // dd($fixedTotal, $sisaKasTotal);
        $data = [
        //Sistem
            'title'             => 'Dashboard',
            'subtitle'          => 'Analytics',
            'user'              => $user,
            'role'              => $roleData,
            'menus'             => $menus,
            'subMenus'          => $subMenus,
            'childSubMenus'     => $childSubMenus,
            'stardateWeek'      => $seninDate,
            'enddateWeek'       => $sabtuDate,
            'startingMonth'     => $startingMonth,
            'endMonth'          => $endMonth,
        //!Sistem

        // Income
            'income'            => $incomeTotal,
            'incomeTotalYes'    => $incomeTotalYes,
            'totIncomeSen'      => $totIncomeSen,
            'totIncomeSel'      => $totIncomeSel,
            'totIncomeRab'      => $totIncomeRab,
            'totIncomeKam'      => $totIncomeKam,
            'totIncomeJum'      => $totIncomeJum,
            'totIncomeSab'      => $totIncomeSab,
            'incomeWeekly'      => $incomeWeekly,
            'incomeLastWeek'    => $incomelastWeek,
            
        //!Income      
            
        // Data Bulanan
            'bulan'             => $bulan,
            'bulanLalu'         => $bulanLalu,
            'totalInvMouthly'   => $totalInvMouthly,
            'incomeMonthly'     => $incomeMonthly,
            'totalBonMonthly'   => $totalBonMonthly,

            'getInvMonthly'     => $getInvMonthly,
            'invBonMonthly'     => $invBonMonthly,

            'setorKasMonthly'   => $setorKasMonthly,
            'setorKasLastMonth' => $setorKasLastMonth,

            'topUpMonthly'      => $topUpMonthly,
            'topUpLastMonth'    => $topUpLastMonth,
            
            'outcomeMountly'    => $outcomeMountly,
            'outcomeLastMount'  => $outcomeLastMount,
            
            'marginMonthly'     => $marginMonthly,
            'marginLastMonth'   => $marginLastMonth,
        //! Data Bulanan 
        
        // Outcome            
            'outcomeTotal'      => $outcomeTotal,
            'outcomeTotalYes'   => $outcomeTotalYes,
            'totOutcomeSen'     => $totOutcomeSen,
            'totOutcomeSel'     => $totOutcomeSel,
            'totOutcomeRab'     => $totOutcomeRab,
            'totOutcomeKam'     => $totOutcomeKam,
            'totOutcomeJum'     => $totOutcomeJum,
            'totOutcomeSab'     => $totOutcomeSab,
            'outcomeWeekly'     => $outcomeWeekly,
            'outcomelastWeek'   => $outcomelastWeek,
        //!Outcome    
            
          
        
        // Invoice
            
            'invLunLastWeek'    => $invLunLastWeek,
            'invPanWeek'        => $invPanWeek,
            'invPanLastWeek'    => $invPanLastWeek,
            'invBonWeek'        => $invBonWeek,
            
        //!Invoice
        ];

        return view('Konten/Portal/dashboard', $data);
    }


    private function formatCurrency($value)
    {
        $absValue = abs($value);

        if ($absValue >= 1000000000) {
            return 'Rp. ' . number_format($value / 1000000000) . ' m';
        } elseif ($absValue >= 1000000) {
            return 'Rp. ' . number_format($value / 1000000) . ' jt';
        } elseif ($absValue >= 1000) {
            return 'Rp. ' . number_format($value / 1000) . ' rb';
        } else {
            return 'Rp. ' . number_format($value);
        }
    }

}
