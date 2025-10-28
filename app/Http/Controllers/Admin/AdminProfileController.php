<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AdminProfileRequest;

class AdminProfileController extends Controller
{
    /**
     * Hiển thị form chỉnh sửa hồ sơ admin
     */
    public function edit()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Không có quyền truy cập');
        }

        return view('admin.profile', compact('user'));
    }

    /**
     * Cập nhật thông tin hồ sơ admin
     */
    public function update(AdminProfileRequest $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                abort(403, 'Không có quyền truy cập');
            }

            // Lấy dữ liệu đã validate
            $data = $request->validated();

            // Log để debug
            Log::info('Updating admin profile for user: ' . $user->id, ['data' => array_keys($data)]);

            // Xử lý avatar
            if ($request->hasFile('avatar')) {
                // Xóa avatar cũ nếu có
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Upload avatar mới
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            } else {
                // Không update avatar nếu không có file mới
                unset($data['avatar']);
            }

            // Xử lý password
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // Xóa các field không cần thiết
            unset($data['current_password']);
            unset($data['password_confirmation']);

            // Đánh dấu đã cập nhật profile
            $data['must_update_profile'] = false;

            // Cập nhật thông tin
            $user->update($data);

            Log::info('Admin profile updated successfully for user: ' . $user->id);

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating admin profile: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
