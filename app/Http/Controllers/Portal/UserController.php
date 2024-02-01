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

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
          
            return redirect('/login');
        }

        $accessMenus = AccessMenu::where('user_id', $user->role)->pluck('menu_id');
        $accessSubmenus = AccessSub::where('role_id', $user->role)->pluck('submenu_id');
        $accessChildren = AccessSubChild::where('role_id', $user->role)->pluck('childsubmenu_id');
        // dd($accessMenus);
    
        // Mengambil data berdasarkan hak akses
        $menus = Menu::whereIn('id', $accessMenus)->get();
        $subMenus = MenuSub::whereIn('id', $accessSubmenus)->get();
        $childSubMenus = MenuSubsChild::whereIn('id', $accessChildren)->get();
        $roleData = UserRole::where('id', $user->role)->first();

        $additionalData = [
            'title'                     => 'User',
            'subtitle'                  => 'Profile',
            'menus'                     => $menus,
            'subMenus'                  => $subMenus,
            'childSubMenus'             => $childSubMenus,
            'user'                      => $user,
            'role'                      => $roleData,
        ];
    
        return view('Konten/User/profile', $additionalData);
    }
}
