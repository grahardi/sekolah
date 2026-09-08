<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginAsController extends Controller
{
    /**
     * Login As generik utk SEMUA jenis akun (admin/guru/induk) - pakai
     * session key SAMA (impersonating_admin_id) dgn implementasi lama yg
     * sebelumnya CUMA ada khusus akun Guru (EraporController::loginSebagaiGuru),
     * biar gak ada 2 sistem paralel yg beda2. "Kembali" tetap pakai route
     * lama (erapor.kembali-admin) yg sudah jalan & dipakai PortalLayout.jsx.
     */
    public function login(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        abort_if($user->id === auth()->id(), 400, 'Gak bisa login sebagai diri sendiri.');

        if (! session()->has('impersonating_admin_id')) {
            session(['impersonating_admin_id' => auth()->id()]);
        }

        Auth::login($user);

        return redirect('/dashboard')->with('success', "Sekarang login sebagai {$user->name}.");
    }
}
