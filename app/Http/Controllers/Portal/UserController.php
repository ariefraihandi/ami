<?php

namespace App\Http\Controllers\Portal;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
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
use App\Models\User;
use App\Models\Tagihan;
use Jenssegers\Agent\Facades\Agent;

class UserController extends Controller
{
    public function getAllUser()
    {
        $customers = User::all();

        $formattedCustomers = $customers->map(function ($customer) {
            // Mendapatkan nilai wa dari database
            $wa = $customer->wa;
        
            // Jika nilai wa diawali dengan '08', hapus '0' dan tambahkan '+62'
            if (substr($wa, 0, 2) === '08') {
                $wa = '62' . substr($wa, 1);
            }
        
            // Jika nilai wa belum diawali dengan '+62' dan diawali dengan '8', tambahkan '+62'
            if (substr($wa, 0, 3) === '8') {
                $wa = '62' . $wa;
            }
                       
            $statusBadge = '';
            switch ($customer->status) {
                case 1:
                    $statusBadge = '<span class="badge bg-success">Karyawan Tetap</span>';
                    break;
                case 2:
                    $statusBadge = '<span class="badge bg-primary">Karyawan Harian</span>';
                    break;
                default:
                    $statusBadge = '<span class="badge bg-danger">Inactive</span>';
                    break;
            }
        
            return [
                'id'            => $customer->id,
                'name'          => $customer->name,
                'email'         => $customer->email,
                'username'      => $customer->username,
                'wa'            => $wa,
                'status'        => $statusBadge, // Gunakan badge HTML sesuai dengan nilai status
                'created_at'    => $customer->created_at->format('Y-m-d H:i:s'), 
                'address'       => $customer->address,
                'role'          => $customer->role,
                'image'         => $customer->image,
                'address'       => $customer->address,
                'birth'         => $customer->date_of_birth,
            ];
        });
        

        return response()->json(['data' => $formattedCustomers]);
    }

//View
    public function index(Request $request)
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
          
            return redirect('/');
        }
        dd($user);
        $accessMenus        = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus     = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren     = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        $menus              = Menu::whereIn('id', $accessMenus)->get();
        $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
        // dd($childSubMenus);
        $roleData           = UserRole::where('id', $user->role)->first();
        $userActivities     = UserActivity::where('user_id', $user->id)->get();

        $latestLoginActivity = UserActivity::where('user_id', $user->id)
        ->where('activity', 'Logged in')
        ->latest()
        ->first();

        $additionalData = [
            'title'                     => 'User',
            'subtitle'                  => 'Profile',
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'user'                      => $user,
            'role'                      => $roleData,
            'userActivities'            => $userActivities,
            'latestLoginActivity'       => $latestLoginActivity,
        ];
    
        return view('Konten/User/profile', $additionalData);
    }
   
    public function showPayroll(Request $request)
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
          
            return redirect('/');
        }

        dd($user);
        $bulanIni           = date('Y-m');
        $accessMenus        = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus     = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren     = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        $menus              = Menu::whereIn('id', $accessMenus)->get();
        $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData           = UserRole::where('id', $user->role)->first();

        $userActivities     = UserActivity::where('user_id', $user->id)->get();
        
        $ambilan            = FinancialTransaction::where('source_receiver', 'Ambilan')
                            ->where('reference_number', 'LIKE', 'ab' . $user->id . '_%')
                            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$bulanIni])
                            ->sum('transaction_amount');

        $totalBonus         = FinancialTransaction::where('source_receiver', 'Bonus')
                            ->where('reference_number', 'LIKE', 'bs' . $user->id . '_%')
                            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$bulanIni])
                            ->sum('transaction_amount');

        $riwayatKeuangan    = FinancialTransaction::where(function ($query) use ($user) {
                            $query->where('reference_number', 'LIKE', 'ab' . $user->id . '_%')
                            ->orWhere('reference_number', 'LIKE', 'bs' . $user->id . '_%');
                            })
                            ->orderByDesc('created_at')
                            ->get();
                            

        $additionalData = [
            'title'                     => 'User',
            'subtitle'                  => 'Profile',
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'user'                      => $user,
            'role'                      => $roleData,
            'userActivities'            => $userActivities,
            'ambilan'                   => $ambilan,
            'riwayatKeuangan'           => $riwayatKeuangan,
            'totalBonus'           => $totalBonus,
        ];
    
        return view('Konten/User/payroll', $additionalData);
    }
   
    public function showSetting(Request $request)
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
          
            return redirect('/');
        }
        $bulanIni           = date('Y-m');
        $accessMenus        = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus     = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren     = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        $menus              = Menu::whereIn('id', $accessMenus)->get();
        $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData           = UserRole::where('id', $user->role)->first();

        $userActivities     = UserActivity::where('user_id', $user->id)->get();
        $user_id            = session('user_id');
        $usersData          = User::find($user_id);
        
                            

        $additionalData = [
            'title'                     => 'User',
            'subtitle'                  => 'Profile',
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'user'                      => $user,
            'usersData'                      => $usersData,
            'role'                      => $roleData,
            'userActivities'            => $userActivities,
        ];
    
        return view('Konten/User/setting', $additionalData);
    }
    
    public function showSecurity(Request $request)
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
          
            return redirect('/');
        }
        $bulanIni           = date('Y-m');
        $accessMenus        = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus     = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren     = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        $menus              = Menu::whereIn('id', $accessMenus)->get();
        $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData           = UserRole::where('id', $user->role)->first();

        $userActivities     = UserActivity::where('user_id', $user->id)->get();
        $user_id            = session('user_id');
        $usersData          = User::find($user_id);

        $additionalData = [
            'title'                     => 'User',
            'subtitle'                  => 'Profile',
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'user'                      => $user,
            'usersData'                 => $usersData,
            'role'                      => $roleData,
            'userActivities'            => $userActivities,
        ];
    
        return view('Konten/User/security', $additionalData);
    }

    //Admin
        public function showUserAdminIndex(Request $request)
        {
            //Check Access
                $requestedUrl = $request->path();      
                $urlParts = explode('/', $requestedUrl);
                $urlPart = $urlParts[0]; // Ambil bagian pertama dari URL    
                $menuSub = MenuSub::where('url', $urlPart)->first();
                
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

            $accessMenus        = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
            $accessSubmenus     = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
            $accessChildren     = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
        
            $menus              = Menu::whereIn('id', $accessMenus)->get();
            $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
            $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
            $roleData           = UserRole::where('id', $user->role)->first();
            $users              = User::all();

            $additionalData = [
                'title'                     => 'Admin',
                'subtitle'                  => 'Users',
                'user'                      => $user,
                'users'                     => $users,
                'role'                      => $roleData,
                'menus'                     => $menus,
                'subMenus'                  => $subMenus,
                'childSubMenus'             => $childSubMenus,
            
            
            ];
            
            return view('Konten/User/adminUsers', $additionalData);
        }     

        public function showRoleList(Request $request)
        {
            //Check Access
                $requestedUrl = $request->path();      
                $urlParts = explode('/', $requestedUrl);
                $urlPart = $urlParts[0]; // Ambil bagian pertama dari URL    
                $menuSub = MenuSub::where('url', $urlPart)->first();
                
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

            $accessMenus        = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
            $accessSubmenus     = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
            $accessChildren     = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
        
            $menus              = Menu::whereIn('id', $accessMenus)->get();
            $subMenus           = MenuSub::whereIn('id', $accessSubmenus)->get();
            $childSubMenus      = MenuSubsChild::whereIn('id', $accessChildren)->get();
            $roleData           = UserRole::where('id', $user->role)->first();
            $allRole            = UserRole::all();
            $allMenu            = Menu::all();
            $allMenuSub         = MenuSub::orderBy('menu_id')->get();
            $allChildSub        = MenuSubsChild::orderBy('id_submenu')->get();

            $additionalData = [
                'title'                     => 'User Admin',
                'subtitle'                  => 'Role Access',
                'user'                      => $user,
                'allRole'                   => $allRole,
                'allMenu'                   => $allMenu,
                'allMenuSub'                => $allMenuSub,
                'allChildSub'               => $allChildSub,
                'role'                      => $roleData,
                'menus'                     => $menus,
                'subMenus'                  => $subMenus,
                'childSubMenus'             => $childSubMenus,            
            ];
            
            return view('Konten/User/adminUsersAccess', $additionalData);
        }
    //!Admin
//!View

    public function addUsers(Request $request)
    {        
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'username' => 'required|string|max:255',
                'password' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'jabatan' => 'required',
                'wa' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'salary' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'status' => 'required',
                'token' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Jika validasi gagal, kembalikan pesan error
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Manipulasi data jika diperlukan sebelum disimpan ke database
            $data = $request->all();
            // Contoh: Hashing password
            $data['password'] = bcrypt($data['password']);

            // Membersihkan nilai salary
            if (isset($data['salary'])) {
                $data['salary'] = $this->cleanNumericInput($data['salary']);
            }

            // Menambahkan inputan jabatan ke dalam array data
            $data['jabatan'] = $request->jabatan;

            // Memodifikasi nilai 'wa'
            if (isset($data['wa'])) {
                $wa = $data['wa'];
                if (substr($wa, 0, 2) === '08') {
                    // Jika diawali dengan '08', hapus '0' dan tambahkan '+62'
                    $data['wa'] = '62' . substr($wa, 1);
                } elseif (substr($wa, 0, 1) === '8') {
                    // Jika diawali dengan '8', tambahkan '+62' di awal
                    $data['wa'] = '62' . $wa;
                }
            }

            $user = User::create($data);

            Tagihan::create([
                'id_tagih' => $user->id,
                'nama_tagihan' => $user->name,
                'jenis_tagihan' => '1',
                'jumlah_tagihan' => $user->salary,
                'start_tagihan' => Carbon::create(2024, 1, 1)->toDateString(),
                'status' => '0', 
                'masa_kerja' => '0', 
                'tagihan_ke' => null,
                'sampai_ke' => null,
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
            
                // Tentukan lokasi penyimpanan foto
                $destinationPath = 'assets/img/staff'; // Lokasi yang diinginkan
            
                // Simpan foto di lokasi yang ditentukan
                $image->move($destinationPath, $imageName);
            
                // Simpan nama file foto ke dalam database
                $user->image = $imageName;
                $user->save();
            } else {
                // Jika tidak ada gambar diunggah, gunakan default.webp
                $user->image = 'default.webp';
                $user->save();
            }

            DB::commit(); // Commit transaksi jika berhasil

            // SweetAlert success message
            return redirect()->back()->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'User berhasil ditambahkan.',
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollback(); // Lakukan rollback jika terjadi kesalahan

            $errorMessage = $e->getMessage();

            // SweetAlert error message dengan pesan kesalahan dari Exception
            return redirect()->back()->withInput()->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Terjadi kesalahan saat menambahkan user. Error: ' . $errorMessage,
                ],
            ]);
        }    
    }   

    public function uploadAvatar(Request $request)
    {
        // Validasi permintaan
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:800',
        ]);

        $device = Agent::device();
        $platform = Agent::platform();
        $browser = Agent::browser();

        $avatar = $request->file('avatar');
        $imageName = $avatar->getClientOriginalName(); // Gunakan nama file asli untuk menyimpan

        // Periksa apakah gambar pengguna saat ini adalah gambar default
        $user = auth()->user();
        if ($user->image !== 'default.webp') {
            // Jika bukan gambar default, hapus gambar lama sebelum menyimpan yang baru
            $oldImagePath = public_path('assets/img/staff') . '/' . $user->image;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Hapus gambar lama
            }
        }

        // Simpan file ke direktori yang sesuai (misalnya, public/img/staff)
        $avatar->move(public_path('assets/img/staff'), $imageName);

        $user->image = $imageName;
        $user->save();

        // Buat objek UserActivity baru
        $adminActivity = new UserActivity([
            'user_id' => auth()->id(),
            'activity' => 'Change image',
            'ip_address' => 'Mengganti foto profile',
            'device_info' => "$device $platform $browser",
        ]);

        // Simpan aktivitas
        $adminActivity->save();

        $successMessage = 'Foto Profil Berhasil Diperbaharui';

        return redirect()->route('user.setting')->with([
            'response' => [
                'success' => true,
                'title' => 'Success',
                'message' => $successMessage,
            ],
        ]);
    }

    public function update(Request $request)
    {
        // Validasi request
        $request->validate([
            'id' => 'required|numeric',
        ]);

        // Cari user berdasarkan ID
        $user = User::find($request->id);

        // Jika user tidak ditemukan, kembalikan response dengan pesan error
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Update data user
        if (!empty($request->name)) {
            $user->name = $request->name;
        }
        if (!empty($request->email)) {
            $user->email = $request->email;
        }
        if (!empty($request->username)) {
            $user->username = $request->username;
        }
        if (!empty($request->wa)) {
            $user->wa = $request->wa;
        }
        if (!empty($request->role)) {
            $user->role = $request->role;
        }
        if (!empty($request->jabatan)) {
            $user->jabatan = $request->jabatan;
        }
        if (!empty($request->status)) {
            $user->status = $request->status;
        }
        if (!empty($request->gaji)) {
            $user->salary = $this->cleanNumericInput($request->gaji);
        }
        if (!empty($request->date_of_birth)) {
            $user->date_of_birth = $request->date_of_birth;
        }
        if (!empty($request->created_at)) {
            $user->created_at = $request->created_at;
        }

        $user->save();

        $response = [
            'success' => true,
            'title' => 'Berhasil',
            'message' => "Data Berhasil Dirubah.",
        ];
        
        return redirect()->back()->with('response', $response);
    }

    public function changePassword(Request $request)
    {
        // Validate the request data
        $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6',
            'confirmPassword' => 'required|same:newPassword',
        ]);
    
        // Retrieve the authenticated user
        $user = Auth::user();
    
        // Check if the provided old password matches the user's current password
        if (!\Hash::check($request->oldPassword, $user->password)) {           
            $response = [
                'success' => false,
                'title' => 'Gagal',
                'message' => "Password Lama Tidak Cocok.",
            ];

            return redirect()->route('user.security')->with('response', $response);
        }
    
        // Check if the new password and confirm password match
        if ($request->newPassword !== $request->confirmPassword) {
            $response = [
                'success' => false,
                'title' => 'Gagal',
                'message' => "Password Tidak Cocok.",
            ];

            return redirect()->route('user.security')->with('response', $response);
        }
    
        // Update the user's password
        $user->password = \Hash::make($request->newPassword);
        $user->save();
        $response = [
            'success' => true, // Atau false tergantung dari keberhasilan operasi
            'title' => 'Success', // Judul pesan SweetAlert
            'message' => 'Data berhasil disimpan.', // Pesan yang ingin ditampilkan
        ];
        return redirect()->route('user.security')->with('response', $response);
    }
    
    

    public function deleteUser(Request $request)
    {
        try {
            // Retrieve the ID from the query parameters
            $id = $request->input('id');
    
            // Delete the menu with the specified ID
            User::where('id', $id)->delete();
    
            // Delete the associated access menu records          
    
            $successMessage = 'Karyawan Berhasil Dihapus';
    
            return redirect()->route('users')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => $successMessage,
                ],
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to delete Role records. ' . $e->getMessage();
    
            return redirect()->route('users')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => $errorMessage,
                ],
            ]);
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
