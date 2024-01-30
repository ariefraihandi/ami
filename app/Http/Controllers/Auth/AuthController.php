<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginPage()
    {
        $data = [
            'title' => "Login",
            'subtitle' => "Portal Atjeh Mediatama Indonesia",
        ];

        return view('Konten.Auth.login', $data);
    }
}
