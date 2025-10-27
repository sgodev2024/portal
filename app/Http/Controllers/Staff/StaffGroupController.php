<?php

namespace App\Http\Controllers\Staff;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\TicketMessage;
use App\Models\StaffCustomerGroup;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StaffGroupController extends Controller
{
    /**
     * Danh sách nhóm khách hàng (staff tự chọn)
     */
    public function index()
    {
        $staffId = Auth::id();
        
        // Nhóm đã nhận
        $myGroups = CustomerGroup::whereHas('staff', function($query) use ($staffId) {
                $query->where('staff_id', $staffId);
            })
            ->with(['users', 'staff'])
            ->active()
            ->get();

        // Nhóm chưa có ai nhận
        $availableGroups = CustomerGroup::whereDoesntHave('staff')
            ->with('users')
            ->active()
            ->get();

        return view('staff.groups.index', compact('myGroups', 'availableGroups'));
    }

    /**
     * Staff tự nhận nhóm
     */
    public function claim(Request $request, $groupId)
    {
        $staffId = Auth::id();
        $group = CustomerGroup::findOrFail($groupId);

        DB::beginTransaction();
        try {
            // ✅ Kiểm tra nhóm đã có người nhận chưa
            $existingStaff = StaffCustomerGroup::where('customer_group_id', $groupId)->first();
            
            if ($existingStaff) {
                return back()->with('error', 'Nhóm này đã có nhân viên khác phụ trách!');
            }

            // ✅ Kiểm tra staff đã nhận tối đa X nhóm chưa (optional)
            $currentGroupCount = StaffCustomerGroup::where('staff_id', $staffId)->count();
            $maxGroups = 5; // Giới hạn mỗi staff tối đa 5 nhóm
            
            if ($currentGroupCount >= $maxGroups) {
                return back()->with('error', "Bạn đã phụ trách tối đa {$maxGroups} nhóm!");
            }

            // ✅ Tạo liên kết
            StaffCustomerGroup::create([
                'staff_id' => $staffId,
                'customer_group_id' => $groupId
            ]);

            // ✅ Auto assign tickets chưa xử lý
            $unassignedTickets = Ticket::whereHas('user.groups', function($query) use ($groupId) {
                    $query->where('customer_group_id', $groupId);
                })
                ->whereNull('assigned_staff_id')
                ->whereIn('status', [Ticket::STATUS_NEW, Ticket::STATUS_IN_PROGRESS])
                ->get();

            foreach ($unassignedTickets as $ticket) {
                $ticket->update([
                    'assigned_staff_id' => $staffId,
                    'assignment_type' => Ticket::ASSIGNMENT_INDIVIDUAL,
                    'status' => $ticket->status === Ticket::STATUS_NEW ? Ticket::STATUS_IN_PROGRESS : $ticket->status
                ]);

                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => $staffId,
                    'message' => "Bạn đã nhận phụ trách nhóm {$group->name} và ticket này được tự động gán cho bạn.",
                    'is_system_message' => true
                ]);
            }

            $message = "Đã nhận nhóm {$group->name} thành công!";
            if ($unassignedTickets->isNotEmpty()) {
                $message .= " Bạn có {$unassignedTickets->count()} ticket(s) mới cần xử lý.";
            }

            DB::commit();
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Staff bỏ nhóm
     */
    public function leave(Request $request, $groupId)
    {
        $staffId = Auth::id();
        
        DB::beginTransaction();
        try {
            $group = CustomerGroup::findOrFail($groupId);
            
            $staffGroup = StaffCustomerGroup::where('customer_group_id', $groupId)
                ->where('staff_id', $staffId)
                ->firstOrFail();

            // ✅ Unassign tickets
            $affectedTickets = Ticket::whereHas('user.groups', function($query) use ($groupId) {
                    $query->where('customer_group_id', $groupId);
                })
                ->where('assigned_staff_id', $staffId)
                ->whereIn('status', [Ticket::STATUS_NEW, Ticket::STATUS_IN_PROGRESS])
                ->get();

            foreach ($affectedTickets as $ticket) {
                $ticket->update([
                    'assigned_staff_id' => null,
                    'assignment_type' => null
                ]);
                
                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => $staffId,
                    'message' => "Ticket đã được bỏ gán do nhân viên không còn phụ trách nhóm {$group->name}.",
                    'is_system_message' => true
                ]);
            }

            $staffGroup->delete();

            DB::commit();
            return back()->with('success', "Đã rời khỏi nhóm {$group->name}!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}