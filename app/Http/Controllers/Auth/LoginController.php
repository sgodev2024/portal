<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LoginLog;
use App\Mail\GenericMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email không tồn tại trong hệ thống.',
            ])->withInput();
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Mật khẩu không chính xác.',
            ])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        try {
            LoginLog::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'login_at'   => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Không thể ghi log đăng nhập: ' . $e->getMessage());
        }
        if ($user->role == 3 && $user->must_update_profile) {
            return redirect()->route('customer.profile.edit')
                ->with('info', 'Vui lòng cập nhật thông tin tài khoản trước khi tiếp tục.');
        }

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

    // Hiển thị form quên mật khẩu
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->is_active) {
            return back()->withErrors(['email' => 'Tài khoản này không hợp lệ hoặc đã bị khóa.']);
        }

        // Tạo token reset
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        // Link đặt lại mật khẩu
        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            // Lấy mẫu email theo code
            $template = EmailTemplate::where('code', 'forgot_password')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return back()->with('error', 'Không tìm thấy mẫu email "forgot_password".');
            }

            // Gửi email
            Mail::to($user->email)->queue(new GenericMail(
                $template,
                [
                    'user_name'   => $user->name,
                    'user_email'  => $user->email,
                    'reset_link'  => $resetLink,
                    'app_name'    => config('app.name'),
                ]
            ));

            return back()->with('success', 'Đã gửi liên kết đặt lại mật khẩu đến email của bạn!');
        } catch (\Exception $e) {
            Log::error("Lỗi gửi mail quên mật khẩu: " . $e->getMessage());
            return back()->with('warning', 'Không thể gửi email. Vui lòng thử lại sau.');
        }
    }

    // Hiển thị form nhập mật khẩu mới
    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Xử lý cập nhật mật khẩu mới
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return back()->withErrors(['email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
            'must_update_profile' => false,
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');
    }
}
