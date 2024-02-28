<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Instansi;
use App\Models\User;
use App\Models\UserActivity;
use Jenssegers\Agent\Facades\Agent;


class AuthController extends Controller
{
    public function showLoginPage()
    {
        // Memeriksa apakah pengguna sudah masuk
        if (session()->has('user_id')) {
            // Jika sudah masuk, langsung alihkan ke halaman profil
            return redirect('/user/profile');
        }

        $data = [
            'title' => "Login",
            'subtitle' => "Portal Atjeh Mediatama Indonesia",
        ];

        return view('Konten.Auth.login', $data);
    }

    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $credentials = [
            'email' => $request->input('username'),
            'password' => $request->input('password'),
        ];
    
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']]) ||
            Auth::attempt(['username' => $credentials['email'], 'password' => $credentials['password']])) {
    
            $user = Auth::user();
    
            if ($user) {
                $device = Agent::device();
                $platform = Agent::platform();
                $browser = Agent::browser();
                
                $userActivity = new UserActivity();
                $userActivity->user_id = $user->id;
                $userActivity->activity = 'Logged in';
                $userActivity->ip_address = $request->ip();
                $userActivity->device_info = "$device $platform $browser";
                $userActivity->save();
                session(['user_id' => $user->id, 'user_role' => $user->role]);

                // Sweet Alert dengan pesan selamat datang
                $response = [
                    'success' => true,
                    'title' => 'Selamat Datang',
                    'message' => "Hallo, $user->name. Selamat datang! Semangat berkerja.",
                ];
    
                return redirect('/user/profile')->with('response', $response);
            } else {
                // Kredensial berhasil tetapi status tidak memenuhi syarat
                Auth::logout();
                $response = [
                    'success' => false,
                    'title' => 'Gagal',
                    'message' => 'Akun Anda belum diverifikasi.'
                ];
                return back()->withInput()->withErrors(['username' => 'Your account is not verified yet.'])->with('response', $response);
            }
        } else {
            $response = [
                'success' => false,
                'title' => 'Gagal',
                'message' => 'Username atau Password Tidak Ditemukan'
            ];
            return back()->with('response', $response);
        }
    }

    public function showRegisForm()
    {                
        $data = [
            'title' => 'Register', // Judul halaman
            'subtitle' => 'Portal Atjeh Mediatama Indonesia', // Judul halaman            
        ];

        return view('Konten.Auth.register', $data);
    }

    public function register(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'token' => 'required|string',
                'terms' => 'accepted',
            ]);

            // Lakukan pengecekan user_key menggunakan HTTP Client Laravel
            $response = Http::post('https://ariefraihandi.biz.id/api/check-user-key', [
                'user_key' => $request->input('token'),
            ]);
            $responseData = $response->json();

            // Periksa apakah request ke API berhasil
            if ($response->successful() && $responseData['message'] === 'User key is valid') {
        
                $existingInstansi = Instansi::where('token', $request->input('token'))->first();
    
                if ($existingInstansi) {
                    // Token already exists, display an error message
                    $response = [
                        'success' => false,
                        'title' => 'Gagal',
                        'message' => 'Token is already registered. Contact Developer.',
                    ];
                    return back()->with('response', $response);
                }
                
                $instansi = new Instansi();
                $instansi->name = 'default';
                $instansi->short_name = 'default';
                $instansi->long_name = 'default';
                $instansi->alamat = 'default';
                $instansi->email = 'default';
                $instansi->wa = 'default';
                $instansi->logo = 'default.webp';
                $instansi->kop_surat = 'default.webp';
                $instansi->token = $request->input('token');
                $instansi->zip_code = 'default';
                $instansi->country = 'default';
                $instansi->phone_number = 'default';
                $instansi->website = 'default';
                $instansi->description = 'default';
                $instansi->save();

                $admin = new User();
                $admin->name = 'Administrator';
                $admin->username = $request->input('username');
                $admin->email = $request->input('email');
                $admin->role = 1;
                $admin->wa = 'default';
                $admin->token = $request->input('token');
                $admin->image = 'default.webp';
                $admin->password = bcrypt($request->input('password'));
                $admin->save();

                $response = [
                    'success' => true,
                    'title' => 'Berhasil',
                    'message' => 'Pendaftaran berhasil. Silakan login.',
                ];
    
                // Redirect to the login page with SweetAlert response
                return redirect()->route('login')->with('response', $response);
            } else {
                // Set error message
                $response = [
                    'success' => false,
                    'title' => 'Gagal',
                    'message' => 'Invalid Token. Registration failed. Hubungi Developer',
                ];
    
                // Redirect back to the registration page with SweetAlert response
                return back()->with('response', $response);
            }
        } catch (\Exception $e) {
            // Log the error
            \Log::error($e->getMessage());
    
            // Set error message
            $response = [
                'success' => false,
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan. Hubungi Developer',
            ];
    
            // Redirect back to the registration page with SweetAlert response
            return back()->with('response', $response);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Clear the session data
        $request->session()->flush();
        $request->session()->regenerate();

        // Sweet Alert dengan pesan logout sukses
        $response = [
            'success' => true,
            'title' => 'Berhasil Logout',
            'message' => 'Anda telah berhasil logout. Sampai jumpa lagi!',
        ];
        return redirect()->route('login')->with('response', $response);       
    }

}
