<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\TicketMessage;
use App\Models\StaffCustomerGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GroupStaffController extends Controller
{
    /**
     * Admin view: Quản lý nhân viên - nhóm
     */
    public function index()
    {
        $groups = CustomerGroup::with(['staff', 'users'])->active()->get();
        $staffList = User::where('role', 2)->where('is_active', 1)->get();
        
        return view('backend.group-staff.index', compact('groups', 'staffList'));
    }

    /**
     * Admin gán nhân viên cho nhóm
     */
    public function assign(Request $request)
{
    $request->validate([
        'group_id' => 'required|exists:customer_groups,id',
        'staff_id' => 'required|exists:users,id',
    ]);

    $staff = User::where('id', $request->staff_id)
        ->where('role', 2)
        ->where('is_active', 1)
        ->firstOrFail();

    $group = CustomerGroup::findOrFail($request->group_id);

    DB::beginTransaction();
    try {
        // ✅ Kiểm tra nhóm đã có nhân viên chưa
        $existingStaff = StaffCustomerGroup::where('customer_group_id', $request->group_id)->first();
        
        if ($existingStaff) {
            return back()->with('error', "Nhóm này đã có nhân viên {$existingStaff->staff->name} phụ trách! Vui lòng gỡ nhân viên cũ trước.");
        }

        // ✅ Tạo liên kết (không cần is_primary)
        StaffCustomerGroup::create([
            'staff_id' => $request->staff_id,
            'customer_group_id' => $request->group_id
        ]);

        $message = "Đã gán {$staff->name} cho nhóm {$group->name}!";

        // Optional: tự động gán ticket nếu bật auto_assign
        if ($request->boolean('auto_assign')) {
            $unassignedTickets = Ticket::whereHas('user.groups', function($query) use ($request) {
                    $query->where('customer_group_id', $request->group_id);
                })
                ->whereNull('assigned_staff_id')
                ->whereIn('status', [Ticket::STATUS_NEW, Ticket::STATUS_IN_PROGRESS])
                ->get();

            foreach ($unassignedTickets as $ticket) {
                $ticket->update([
                    'assigned_staff_id' => $staff->id,
                    'assigned_group_id' => $group->id,
                    'assignment_type' => Ticket::ASSIGNMENT_GROUP,
                    'status' => in_array($ticket->status, [Ticket::STATUS_NEW, 'open']) ? Ticket::STATUS_IN_PROGRESS : $ticket->status
                ]);

                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => Auth::id(),
                    'message' => "Ticket đã được gán theo nhóm {$group->name} cho {$staff->name}.",
                    'is_system_message' => true
                ]);
            }

            if ($unassignedTickets->isNotEmpty()) {
                $message .= " Đã tự động gán {$unassignedTickets->count()} ticket(s).";
            }
        }

        DB::commit();
        return back()->with('success', $message);
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
    }
}

    /**
     * Admin gỡ nhân viên khỏi nhóm
     */
    public function remove(Request $request, $groupId, $staffId)
    {
        DB::beginTransaction();
        try {
            $group = CustomerGroup::findOrFail($groupId);
            $staff = User::findOrFail($staffId);
            
            $staffGroup = StaffCustomerGroup::where('customer_group_id', $groupId)
                ->where('staff_id', $staffId)
                ->firstOrFail();

            // ✅ Unassign tất cả tickets của nhân viên này trong nhóm (trừ tickets đã đóng)
            $affectedTickets = Ticket::whereHas('user.groups', function($query) use ($groupId) {
                    $query->where('customer_group_id', $groupId);
                })
                ->where('assigned_staff_id', $staffId)
                ->where('assignment_type', Ticket::ASSIGNMENT_GROUP)
                ->where('assigned_group_id', $groupId)
                ->where('status', '!=', Ticket::STATUS_CLOSED) // Không unassign tickets đã đóng
                ->get();

            foreach ($affectedTickets as $ticket) {
                $ticket->update([
                    'assigned_staff_id' => null,
                    'assigned_group_id' => null,
                    'assignment_type' => null,
                    'status' => Ticket::STATUS_NEW // Reset về trạng thái chưa xử lý khi bỏ gán
                ]);
                
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => Auth::id(),
                    'message' => "Ticket đã được bỏ gán do {$staff->name} không còn phụ trách nhóm. Vui lòng gán lại nhân viên mới.",
                    'is_system_message' => true
                ]);
            }

            $staffGroup->delete();

            $message = "Đã gỡ {$staff->name} khỏi nhóm!";
            if ($affectedTickets->isNotEmpty()) {
                $message .= " {$affectedTickets->count()} ticket(s) cần được gán lại.";
            }

            DB::commit();
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}