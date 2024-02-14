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
            
            // Konversi nilai status menjadi badge HTML yang diinginkan
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
           
           
        ];
        
        return view('Konten/User/adminUsers', $additionalData);
    }     
    
    public function addUsers(Request $request)
    {
        // dd($request);
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

    

    private function cleanNumericInput($input)
    {
        // Menghapus titik (.) dan koma (,)
        $cleanedInput = str_replace(['.', ','], '', $input);

        // Menghapus dua digit nol di belakang koma
        $cleanedInput = preg_replace('/,00$/', '', $cleanedInput);

        return $cleanedInput;
    }
}
