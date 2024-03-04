<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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

            $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
            $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
            $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');

            $menus = Menu::whereIn('id', $accessMenus)->get();
            $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
            $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();
            $roleData = UserRole::where('id', $user->role)->first();
        //!Syistem

        $today      = Carbon::today();
        $yesterday  = $today->copy()->subDay(); 

        $income     = FinancialTransaction::whereDate('transaction_date', $today)
                    ->whereIn('status', [1, 2, 3])
                    ->get();

        $incomeYes  = FinancialTransaction::whereDate('transaction_date', $yesterday)
                    ->whereIn('status', [1, 2, 3])
                    ->get();



        $incomeTotal        = $income->sum('transaction_amount');
        $incomeTotalYes     = $incomeYes->sum('transaction_amount');

        $data = [
            'title'                 => 'Customer List',
            'subtitle'              => 'Dashboard',
            'user'                  => $user,
            'role'                  => $roleData,
            'menus'                 => $menus,
            'subMenus'              => $subMenus,
            'childSubMenus'         => $childSubMenus,
            'income'                => $incomeTotal,
            'incomeTotalYes'        => $incomeTotalYes,
         
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
