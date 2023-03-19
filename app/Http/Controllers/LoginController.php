<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required | email',
            'password' => 'required'
        ]);

        $checkLogin = $request->only(['email', 'password']);

        if (Auth::attempt($checkLogin)) {
            User::where('id', auth()->user()->id)->update([
                'status' => 'Online'
            ]);
            return redirect()->route('convos');
        } else {
            return back()->with('creds_error', $request->email);
        }
    }
    public function logout()
    {
        User::where('id', auth()->user()->id)->update([
            'status' => 'Offline',
            'last_seen' => now()
        ]);
        Auth::logout();
        return redirect()->route('index');
    }
}
