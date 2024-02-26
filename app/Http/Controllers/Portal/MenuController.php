<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
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

class MenuController extends Controller
{

    //menus
    public function getAllMenus()
    {
        try {
            $menus = Menu::all();

            return response()->json(['data' => $menus]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showMenusIndex(Request $request)
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
          
            return redirect('/');
        }

        $menu           = Menu::all();
        $menusub        = MenuSub::all();
        $menuchild      = MenuSubsChild::all();
        $role           = UserRole::all();
        $menuCount      = $menu->count();
        $menusubCount   = $menusub->count();
        $menuchildCount = $menuchild->count();
        $roleCount      = $role->count();

        $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        // Mengambil data berdasarkan hak akses
        $menus = Menu::whereIn('id', $accessMenus)->get();
        $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData = UserRole::where('id', $user->role)->first();

        $additionalData = [
            'title'                 => 'Menu',
            'subtitle'              => 'List',
            'user'                  => $user,
            'role'                  => $roleData,
            'menus'                 => $menus,
            'subMenus'              => $subMenus,
            'childSubMenus'         => $childSubMenus,
            'menu'                  => $menu,
            'menusub'               => $menusub,
            'menuchild'             => $menuchild,
            'menuCount'             => $menuCount,
            'menusubCount'          => $menusubCount,
            'menuchildCount'        => $menuchildCount,
            'roleCount'             => $roleCount,
        ];   
        return view('Konten/Menu/menu-index', $additionalData);
    }

    public function addMenu(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'menu_name' => 'required|string|max:255',
                // Add any other validation rules you may need
            ]);

            // Fetch the last order value from the database and increment it
            $lastOrder = Menu::max('order');
            $order = $lastOrder + 1;

            // Create a new menu
            $menu = new Menu([
                'menu_name' => $request->input('menu_name'),
                'order' => $order,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            // Save the menu
            $menu->save();
           
            $userMenu = new AccessMenu([
                'user_id' => 1,
                'menu_id' => $menu->id, 
            ]);

            // Save the user_menu record
            $userMenu->save();

            // Flash success response to the session
            return redirect()->route('menus.index')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'Menu added successfully',
                ],
            ]);
        } catch (\Exception $e) {
            // Flash error response to the session
            return redirect()->route('menus.index')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Failed to add menu. ' . $e->getMessage(),
                ],
            ]);
        }
    }

    public function deleteMenu(Request $request)
    {
        try {
            // Retrieve the ID from the query parameters
            $id = $request->input('id');
    
            // Delete the menu with the specified ID
            Menu::where('id', $id)->delete();
    
            // Delete the associated access menu records
            AccessMenu::where('menu_id', $id)->delete();
    
            $successMessage = 'Menu and associated access menu records deleted successfully';
    
            return redirect()->route('menus.index')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => $successMessage,
                ],
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to delete menu and associated access menu records. ' . $e->getMessage();
    
            return redirect()->route('menus.index')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => $errorMessage,
                ],
            ]);
        }
    }

    // Submenus
    public function getAllSubmenus()
    {
        try {
            $submenus = MenuSub::join('menus', 'menus.id', '=', 'menu_subs.menu_id')
                ->select('menu_subs.*', 'menus.menu_name')
                ->get();

            return response()->json(['data' => $submenus]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showSubmenusIndex(Request $request)
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
          
            return redirect('/');
        }

        $menu           = Menu::all();
        $menusub        = MenuSub::all();
        $menuchild      = MenuSubsChild::all();
        $role           = UserRole::all();
        $menuCount      = $menu->count();
        $menusubCount   = $menusub->count();
        $menuchildCount = $menuchild->count();
        $roleCount      = $role->count();

        $accessMenus    = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        $menus          = Menu::whereIn('id', $accessMenus)->get();
        $subMenus       = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus  = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData       = UserRole::where('id', $user->role)->first();
      
        $additionalData = [
            'title'                 => 'Submenu',
            'subtitle'              => 'List',
            'user'                  => $user,
            'role'                  => $roleData,
            'menus'                 => $menus,
            'subMenus'              => $subMenus,
            'childSubMenus'         => $childSubMenus,
            'menu'                  => $menu,
            'menusub'               => $menusub,
            'menuchild'             => $menuchild,
            'menuCount'             => $menuCount,
            'menusubCount'          => $menusubCount,
            'menuchildCount'        => $menuchildCount,
            'roleCount'             => $roleCount,
        ];   
        return view('Konten/Menu/menu-submenu', $additionalData);
    }

    public function addSubmenu(Request $request)
    {
        try {
            // Validate the request data for submenu
            $request->validate([
                'submenu_name' => 'required|string|max:255',
                'menu_id' => 'required|exists:menus,id', // Validate if the selected menu exists
                'url' => 'required|string',
                'icon' => 'required|string',
            ]);

              // Fetch the last order value from the database and increment it
              $lastOrder = MenuSub::max('order');
              $order = $lastOrder + 1;
    
            // Create a new submenu
            $submenu = new MenuSub([
                'menu_id' => $request->input('menu_id'),
                'title' => $request->input('submenu_name'),
                'order' => $order,
                'url' => $request->input('url'),
                'icon' => $request->input('icon'),
                'itemsub' => $request->has('itemsub') ? 1 : 0,
                'status' => $request->has('status') ? 1 : 0,
            ]);
    
            // Save the submenu
            $submenu->save();

            $userMenu = new AccessSub([
                'role_id' => 1,
                'submenu_id' => $submenu->id, 
            ]);

            // Save the user_menu record
            $userMenu->save();
    
            // Flash success response to the session
            return redirect()->route('menu.submenu')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'Submenu added successfully',
                ],
            ]);
        } catch (\Exception $e) {
            // Flash error response to the session
            return redirect()->route('menu.submenu')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Failed to add submenu. ' . $e->getMessage(),
                ],
            ]);
        }
    }

    public function deleteSubmenu(Request $request)
    {
        try {
            // Retrieve the ID from the query parameters
            $id = $request->input('id');
    
            // Delete the menu with the specified ID
            MenuSub::where('id', $id)->delete();
    
            // Delete the associated access menu records
            AccessSub::where('submenu_id', $id)->delete();
    
            $successMessage = 'Submenu Berhasil Dihapus';
    
            return redirect()->route('menu.submenu')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => $successMessage,
                ],
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to delete menu and associated access menu records. ' . $e->getMessage();
    
            return redirect()->route('menu.submenu')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => $errorMessage,
                ],
            ]);
        }
    }
    
    public function editSubmenu(Request $request)
    {
        try {
            // Validasi data yang diterima dari formulir
            $validatedData = $request->validate([
                'submenu_name' => 'required|string',
                'menu_id' => 'required|numeric',
                'url' => 'required|string',
                'icon' => 'required|string',
                'itemsub' => 'nullable|boolean',
                'id' => 'required|numeric'
            ]);

            // Peroleh data dari formulir
            $submenuName = $request->input('submenu_name');
            $menuId = $request->input('menu_id');
            $url = $request->input('url');
            $icon = $request->input('icon');
            $itemsub = $request->input('itemsub', 0); // Jika checkbox tidak dicentang, nilainya akan menjadi 0
            $id = $request->input('id');

            // Lakukan operasi yang diperlukan untuk mengedit submenu
            $submenu = MenuSub::find($id);
            if (!$submenu) {
                throw new \Exception('Submenu tidak ditemukan');
            }
            $submenu->title = $submenuName;
            $submenu->menu_id = $menuId;
            $submenu->url = $url;
            $submenu->icon = $icon;
            $submenu->itemsub = $itemsub;
            $submenu->save();

            // Redirect ke halaman yang sesuai dengan pesan sukses
            return redirect()->route('menu.submenu')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'Submenu berhasil dirubah',
                ],
            ]);
        } catch (ValidationException $e) {
            // Tangani jika validasi gagal
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Tangani jika terjadi kesalahan lain
            return redirect()->back()->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => $e->getMessage(),
                ],
            ]);
        }
    }

    // Child Submenus
    public function getAllChildSubmenus()
    {
        try {
            $childmenus = MenuSubsChild::all();

            return response()->json(['data' => $childmenus]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function showChildSubmenusIndex(Request $request)
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
          
            return redirect('/');
        }
        
        $menu           = Menu::all();
        $menusub        = MenuSub::all();
        $menuchild      = MenuSubsChild::all();
        $role           = UserRole::all();
        $menuCount      = $menu->count();
        $menusubCount   = $menusub->count();
        $menuchildCount = $menuchild->count();
        $roleCount      = $role->count();

        $accessMenus    = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        // Mengambil data berdasarkan hak akses
        $menus          = Menu::whereIn('id', $accessMenus)->get();
        $subMenus       = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus  = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData       = UserRole::where('id', $user->role)->first();

        $additionalData = [
            'title'                 => 'Submenu',
            'subtitle'              => 'List',
            'user'                  => $user,
            'role'                  => $roleData,
            'menus'                 => $menus,
            'subMenus'              => $subMenus,
            'childSubMenus'         => $childSubMenus,
            'menu'                  => $menu,
            'menusub'               => $menusub,
            'menuchild'             => $menuchild,
            'menuCount'             => $menuCount,
            'menusubCount'          => $menusubCount,
            'menuchildCount'        => $menuchildCount,
            'roleCount'             => $roleCount,
        ];   
        return view('Konten/Menu/menu-submenuchild', $additionalData);
    }

    public function addChildSubmenu(Request $request)
    {
        try {
            // Validate the request data for submenu
            $request->validate([
                'childsubmenu_name' => 'required|string|max:255',
                'submenu_id' => 'required', // Validate if the selected menu exists
                'url' => 'required|string',
            ]);
    
            // Fetch the last order value from the database and increment it
            $lastOrder = MenuSubsChild::max('order');
            $order = $lastOrder + 1;
    
            // Create a new submenu
            $childsubmenu = new MenuSubsChild([
                'id_submenu' => $request->input('submenu_id'),
                'title' => $request->input('childsubmenu_name'),
                'order' => $order,
                'url' => $request->input('url'),
                'is_active' => $request->has('childSubmenuStatus') ? 1 : 0,
            ]);
    
            // Save the submenu
            $childsubmenu->save();
    
            $userMenu = new AccessSubChild([
                'role_id' => 1,
                'childsubmenu_id' => $childsubmenu->id, // Fix the typo here
            ]);
    
            // Save the user_menu record
            $userMenu->save();
    
            // Flash success response to the session
            return redirect()->route('menu.child')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'Child Submenu added successfully',
                ],
            ]);
        } catch (\Exception $e) {
            // Flash error response to the session
            return redirect()->route('menu.child')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Failed to add Child Submenu. ' . $e->getMessage(),
                ],
            ]);
        }
    }

    public function deleteChildSubmenu(Request $request)
    {
        try {
            // Retrieve the ID from the query parameters
            $id = $request->input('id');
    
            // Delete the menu with the specified ID
            MenuSubsChild::where('id', $id)->delete();
    
            // Delete the associated access menu records
            AccessSubChild::where('childsubmenu_id', $id)->delete();
    
            $successMessage = 'Submenu Berhasil Dihapus';
    
            return redirect()->route('menu.child')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => $successMessage,
                ],
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to delete menu and associated access menu records. ' . $e->getMessage();
    
            return redirect()->route('menu.child')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => $errorMessage,
                ],
            ]);
        }
    }

    // Role 
    public function getAllRole()
    {
        try {
            $role = UserRole::all();

            return response()->json(['data' => $role]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function showRoleIndex(Request $request)
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
          
            return redirect('/');
        }
        
        $menu           = Menu::all();
        $menusub        = MenuSub::all();
        $menuchild      = MenuSubsChild::all();
        $role           = UserRole::all();
        $menuCount      = $menu->count();
        $menusubCount   = $menusub->count();
        $menuchildCount = $menuchild->count();
        $roleCount      = $role->count();
      
        $accessMenus    = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
    
        // Mengambil data berdasarkan hak akses
        $menus          = Menu::whereIn('id', $accessMenus)->get();
        $subMenus       = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus  = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData       = UserRole::where('id', $user->role)->first();

        $additionalData = [
            'title'                 => 'Menu',
            'subtitle'              => 'Role',
            'user'                  => $user,
            'role'                  => $roleData,
            'menus'                 => $menus,
            'subMenus'              => $subMenus,
            'childSubMenus'         => $childSubMenus,
            'menu'                  => $menu,
            'menusub'               => $menusub,
            'menuchild'             => $menuchild,
            'menuCount'             => $menuCount,
            'menusubCount'          => $menusubCount,
            'menuchildCount'        => $menuchildCount,
            'roleCount'             => $roleCount,            
        ];   
        return view('Konten/Menu/menu-subrole', $additionalData);
    }

    public function addRole(Request $request)
    {
        try {
            // Validate the request data for role
            $request->validate([
                'role' => 'required|string|max:255',
            ]);

            // Create a new role
            $role = new UserRole([
                'role' => $request->input('role'),
            ]);

            // Save the role
            $role->save();

            // Flash success response to the session
            return redirect()->route('menu.role')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => 'Role added successfully',
                ],
            ]);
        } catch (\Exception $e) {
            // Flash error response to the session
            return redirect()->route('menu.role')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => 'Failed to add role. ' . $e->getMessage(),
                ],
            ]);
        }
    }

    public function deleteRole(Request $request)
    {
        try {
            // Retrieve the ID from the query parameters
            $id = $request->input('id');
    
            // Delete the menu with the specified ID
            UserRole::where('id', $id)->delete();
    
            // Delete the associated access menu records          
    
            $successMessage = 'Role Berhasil Dihapus';
    
            return redirect()->route('menu.role')->with([
                'response' => [
                    'success' => true,
                    'title' => 'Success',
                    'message' => $successMessage,
                ],
            ]);
        } catch (\Exception $e) {
            $errorMessage = 'Failed to delete Role records. ' . $e->getMessage();
    
            return redirect()->route('menu.role')->with([
                'response' => [
                    'success' => false,
                    'title' => 'Error',
                    'message' => $errorMessage,
                ],
            ]);
        }
    }
}
