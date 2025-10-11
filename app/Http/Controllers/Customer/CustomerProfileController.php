<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
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
        /** @var User $user */
        $user->update(array_merge($data, [
            'must_update_profile' => false,
        ]));

        return redirect()
            ->route('customer.dashboard')
            ->with('success', 'Cập nhật thông tin thành công!');
    }
}
