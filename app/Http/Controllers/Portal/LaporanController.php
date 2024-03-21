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


class LaporanController extends Controller
{
    public function laporanHarian(Request $request)
    {
        //Sistem
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
        //!Sistem  
        
        //Date
            $starting       = Carbon::createFromDate(2023, 12, 1);
            $startDate      = $request->input('startDate');
            $endDate        = $request->input('endDate');
            
            if (!$startDate || !$endDate) {
                $startDate  = now()->startOfDay();
                $endDate    = now()->endOfDay();
            } else {
                $startDate  = Carbon::parse($startDate)->startOfDay();
                $endDate    = Carbon::parse($endDate)->endOfDay();
            }
            $today          = Carbon::now();
            $yesterday      = $startDate->copy()->subDay();         
            $startMonth     = $startDate->copy()->startOfMonth();
            $endMonth       = $startMonth->copy()->endOfMonth();       
            $startYear      = $startDate->copy()->startOfYear();
            $endYear        = $startYear->copy()->endOfYear();   
        //!Date

        $incomeTotal            = FinancialTransaction::getIncomeRangeAmount($startDate, $endDate);
        $outcomeTotal           = FinancialTransaction::getRangeOutTransonAmount($startDate, $endDate);
        $topup                  = FinancialTransaction::getWeeklyTopUpAmount($startDate, $endDate);
        $saldoKas               = FinancialTransaction::getWeeklySetorKasAmount($startDate, $endDate);
        
        $incomeData             = FinancialTransaction::getIncomeByRange($startDate, $endDate);
        $outcomeData            = FinancialTransaction::getOutcomeByRange($startDate, $endDate);
        $setorData              = FinancialTransaction::getSetor($startDate, $endDate);
        $topupData              = FinancialTransaction::getTopup($startDate, $endDate);
        
        $invoiceBon             = Invoice::getBon($startDate, $endDate);
        $allInvoiceData         = Invoice::getInv($startDate, $endDate);
        $panjarInvoiceData      = Invoice::getInvPJ($startDate, $endDate);
        $bbInvoiceData          = Invoice::getInvBB($startDate, $endDate);
        $countInvoice           = Invoice::getInv($startDate, $endDate)->count();
        $countLuns              = Invoice::getInvLN($startDate, $endDate)->count();
        $countPanjar            = Invoice::getInvPJ($startDate, $endDate)->count();
        $countInvBB             = Invoice::getInvBB($startDate, $endDate)->count();
        $tempoInv               = Invoice::getInv($starting, $endDate)
                                ->where('due_date', '<=', $today)
                                ->where('status', '!=', 2)
                                ->count();


        //Geting Saldo Sisa
            $incomeForSisa      = FinancialTransaction::getIncomeRangeAmount($starting, $yesterday);
            $outcomeForSisa     = FinancialTransaction::getRangeOutTransonAmount($starting, $yesterday);
            $topupForSisa       = FinancialTransaction::getWeeklyTopUpAmount($starting, $yesterday);
            $setorKasForSisa    = FinancialTransaction::getWeeklySetorKasAmount($starting, $yesterday);
            $sisaBefore         = $incomeForSisa+$topupForSisa-$outcomeForSisa-$setorKasForSisa;          
        //!Geting Saldo Sisa

        $data = [
            //Config
                'title'                     => 'Laporan',
                'subtitle'                  => 'Harian',
                'user'                      => $user,
                'role'                      => $roleData,
                'menus'                     => $menus,
                'subMenus'                  => $subMenus,
                'childSubMenus'             => $childSubMenus,
                'startDate'                 => $startDate,
                'startMonth'                => $startMonth,
                'endMonth'                  => $endMonth,
                'startYear'                 => $startYear,
                'endYear'                   => $endYear,
            //!Config

            //Keuangan
                'incomeTotal'               => $incomeTotal,
                'sisaBefore'                => $sisaBefore,
                'outcomeTotal'              => $outcomeTotal,
                'topup'                     => $topup,
                'saldoKas'                  => $saldoKas,
                'invoiceBon'                => $invoiceBon,
                
                'incomeData'                => $incomeData,
                'outcomeData'               => $outcomeData,
                'setorData'                 => $setorData,
                'topupData'                 => $topupData,
            
            //!Keuangan
            
            //Invoice
                'allInvoiceData'            => $allInvoiceData,
                'countLuns'                 => $countLuns,
                'countInvoice'              => $countInvoice,
                'countPanjar'               => $countPanjar,
                'countInvBB'                => $countInvBB,
                'tempoInv'                  => $tempoInv,
            //!Invoice
        ];
    
        return view('Konten/Laporan/harian', $data);
    }
    
    public function laporanBulanan(Request $request)
    {
        //Sistem
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
        //!Sistem  
        
        //Date
            $starting       = Carbon::createFromDate(2023, 12, 1);
            $startDate      = $request->input('startDate');
            $endDate        = $request->input('endDate');
            
            if (!$startDate || !$endDate) {
                $startDate  = now()->startOfDay();
                $endDate    = now()->endOfDay();
            } else {
                $startDate  = Carbon::parse($startDate)->startOfDay();
                $endDate    = Carbon::parse($endDate)->endOfDay();
            }
            
            $today          = Carbon::now();
            $yesterday      = $startDate->copy()->subDay();
            $selectedMonth  = $startDate->month;
            $monthName      = $startDate->locale('id')->monthName;
            $startYear      = $startDate->copy()->startOfYear();
            $endYear        = $startYear->copy()->endOfYear();   
        //!Date

        $incomeTotal            = FinancialTransaction::getIncomeRangeAmount($startDate, $endDate);
        $outcomeTotal           = FinancialTransaction::getRangeOutTransonAmount($startDate, $endDate);
        $topup                  = FinancialTransaction::getWeeklyTopUpAmount($startDate, $endDate);
        $saldoKas               = FinancialTransaction::getWeeklySetorKasAmount($startDate, $endDate);
        $invoiceBon             = Invoice::getBon($startDate, $endDate);

        $incomeData             = FinancialTransaction::getIncomeByRange($startDate, $endDate);
        $outcomeData            = FinancialTransaction::getOutcomeByRange($startDate, $endDate);
        $setorData              = FinancialTransaction::getSetor($startDate, $endDate);
        $topupData              = FinancialTransaction::getTopup($startDate, $endDate);

        $invoiceBon             = Invoice::getBon($startDate, $endDate);
        $allInvoiceData         = Invoice::getInv($startDate, $endDate);
        $panjarInvoiceData      = Invoice::getInvPJ($startDate, $endDate);
        $bbInvoiceData          = Invoice::getInvBB($startDate, $endDate);
        $countInvoice           = Invoice::getInv($startDate, $endDate)->count();
        $countLuns              = Invoice::getInvLN($startDate, $endDate)->count();
        $countPanjar            = Invoice::getInvPJ($startDate, $endDate)->count();
        $countInvBB             = Invoice::getInvBB($startDate, $endDate)->count();
        $tempoInv               = Invoice::getInv($starting, $endDate)
                                ->where('due_date', '<=', $today)
                                ->where('status', '!=', 2)
                                ->count();

        //Geting Saldo Sisa
            $incomeForSisa      = FinancialTransaction::getIncomeRangeAmount($starting, $yesterday);
            $outcomeForSisa     = FinancialTransaction::getRangeOutTransonAmount($starting, $yesterday);
            $topupForSisa       = FinancialTransaction::getWeeklyTopUpAmount($starting, $yesterday);
            $setorKasForSisa    = FinancialTransaction::getWeeklySetorKasAmount($starting, $yesterday);
            $sisaBefore         = $incomeForSisa+$topupForSisa-$outcomeForSisa-$setorKasForSisa;          
        //!Geting Saldo Sisa

        $data = [
            //Config
                'title'                     => 'Laporan',
                'subtitle'                  => 'Bulanan',
                'user'                      => $user,
                'role'                      => $roleData,
                'menus'                     => $menus,
                'subMenus'                  => $subMenus,
                'childSubMenus'             => $childSubMenus,
                'today'                     => $today,
                'startDate'                 => $startDate,
                'dateStarUnduh'             => $startDate,
                'dateEndUnduh'              => $endDate,
                'selectedMonth'             => $selectedMonth,
                'monthName'                 => $monthName,
                'startYear'                 => $startYear,
                'endYear'                   => $endYear,
            //!Config

            //Keuangan
                'incomeTotal'               => $incomeTotal,
                'sisaBefore'                => $sisaBefore,
                'outcomeTotal'              => $outcomeTotal,
                'topup'                     => $topup,
                'saldoKas'                  => $saldoKas,
                'invoiceBon'                => $invoiceBon,

                'incomeData'                => $incomeData,
                'outcomeData'               => $outcomeData,
                'setorData'                 => $setorData,
                'topupData'                 => $topupData,
            
            //!Keuangan

            //Invoice
                'allInvoiceData'            => $allInvoiceData,
                'countLuns'                 => $countLuns,
                'countInvoice'              => $countInvoice,
                'countPanjar'               => $countPanjar,
                'countInvBB'                => $countInvBB,
                'tempoInv'                  => $tempoInv,
            //!Invoice
        ];
    
        return view('Konten/Laporan/bulanan', $data);
    }
    
    public function laporanTahunan(Request $request)
    {
        //Sistem
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
        //!Sistem  
        
        //Date
            $starting       = Carbon::createFromDate(2023, 12, 1);
            $startDate      = $request->input('startDate');
            $endDate        = $request->input('endDate');
            
            if (!$startDate || !$endDate) {
                $startDate  = now()->startOfDay();
                $endDate    = now()->endOfDay();
            } else {
                $startDate  = Carbon::parse($startDate)->startOfDay();
                $endDate    = Carbon::parse($endDate)->endOfDay();
            }
            
            $today          = Carbon::now();
            $yesterday      = $startDate->copy()->subDay();
            $selectedYear   = $startDate->year;
            $monthName      = $startDate->locale('id')->monthName;
            $startOfMonth   = $today->copy()->startOfMonth();
            $endOfMonth     = $today->copy()->endOfMonth();
        //!Date

        $incomeTotal            = FinancialTransaction::getIncomeRangeAmount($startDate, $endDate);
        $outcomeTotal           = FinancialTransaction::getRangeOutTransonAmount($startDate, $endDate);
        $topup                  = FinancialTransaction::getWeeklyTopUpAmount($startDate, $endDate);
        $saldoKas               = FinancialTransaction::getWeeklySetorKasAmount($startDate, $endDate);
        $invoiceBon             = Invoice::getBon($startDate, $endDate);

        $incomeData             = FinancialTransaction::getIncomeByRange($startDate, $endDate);
        $outcomeData            = FinancialTransaction::getOutcomeByRange($startDate, $endDate);
        $setorData              = FinancialTransaction::getSetor($startDate, $endDate);
        $topupData              = FinancialTransaction::getTopup($startDate, $endDate);

        $invoiceBon             = Invoice::getBon($startDate, $endDate);
        $allInvoiceData         = Invoice::getInv($startDate, $endDate);
        $panjarInvoiceData      = Invoice::getInvPJ($startDate, $endDate);
        $bbInvoiceData          = Invoice::getInvBB($startDate, $endDate);
        $countInvoice           = Invoice::getInv($startDate, $endDate)->count();
        $countLuns              = Invoice::getInvLN($startDate, $endDate)->count();
        $countPanjar            = Invoice::getInvPJ($startDate, $endDate)->count();
        $countInvBB             = Invoice::getInvBB($startDate, $endDate)->count();
        $tempoInv               = Invoice::getInv($starting, $endDate)
                                ->where('due_date', '<=', $today)
                                ->where('status', '!=', 2)
                                ->count();

        //Geting Saldo Sisa
            $incomeForSisa      = FinancialTransaction::getIncomeRangeAmount($starting, $yesterday);
            $outcomeForSisa     = FinancialTransaction::getRangeOutTransonAmount($starting, $yesterday);
            $topupForSisa       = FinancialTransaction::getWeeklyTopUpAmount($starting, $yesterday);
            $setorKasForSisa    = FinancialTransaction::getWeeklySetorKasAmount($starting, $yesterday);
            $sisaBefore         = $incomeForSisa+$topupForSisa-$outcomeForSisa-$setorKasForSisa;          
        //!Geting Saldo Sisa

        $data = [
            //Config
                'title'                     => 'Laporan',
                'subtitle'                  => 'Tahunan',
                'user'                      => $user,
                'role'                      => $roleData,
                'menus'                     => $menus,
                'subMenus'                  => $subMenus,
                'childSubMenus'             => $childSubMenus,
                'today'                     => $today,
                'startDate'                 => $startDate,
                'dateStarUnduh'             => $startDate,
                'dateEndUnduh'              => $endDate,
                'startOfMonth'              => $startOfMonth,
                'endOfMonth'                => $endOfMonth,            
                'selectedYear'              => $selectedYear,
                'monthName'                 => $monthName,
            //!Config

            //Keuangan
                'incomeTotal'               => $incomeTotal,
                'sisaBefore'                => $sisaBefore,
                'outcomeTotal'              => $outcomeTotal,
                'topup'                     => $topup,
                'saldoKas'                  => $saldoKas,
                'invoiceBon'                => $invoiceBon,

                'incomeData'                => $incomeData,
                'outcomeData'               => $outcomeData,
                'setorData'                 => $setorData,
                'topupData'                 => $topupData,
            
            //!Keuangan

             //Invoice
                'allInvoiceData'            => $allInvoiceData,
                'countLuns'                 => $countLuns,
                'countInvoice'              => $countInvoice,
                'countPanjar'               => $countPanjar,
                'countInvBB'                => $countInvBB,
                'tempoInv'                  => $tempoInv,
            //!Invoice
        ];
    
        return view('Konten/Laporan/tahunan', $data);
    }

}
