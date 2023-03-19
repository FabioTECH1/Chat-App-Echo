<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'password' => 'required | confirmed',
            'image' => 'required | mimes:jpeg,bmp,png' // Only allow .jpg, .bmp and .png file types.
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            return back()->with('exists', 'A user with this email exist!');
        }
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $imagename = time() . '.' . $extension;

        // dd($request->all());
        User::create([
            'email' => $request->email,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'password' =>  Hash::make($request->password),
            'profile_pic' => $imagename
        ]);
        $image->move('uploads', $imagename);

        $checkLogin = $request->only(['email', 'password']);
        if (Auth::attempt($checkLogin)) {
            return redirect()->route('convos');
        } else {
            return back()->with('creds_error', ' ');
        }
    }
}