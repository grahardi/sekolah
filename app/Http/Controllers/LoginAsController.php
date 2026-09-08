<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginAsController extends Controller
{
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
