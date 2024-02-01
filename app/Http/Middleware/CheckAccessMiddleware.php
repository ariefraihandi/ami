<?php

// app/Http/Middleware/CheckAccessMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\AccessMenu;
use App\Models\AccessSub;
use App\Models\AccessSubChild; // Import the AccessSubChild model

class CheckAccessMiddleware
{
    public function handle($request, Closure $next)
    {
        // Mendapatkan URL saat ini
        $currentUrl = Route::current()->getName();

        // Mendapatkan user_role dari sesi
        $userRole = session('user_role');

        // Memeriksa apakah pengguna memiliki akses ke URL
        $hasAccess = AccessMenu::where('user_id', $userRole)
            ->where('menu_id', $currentUrl)
            ->exists();

        if (!$hasAccess) {
            // Jika tidak memiliki akses, Anda dapat mengarahkan ke halaman tertentu
            return redirect('/user/profile');
        }

        // Melanjutkan ke pemeriksaan akses submenu
        $hasSubAccess = AccessSub::where('role_id', $userRole)
            ->where('submenu_id', $currentUrl)
            ->exists();

        if (!$hasSubAccess) {
            // Jika tidak memiliki akses submenu, Anda dapat mengarahkan ke halaman tertentu
            return redirect('/user/profile');
        }

        // Melanjutkan ke pemeriksaan akses child submenu
        $hasChildAccess = AccessSubChild::where('role_id', $userRole)
            ->where('childsubmenu_id', $currentUrl)
            ->exists();

        if (!$hasChildAccess) {
            // Jika tidak memiliki akses child submenu, Anda dapat mengarahkan ke halaman tertentu
            return redirect('/user/profile');
        }

        return $next($request);
    }
}
