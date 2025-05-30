<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;

class AuthController extends Controller
{
    function login()
    {
        return view("User/login");
    }

    function register()
    {
        return view("User/register");
    }

    function adminLogin()
    {
        return view("Admin/login");
    }

    function logout()
    {
        if (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
            return Redirect::route('login');
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            return Redirect::route('admin.login');
        }
        return Redirect::route('home');
    }


    function userlogin(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'email' => 'required|email',
        //     'password' => 'required|min:6',
        // ]);

        $credentials = $request->only('email', 'password');
        // dd($credentials);
        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    function adminValidateAndLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);

    }
}
