<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\Models\StaffCustomerGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupStaffController extends Controller
{
    public function index()
    {
        $groups = CustomerGroup::with(['staff', 'users'])->active()->get();
        $staffList = User::where('role', 2)->where('is_active', 1)->get();
        
        return view('backend.group-staff.index', compact('groups', 'staffList'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:customer_groups,id',
            'staff_id' => 'required|exists:users,id',
            'is_primary' => 'boolean'
        ]);

        // Kiểm tra nhân viên có phải staff không
        $staff = User::where('id', $request->staff_id)
            ->where('role', 2)
            ->where('is_active', 1)
            ->firstOrFail();

        // Nếu là primary, bỏ primary của các staff khác trong nhóm này
        if ($request->is_primary) {
            StaffCustomerGroup::where('customer_group_id', $request->group_id)
                ->update(['is_primary' => false]);
        }

        // Tạo hoặc cập nhật liên kết
        StaffCustomerGroup::updateOrCreate(
            [
                'staff_id' => $request->staff_id,
                'customer_group_id' => $request->group_id
            ],
            [
                'is_primary' => $request->is_primary ?? false
            ]
        );

        return back()->with('success', 'Đã gán nhân viên cho nhóm thành công!');
    }

    public function remove(Request $request, $groupId, $staffId)
    {
        StaffCustomerGroup::where('customer_group_id', $groupId)
            ->where('staff_id', $staffId)
            ->delete();

        return back()->with('success', 'Đã gỡ nhân viên khỏi nhóm!');
    }
}