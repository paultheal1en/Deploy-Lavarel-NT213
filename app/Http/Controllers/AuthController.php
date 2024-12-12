<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // Kiểm tra người dùng
        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không chính xác.'],
            ]);
        }

        // Thiết lập lại session để tránh Session Fixation
        Session::regenerate();

        // Cập nhật lại CSRF token sau khi thay đổi session ID
        $csrf_token = csrf_token();

        // Đăng nhập thành công
        Auth::login($user);
        Session::put('user_id', $user->ID);
        Session::put('user_name', $user->name);
        
        // Trả về thông tin bao gồm CSRF token mới
        return redirect('/brac');
    }


    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',        // Ít nhất một chữ cái in hoa
                'regex:/[0-9]/',        // Ít nhất một chữ số
                'regex:/[@$!%*?&]/'     // Ít nhất một ký tự đặc biệt
            ],
        ], [
            'password.regex' => 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ cái viết hoa, chữ số và ít nhất một ký tự đặc biệt (@, $, !, %, *, ?, &).'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
    }

    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }
}