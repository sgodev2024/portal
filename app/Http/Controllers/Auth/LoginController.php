<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ], [
            'name.required' => 'Vui lòng nhập tên hoặc email.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);
        $loginField = filter_var($request->name, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $user = User::where($loginField, $request->name)->first();

        if (!$user) {
            return back()->withErrors([
                'name' => 'Tài khoản không tồn tại.',
            ])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'name' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ]);
        }


        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Mật khẩu không chính xác.',
            ])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // if ($user->role == 3 && $user->must_update_profile) {
        //     return redirect()->route('customers.edit', $user->id)
        //         ->with('info', 'Vui lòng cập nhật thông tin tài khoản trước khi tiếp tục.');
        // }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        }

        return redirect()->route('customer.dashboard');
    }



    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('laravel_session'));

        return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
    }
}
