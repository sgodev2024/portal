<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CustomerProfileRequest;

class CustomerProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa hồ sơ khách hàng
     */
    public function edit()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Không có quyền truy cập');
        }

        return view('profile', compact('user'));
    }

    /**
     * Cập nhật thông tin hồ sơ khách hàng
     */
    public function update(CustomerProfileRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Không có quyền truy cập');
        }

        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['current_password']);
        unset($data['password_confirmation']);

        /** @var User $user */
        $user->update(array_merge($data, [
            'must_update_profile' => false,
        ]));

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Cập nhật thông tin thành công!');
    }
}
