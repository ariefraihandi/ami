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

            $menus              = Menu::whereIn('id', $accessMenus)->get();
            $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
            $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
            $roleData           = UserRole::where('id', $user->role)->first();

            $starting           = Carbon::createFromDate(2023, 12, 1);
            $bulan              = Carbon::now()->locale('id')->monthName;        
            $bulanLalu          = Carbon::now()->subMonth()->locale('id')->monthName;
            $duaBulanLalu       = Carbon::now()->subMonths(2)->locale('id')->monthName;
            $startingYear       = Carbon::now()->startOfYear();
            $endingYear         = Carbon::now()->endOfYear();
            $startingLastYear   = Carbon::now()->startOfYear()->subYear();
            $endingLastYear     = Carbon::now()->endOfYear()->subYear();
            $startingMonth      = Carbon::now()->startOfMonth();
            $endMonth           = Carbon::now()->endOfMonth();
            $startPastMonth     = $startingMonth->copy()->subMonth()->startOfMonth();
            $endPastMonth       = $startPastMonth->copy()->endOfMonth();      
            $startTwoMonth      = $startingMonth->copy()->subMonths(2)->startOfMonth();
            $endTwoMonth        = $startTwoMonth->copy()->endOfMonth();           
            $currentYear        = Carbon::now()->year;
            $lastYear            = Carbon::now()->subYear()->year;
            
            $startWeek1     = $startingMonth->copy()->startOfWeek();
            $endWeek1       = $startingMonth->copy()->startOfWeek()->addDays(6);    
            $startWeek2     = $startWeek1->copy()->addWeek();
            $endWeek2       = $startWeek2->copy()->addDays(6);
            $startWeek3     = $startWeek2->copy()->addWeek();
            $endWeek3       = $startWeek3->copy()->addDays(6);
            $startWeek4     = $startWeek3->copy()->addWeek();
            $endWeek4       = $startWeek4->copy()->addDays(6);            
            
            $startPaWeek1   = $startPastMonth->copy()->startOfWeek();
            $endPaWeek1     = $startPastMonth->copy()->startOfWeek()->addDays(6);    
            $startPaWeek2   = $startPaWeek1->copy()->addWeek();
            $endPaWeek2     = $startPaWeek2->copy()->addDays(6);
            $startPaWeek3   = $startPaWeek2->copy()->addWeek();
            $endPaWeek3     = $startPaWeek3->copy()->addDays(6);
            $startPaWeek4   = $startPaWeek3->copy()->addWeek();
            $endPaWeek4     = $startPaWeek4->copy()->addDays(6);            
        //!Syistem        
       
        //Date Configuration
            $yearStart              = $startingYear->toDateString();
            $today                  = Carbon::now();
            $yesterday              = $today->copy()->subDay();
            
            //Mingguan
                $seninDate              = $today->copy()->startOfWeek();
                $selasaDate             = $seninDate->copy()->addDay();
                $rabuDate               = $seninDate->copy()->addDays(2);
                $kamisDate              = $seninDate->copy()->addDays(3);
                $jumatDate              = $seninDate->copy()->addDays(4);
                $sabtuDate              = $seninDate->copy()->addDays(5);
                $startLast              = $seninDate->copy()->subWeek();
                $endLast                = $sabtuDate->copy()->subWeek();
            //!Mingguan

            // Bulanan
                $janStartDateYear       = Carbon::create($currentYear, 1, 1)->toDateString();
                $janEndDateYear         = Carbon::create($currentYear, 1, 1)->endOfMonth()->toDateString();

                $janStartDateLastYear   = Carbon::create($lastYear, 1, 1)->toDateString();
                $janEndDateLastYear     = Carbon::create($lastYear, 1, 1)->endOfMonth()->toDateString();
            
                $febStartDateYear       = Carbon::create($currentYear, 2, 1)->toDateString();
                $febEndDateYear         = Carbon::create($currentYear, 2, 1)->endOfMonth()->toDateString();

                $febStartDateLastYear   = Carbon::create($lastYear, 2, 1)->toDateString();
                $febEndDateLastYear     = Carbon::create($lastYear, 2, 1)->endOfMonth()->toDateString();

                $marStartDateYear       = Carbon::create($currentYear, 3, 1)->toDateString();
                $marEndDateYear         = Carbon::create($currentYear, 3, 1)->endOfMonth()->toDateString();

                $marStartDateLastYear   = Carbon::create($lastYear, 3, 1)->toDateString();
                $marEndDateLastYear     = Carbon::create($lastYear, 3, 1)->endOfMonth()->toDateString();

                $aprStartDateYear       = Carbon::create($currentYear, 4, 1)->toDateString();
                $aprEndDateYear         = Carbon::create($currentYear, 4, 1)->endOfMonth()->toDateString();

                $aprStartDateLastYear   = Carbon::create($lastYear, 4, 1)->toDateString();
                $aprEndDateLastYear     = Carbon::create($lastYear, 4, 1)->endOfMonth()->toDateString();

                $mayStartDateYear       = Carbon::create($currentYear, 5, 1)->toDateString();
                $mayEndDateYear         = Carbon::create($currentYear, 5, 1)->endOfMonth()->toDateString();

                $mayStartDateLastYear   = Carbon::create($lastYear, 5, 1)->toDateString();
                $mayEndDateLastYear     = Carbon::create($lastYear, 5, 1)->endOfMonth()->toDateString();

                $junStartDateYear       = Carbon::create($currentYear, 6, 1)->toDateString();
                $junEndDateYear         = Carbon::create($currentYear, 6, 1)->endOfMonth()->toDateString();

                $junStartDateLastYear   = Carbon::create($lastYear, 6, 1)->toDateString();
                $junEndDateLastYear     = Carbon::create($lastYear, 6, 1)->endOfMonth()->toDateString();

                $julStartDateYear       = Carbon::create($currentYear, 7, 1)->toDateString();
                $julEndDateYear         = Carbon::create($currentYear, 7, 1)->endOfMonth()->toDateString();

                $julStartDateLastYear   = Carbon::create($lastYear, 7, 1)->toDateString();
                $julEndDateLastYear     = Carbon::create($lastYear, 7, 1)->endOfMonth()->toDateString();

                $augStartDateYear       = Carbon::create($currentYear, 8, 1)->toDateString();
                $augEndDateYear         = Carbon::create($currentYear, 8, 1)->endOfMonth()->toDateString();

                $augStartDateLastYear   = Carbon::create($lastYear, 8, 1)->toDateString();
                $augEndDateLastYear     = Carbon::create($lastYear, 8, 1)->endOfMonth()->toDateString();

                $sepStartDateYear       = Carbon::create($currentYear, 9, 1)->toDateString();
                $sepEndDateYear         = Carbon::create($currentYear, 9, 1)->endOfMonth()->toDateString();

                $sepStartDateLastYear   = Carbon::create($lastYear, 9, 1)->toDateString();
                $sepEndDateLastYear     = Carbon::create($lastYear, 9, 1)->endOfMonth()->toDateString();

                $octStartDateYear       = Carbon::create($currentYear, 10, 1)->toDateString();
                $octEndDateYear         = Carbon::create($currentYear, 10, 1)->endOfMonth()->toDateString();

                $octStartDateLastYear   = Carbon::create($lastYear, 10, 1)->toDateString();
                $octEndDateLastYear     = Carbon::create($lastYear, 10, 1)->endOfMonth()->toDateString();

                $novStartDateYear       = Carbon::create($currentYear, 11, 1)->toDateString();
                $novEndDateYear         = Carbon::create($currentYear, 11, 1)->endOfMonth()->toDateString();

                $novStartDateLastYear   = Carbon::create($lastYear, 11, 1)->toDateString();
                $novEndDateLastYear     = Carbon::create($lastYear, 11, 1)->endOfMonth()->toDateString();

                $decStartDateYear       = Carbon::create($currentYear, 12, 1)->toDateString();
                $decEndDateYear         = Carbon::create($currentYear, 12, 1)->endOfMonth()->toDateString();

                $decStartDateLastYear   = Carbon::create($lastYear, 12, 1)->toDateString();
                $decEndDateLastYear     = Carbon::create($lastYear, 12, 1)->endOfMonth()->toDateString();
            //!Bulanan
        //!Date Configuration        
        
        $incomeTotal        = FinancialTransaction::getTransactionAmount($today);
        $incomeTotalYes     = FinancialTransaction::getTransactionAmount($yesterday);
        $totIncomeSen       = FinancialTransaction::getTransactionAmount($seninDate);
        $totIncomeSel       = FinancialTransaction::getTransactionAmount($selasaDate);
        $totIncomeRab       = FinancialTransaction::getTransactionAmount($rabuDate);
        $totIncomeKam       = FinancialTransaction::getTransactionAmount($kamisDate);
        $totIncomeJum       = FinancialTransaction::getTransactionAmount($jumatDate);
        $totIncomeSab       = FinancialTransaction::getTransactionAmount($sabtuDate);
      
        
        
        // $incomelastWeek     = FinancialTransaction::getIncomeRangeAmount($startLast, $endLast);
        
        //Mingguan
            //Income
                $incomeWeek1    = FinancialTransaction::getIncomeRangeAmount($startWeek1, $endWeek1);
                $incomeWeek2    = FinancialTransaction::getIncomeRangeAmount($startWeek2, $endWeek2);
                $incomeWeek3    = FinancialTransaction::getIncomeRangeAmount($startWeek3, $endWeek3);
                $incomeWeek4    = FinancialTransaction::getIncomeRangeAmount($startWeek4, $endWeek4);
                
                $incomeWeekly   = FinancialTransaction::getIncomeRangeAmount($seninDate, $sabtuDate);
                $incomelastWeek = FinancialTransaction::getIncomeRangeAmount($startLast, $endLast);
                
                $incPastWeek1   = FinancialTransaction::getIncomeRangeAmount($startPaWeek1, $endPaWeek1);
                $incPastWeek2   = FinancialTransaction::getIncomeRangeAmount($startPaWeek2, $endPaWeek2);
                $incPastWeek3   = FinancialTransaction::getIncomeRangeAmount($startPaWeek3, $endPaWeek3);
                $incPastWeek4   = FinancialTransaction::getIncomeRangeAmount($startPaWeek4, $endPaWeek4);
            //!Income
            //Outcome
                $outcomeWeek1   = FinancialTransaction::getRangeOutTransonAmount($startWeek1, $endWeek1);  
                $outcomeWeek2   = FinancialTransaction::getRangeOutTransonAmount($startWeek2, $endWeek2);  
                $outcomeWeek3   = FinancialTransaction::getRangeOutTransonAmount($startWeek3, $endWeek3);  
                $outcomeWeek4   = FinancialTransaction::getRangeOutTransonAmount($startWeek4, $endWeek4);  
                
                $outPastWeek1   = FinancialTransaction::getRangeOutTransonAmount($startPaWeek1, $endPaWeek1);  
                $outPastWeek2   = FinancialTransaction::getRangeOutTransonAmount($startPaWeek2, $endPaWeek2);  
                $outPastWeek3   = FinancialTransaction::getRangeOutTransonAmount($startPaWeek3, $endPaWeek3);  
                $outPastWeek4   = FinancialTransaction::getRangeOutTransonAmount($startPaWeek4, $endPaWeek4);  
            //!Outcome
            //Margin
                $marginWeek1    = $incomeWeek1-$outcomeWeek1;
                $marginWeek2    = $incomeWeek2-$outcomeWeek2;
                $marginWeek3    = $incomeWeek3-$outcomeWeek3;
                $marginWeek4    = $incomeWeek4-$outcomeWeek4;
                
                $margPastWeek1  = $incPastWeek1-$outPastWeek1;
                $margPastWeek2  = $incPastWeek2-$outPastWeek2;
                $margPastWeek3  = $incPastWeek3-$outPastWeek3;
                $margPastWeek4  = $incPastWeek4-$outPastWeek4;
            //!Margin
        //!Mingguan
        //Bulanan
            //Money
                $incomeThisMonth    = FinancialTransaction::getIncomeRangeAmount($startingMonth, $today);
                $incomeLastMonth    = FinancialTransaction::getIncomeRangeAmount($startPastMonth, $endPastMonth);
                $incomeTwoMonth     = FinancialTransaction::getIncomeRangeAmount($startTwoMonth, $endTwoMonth);
                $totalInvPaid       = Invoice::getInvPaid($startingMonth, $today);               
                $totalInvMouthly    = Invoice::getInv($startingMonth, $today)->sum('total_amount');
                $totalBonMonthly    = Invoice::getBon($startingMonth, $today);
                $setorKasMonthly    = FinancialTransaction::getWeeklySetorKasAmount($startingMonth, $today);        
                $setorKasLastMonth  = FinancialTransaction::getWeeklySetorKasAmount($startPastMonth, $endPastMonth);     
                $topUpMonthly       = FinancialTransaction::getWeeklyTopUpAmount($startingMonth, $today);           
                $topUpLastMonth     = FinancialTransaction::getWeeklyTopUpAmount($startPastMonth, $endPastMonth);  
                
                $outcomeMountly     = FinancialTransaction::getRangeOutTransonAmount($startingMonth, $today);  
                $outcomeLastMount   = FinancialTransaction::getRangeOutTransonAmount($startPastMonth, $endPastMonth);  
                $marginMonthly      = $incomeThisMonth-$outcomeMountly;
                $marginLastMonth    = $incomeLastMonth-$outcomeLastMount;
                $operationalThisMo  = $outcomeMountly-$topUpMonthly;
                $operationalLastMo  = $outcomeLastMount-$topUpLastMonth;
            //!Money
            //Count
                // $getInvMonthly      = Invoice::getInv($startingMonth, $today)->count();
                // $invBonMonthly      = Invoice::getCountInvBon($startingMonth, $today);
            //!Count
            //Monthly Income
                $incomeJanYear      = FinancialTransaction::getMarginByRange($janStartDateYear, $janEndDateYear);
                $incomeJanLastYear  = FinancialTransaction::getMarginByRange($janStartDateLastYear, $janEndDateLastYear);
                $incomeFebYear      = FinancialTransaction::getMarginByRange($febStartDateYear, $febEndDateYear);
                $incomeFebLastYear  = FinancialTransaction::getMarginByRange($febStartDateLastYear, $febEndDateLastYear);
                $incomeMarYear      = FinancialTransaction::getMarginByRange($marStartDateYear, $marEndDateYear);
                $incomeMarLastYear  = FinancialTransaction::getMarginByRange($marStartDateLastYear, $marEndDateLastYear);
                $incomeAprYear      = FinancialTransaction::getMarginByRange($aprStartDateYear, $aprEndDateYear);
                $incomeAprLastYear  = FinancialTransaction::getMarginByRange($aprStartDateLastYear, $aprEndDateLastYear);
                $incomeMayYear      = FinancialTransaction::getMarginByRange($mayStartDateYear, $mayEndDateYear);
                $incomeMayLastYear  = FinancialTransaction::getMarginByRange($mayStartDateLastYear, $mayEndDateLastYear);
                $incomeJunYear      = FinancialTransaction::getMarginByRange($junStartDateYear, $junEndDateYear);
                $incomeJunLastYear  = FinancialTransaction::getMarginByRange($junStartDateLastYear, $junEndDateLastYear);
                $incomeJulYear      = FinancialTransaction::getMarginByRange($julStartDateYear, $julEndDateYear);
                $incomeJulLastYear  = FinancialTransaction::getMarginByRange($julStartDateLastYear, $julEndDateLastYear);
                $incomeAugYear      = FinancialTransaction::getMarginByRange($augStartDateYear, $augEndDateYear);
                $incomeAugLastYear  = FinancialTransaction::getMarginByRange($augStartDateLastYear, $augEndDateLastYear);
                $incomeSepYear      = FinancialTransaction::getMarginByRange($sepStartDateYear, $sepEndDateYear);
                $incomeSepLastYear  = FinancialTransaction::getMarginByRange($sepStartDateLastYear, $sepEndDateLastYear);
                $incomeOctYear      = FinancialTransaction::getMarginByRange($octStartDateYear, $octEndDateYear);
                $incomeOctLastYear  = FinancialTransaction::getMarginByRange($octStartDateLastYear, $octEndDateLastYear);
                $incomeNovYear      = FinancialTransaction::getMarginByRange($novStartDateYear, $novEndDateYear);
                $incomeNovLastYear  = FinancialTransaction::getMarginByRange($novStartDateLastYear, $novEndDateLastYear);
                $incomeDecYear      = FinancialTransaction::getMarginByRange($decStartDateYear, $decEndDateYear);
                $incomeDecLastYear  = FinancialTransaction::getMarginByRange($decStartDateLastYear, $decEndDateLastYear);
            //!Monthly Income
        //!Bulanan

        //Tahunan
            $totalInvPaidYear       = Invoice::getInvPaid($startingYear, $today);   
            $totalInvPaidLastYear   = Invoice::getInvPaid($startingLastYear, $endingLastYear);   
        //!Tahunan

        //Geting Saldo Sisa This Month
          $incomeForSisa      = FinancialTransaction::getIncomeRangeAmount($starting, $today);
          $outcomeForSisa     = FinancialTransaction::getRangeOutTransonAmount($starting, $today);
          $topupForSisa       = FinancialTransaction::getWeeklyTopUpAmount($starting, $today);
          $setorKasForSisa    = FinancialTransaction::getWeeklySetorKasAmount($starting, $today);
          $sisaSaldo          = $incomeForSisa+$topupForSisa-$outcomeForSisa-$setorKasForSisa;          
        //!Geting Saldo Sisa This Month
        
        //Geting Saldo Sisa This Month
          $incomeForSisaPast  = FinancialTransaction::getIncomeRangeAmount($starting, $today);
          $outcomeForSisaPast = FinancialTransaction::getRangeOutTransonAmount($starting, $today);
          $topupForSisaPast   = FinancialTransaction::getWeeklyTopUpAmount($starting, $today);
          $setorKasForPast    = FinancialTransaction::getWeeklySetorKasAmount($starting, $today);
          $sisaSaldoPast      = $incomeForSisaPast+$topupForSisaPast-$outcomeForSisaPast-$setorKasForPast;          
        //!Geting Saldo Sisa This Month

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
            //!Income      
            
          
        //Data Mingguan
            'incomeWeekly'   => $incomeWeekly,
            'incomeLastWeek' => $incomelastWeek,            
            'incomeWeek1'    => $incomeWeek1,
            'incomeWeek2'    => $incomeWeek2,
            'incomeWeek3'    => $incomeWeek3,
            'incomeWeek4'    => $incomeWeek4,
            
            'incPastWeek1'   => $incPastWeek1,
            'incPastWeek2'   => $incPastWeek2,
            'incPastWeek3'   => $incPastWeek3,
            'incPastWeek4'   => $incPastWeek4,
            
            'marginWeek1'    => $marginWeek1,
            'marginWeek2'    => $marginWeek2,
            'marginWeek3'    => $marginWeek3,
            'marginWeek4'    => $marginWeek4,
            
            'margPastWeek1'  => $margPastWeek1,
            'margPastWeek2'  => $margPastWeek2,
            'margPastWeek3'  => $margPastWeek3,
            'margPastWeek4'  => $margPastWeek4,

            
        //!Data Mingguan
        
        // Data Bulanan
            'bulan'             => $bulan,
            'bulanLalu'         => $bulanLalu,
            'duaBulanLalu'      => $duaBulanLalu,
            'totalInvMouthly'   => $totalInvMouthly,
            
            'sisaSaldo'         => $sisaSaldo,
            'sisaSaldoPast'     => $sisaSaldoPast,

            'incomeThisMonth'   => $incomeThisMonth,
            'incomeLastMonth'   => $incomeLastMonth,
            'incomweToMonth'    => $incomeTwoMonth,
            
            'operationalThisMo' => $operationalThisMo,
            'operationalLastMo' => $operationalLastMo,

            'totalInvPaid'      => $totalInvPaid,
            'totalBonMonthly'   => $totalBonMonthly,

            'setorKasMonthly'   => $setorKasMonthly,
            'setorKasLastMonth' => $setorKasLastMonth,

            'topUpMonthly'      => $topUpMonthly,
            'topUpLastMonth'    => $topUpLastMonth,
            
            'outcomeMountly'    => $outcomeMountly,
            'outcomeLastMount'  => $outcomeLastMount,

            'marginMonthly'     => $marginMonthly,
            'marginLastMonth'   => $marginLastMonth,
            //icomeMonthlyYear
                'incomeJanYear'  => $incomeJanYear, 
                'incomeFebYear'  => $incomeFebYear, 
                'incomeMarYear'  => $incomeMarYear, 
                'incomeAprYear'  => $incomeAprYear, 
                'incomeMayYear'  => $incomeMayYear, 
                'incomeJunYear'  => $incomeJunYear, 
                'incomeJulYear'  => $incomeJulYear, 
                'incomeAugYear'  => $incomeAugYear, 
                'incomeSepYear'  => $incomeSepYear, 
                'incomeOctYear'  => $incomeOctYear, 
                'incomeNovYear'  => $incomeNovYear, 
                'incomeDecYear'  => $incomeDecYear, 
            //!icomeMonthlyYear
        //! Data Bulanan 
        
        // Data Tahunan 
            'currentYear'           => $currentYear,
            'startingYear'          => $startingYear,
            'endingYear'          => $endingYear,
            'totalInvPaidYear'      => $totalInvPaidYear,
            'totalInvPaidLastYear'  => $totalInvPaidLastYear,
        //! Data Tahunan 
        
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
