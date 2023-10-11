<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Phương thức hiển thị form đăng nhập
public function showLoginForm()
{
    return view('auth.login');
}

// Phương thức xử lý đăng nhập
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        // Đăng nhập thành công
        $user = Auth::user(); // Lấy thông tin người dùng đã đăng nhập
        Session::put('user_name', $user->name);
        return redirect()->intended('/home');
    } else {
        // Đăng nhập không thành công
        return redirect()->route('login')->with('error', 'Email or password is incorrect!');
    }
}

// Phương thức đăng xuất
public function logout()
{
    Auth::logout();
    Session::forget('user_name');
    return redirect()->route('login');
}

public function showRegistrationForm()
{
    return view('auth.register');
}

public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    Auth::login($user);

    return redirect('/login');
}
}
