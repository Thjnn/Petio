<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /** Ô "Email/Số điện thoại/Tên đăng nhập": tìm theo cả ba cột. */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string|max:150',
            'password' => 'required|string',
        ], [
            'login.required' => 'Hãy nhập email, số điện thoại hoặc tên đăng nhập.',
            'password.required' => 'Hãy nhập mật khẩu.',
        ]);

        $login = trim($request->login);
        $user = User::where('username', $login)->orWhere('email', $login)->orWhere('phone', $login)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['login' => 'Thông tin đăng nhập hoặc mật khẩu không đúng.'])->onlyInput('login');
        }

        if (! $user->isActive()) {
            return back()->withErrors(['login' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.'])->onlyInput('login');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended($user->can('admin') ? route('admin.dashboard') : route('home'))->with('success', 'Chào mừng '.$user->displayName().' quay lại Petio!');
    }

    public function register(Request $request)
    {
        $login = trim((string) $request->input('login'));
        $column = match (true) {
            filter_var($login, FILTER_VALIDATE_EMAIL) !== false => 'email',
            (bool) preg_match('/^0[0-9]{9}$/', $login) => 'phone',
            default => 'username',
        };

        $request->merge(['login' => $login])->validate([
            'login' => [
                'required', 'max:150',
                $column === 'username' ? 'regex:/^[A-Za-z0-9_.]{3,50}$/' : 'string',
                "unique:users,{$column}", 'unique:users,username',
            ],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'login.required' => 'Hãy nhập email, số điện thoại hoặc tên đăng nhập.',
            'login.regex' => 'Tên đăng nhập 3–50 ký tự, chỉ gồm chữ không dấu, số, dấu chấm hoặc gạch dưới.',
            'login.unique' => 'Tài khoản này đã được đăng ký.',
            'password.required' => 'Hãy nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        // username luôn bắt buộc: đăng ký bằng email/SĐT thì dùng luôn giá trị đó làm username
        $user = User::create([
            'username' => $login,
            $column => $login,
            'password' => $request->password,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với Petio 🎉');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đã đăng xuất.');
    }
}
