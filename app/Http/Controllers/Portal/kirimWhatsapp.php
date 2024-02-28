<?php

namespace App\Http\Controllers\Portal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class kirimWhatsapp extends Controller
{
    public function kirimPesan(Request $request)
    {
        $id = $request->query('id'); // Mendapatkan nilai dari parameter 'id'
        $type = $request->query('type'); // Mendapatkan nilai dari parameter 'type'

        if ($type === 'user') {
            // Cari data user berdasarkan ID
            $user = User::find($id);

            if ($user) {
                // Ambil informasi nama, username, dan wa
                $name = $user->name;
                $username = $user->username;
                $wa = $user->wa;

                // Format pesan
                $message = "Assalamualaikum Bapak/Ibu *$name*,\n\nKami menginformasikan bahwa authentikasi untuk login Anda adalah:\n\n*Username*: $username\n*Password*: 123456\n\nSegera lakukan perubahan password melalui link berikut:\nhttps://apps.atjehmediatama.co.id/user/security\n\nSalam hangat,\nAdmin";

                // Buat URL untuk WhatsApp
                $waUrl = 'https://wa.me/' . $wa . '?text=' . urlencode($message);

                // Redirect ke URL WhatsApp
                return redirect()->away($waUrl);
            } else {
                // User tidak ditemukan
                return "User with ID $id not found.";
            }
        } else {
            // Tipe tidak didukung
            return "Unsupported type: $type";
        }
    }
}
