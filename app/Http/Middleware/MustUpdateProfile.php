<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MustUpdateProfile
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role == 3 && $user->must_update_profile) {
            // Nếu chưa cập nhật profile, redirect về form edit
            return redirect()->route('customer.profile.edit')
                ->with('info', 'Vui lòng cập nhật thông tin tài khoản trước khi tiếp tục.');
        }

        return $next($request);
    }
}
