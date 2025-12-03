<?php

namespace App\Controllers;

use SwiftPHP\Auth\Auth;
use SwiftPHP\Core\Controller;
use SwiftPHP\Http\{Request};

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($data['email'], $data['password'])) {
            return redirect('/dashboard');
        }

        return view('auth.login', [
            'error' => 'Invalid credentials'
        ]);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::register($data);

        if ($user) {
            return redirect('/dashboard');
        }

        return view('auth.register', [
            'error' => 'Registration failed'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function profile()
    {
        return view('auth.profile', [
            'user' => Auth::user()
        ]);
    }
}
