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

        $group = CustomerGroup::findOrFail($request->group_id);

        DB::beginTransaction();
        try {
            // Nếu là primary, bỏ primary của các staff khác trong nhóm này
            if ($request->is_primary) {
                StaffCustomerGroup::where('customer_group_id', $request->group_id)
                    ->update(['is_primary' => false]);
            }

            // Tạo hoặc cập nhật liên kết
            $staffGroup = StaffCustomerGroup::updateOrCreate(
                [
                    'staff_id' => $request->staff_id,
                    'customer_group_id' => $request->group_id
                ],
                [
                    'is_primary' => $request->is_primary ?? false
                ]
            );

            $message = 'Đã gán nhân viên cho nhóm thành công!';

            // Nếu là primary staff, tự động gán các tickets chưa được assign của nhóm này
            if ($request->is_primary) {
                $unassignedTickets = Ticket::whereHas('user.groups', function($query) use ($request) {
                        $query->where('customer_group_id', $request->group_id);
                    })
                    ->whereNull('assigned_staff_id')
                    ->whereIn('status', [Ticket::STATUS_NEW, Ticket::STATUS_IN_PROGRESS])
                    ->get();

                if ($unassignedTickets->isNotEmpty()) {
                    foreach ($unassignedTickets as $ticket) {
                        $ticket->update([
                            'assigned_staff_id' => $staff->id,
                            'assignment_type' => Ticket::ASSIGNMENT_INDIVIDUAL,
                            'status' => $ticket->status === Ticket::STATUS_NEW ? Ticket::STATUS_IN_PROGRESS : $ticket->status
                        ]);

                        TicketMessage::create([
                            'ticket_id' => $ticket->id,
                            'sender_id' => Auth::id(),
                            'message' => "Ticket đã được tự động gán cho {$staff->name} - nhân viên chính phụ trách nhóm {$group->name}.",
                            'is_system_message' => true
                        ]);
                    }

                    $message .= " Đã tự động gán {$unassignedTickets->count()} ticket(s) chưa được xử lý cho {$staff->name}.";
                }
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function remove(Request $request, $groupId, $staffId)
    {
        DB::beginTransaction();
        try {
            $group = CustomerGroup::findOrFail($groupId);
            $staff = User::findOrFail($staffId);
            
            // Kiểm tra xem nhân viên này có phải primary không
            $staffGroup = StaffCustomerGroup::where('customer_group_id', $groupId)
                ->where('staff_id', $staffId)
                ->first();
            
            if (!$staffGroup) {
                return back()->with('error', 'Nhân viên không thuộc nhóm này!');
            }

            $wasPrimary = $staffGroup->is_primary;

            // 1. Tìm tickets của các khách hàng trong nhóm này được gán cho nhân viên bị xóa
            $affectedTickets = Ticket::whereHas('user.groups', function($query) use ($groupId) {
                    $query->where('customer_group_id', $groupId);
                })
                ->where('assigned_staff_id', $staffId)
                ->whereIn('status', [Ticket::STATUS_NEW, Ticket::STATUS_IN_PROGRESS])
                ->get();

            if ($affectedTickets->isNotEmpty()) {
                // 2. Tìm nhân viên thay thế
                $replacementStaff = $this->findReplacementStaff($groupId, $staffId);

                if ($replacementStaff) {
                    // Gán lại tickets cho nhân viên thay thế
                    foreach ($affectedTickets as $ticket) {
                        $ticket->update([
                            'assigned_staff_id' => $replacementStaff->id,
                            'assignment_type' => Ticket::ASSIGNMENT_INDIVIDUAL
                        ]);
                        
                        // Tạo thông báo trong ticket
                        \App\Models\TicketMessage::create([
                            'ticket_id' => $ticket->id,
                            'sender_id' => Auth::id(),
                            'message' => "Ticket đã được chuyển từ {$staff->name} sang {$replacementStaff->name} do thay đổi phân công nhóm.",
                            'is_system_message' => true
                        ]);
                    }
                    
                    $message = "Đã gỡ nhân viên khỏi nhóm và chuyển {$affectedTickets->count()} ticket(s) sang {$replacementStaff->name}!";
                } else {
                    // Không có nhân viên thay thế, unassign tickets
                    foreach ($affectedTickets as $ticket) {
                        $ticket->update([
                            'assigned_staff_id' => null,
                            'assignment_type' => null
                        ]);
                        
                        \App\Models\TicketMessage::create([
                            'ticket_id' => $ticket->id,
                            'sender_id' => Auth::id(),
                            'message' => "Ticket đã được bỏ gán do {$staff->name} không còn phụ trách nhóm. Vui lòng gán lại nhân viên mới.",
                            'is_system_message' => true
                        ]);
                    }
                    
                    $message = "Đã gỡ nhân viên khỏi nhóm. {$affectedTickets->count()} ticket(s) cần được gán lại nhân viên!";
                }
            } else {
                $message = "Đã gỡ nhân viên khỏi nhóm!";
            }

            // 3. Xóa liên kết staff-group
            $staffGroup->delete();

            // 4. Nếu nhân viên bị xóa là primary, tự động chọn primary mới
            if ($wasPrimary) {
                $newPrimary = StaffCustomerGroup::where('customer_group_id', $groupId)
                    ->whereHas('staff', function($query) {
                        $query->where('is_active', 1);
                    })
                    ->first();
                
                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                    $message .= " Nhân viên {$newPrimary->staff->name} đã được đặt làm primary.";
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
     * Tìm nhân viên thay thế phù hợp nhất
     * Ưu tiên: Primary staff > Staff còn lại trong nhóm > null
     */
    private function findReplacementStaff($groupId, $excludeStaffId)
    {
        // Tìm primary staff của nhóm (nếu có)
        $primaryStaff = StaffCustomerGroup::where('customer_group_id', $groupId)
            ->where('staff_id', '!=', $excludeStaffId)
            ->where('is_primary', true)
            ->whereHas('staff', function($query) {
                $query->where('is_active', 1);
            })
            ->first();

        if ($primaryStaff) {
            return $primaryStaff->staff;
        }

        // Nếu không có primary, lấy staff bất kỳ trong nhóm
        $anyStaff = StaffCustomerGroup::where('customer_group_id', $groupId)
            ->where('staff_id', '!=', $excludeStaffId)
            ->whereHas('staff', function($query) {
                $query->where('is_active', 1);
            })
            ->first();

        return $anyStaff ? $anyStaff->staff : null;
    }

    /**
     * Chuyển tất cả tickets khi nhân viên nghỉ việc
     */
    public function transferTicketsOnInactive($staffId)
    {
        DB::beginTransaction();
        try {
            $staff = User::findOrFail($staffId);
            
            // Lấy tất cả tickets đang active của nhân viên này
            $activeTickets = Ticket::where('assigned_staff_id', $staffId)
                ->whereIn('status', [Ticket::STATUS_NEW, Ticket::STATUS_IN_PROGRESS])
                ->with('user.groups')
                ->get();

            $transferredCount = 0;
            $unassignedCount = 0;

            foreach ($activeTickets as $ticket) {
                // Tìm nhóm của khách hàng
                $customerGroup = $ticket->user->groups->first();
                
                if ($customerGroup) {
                    // Tìm nhân viên thay thế trong nhóm
                    $replacement = $this->findReplacementStaff($customerGroup->id, $staffId);
                    
                    if ($replacement) {
                        $ticket->update([
                            'assigned_staff_id' => $replacement->id,
                            'assignment_type' => Ticket::ASSIGNMENT_INDIVIDUAL
                        ]);
                        
                        \App\Models\TicketMessage::create([
                            'ticket_id' => $ticket->id,
                            'sender_id' => Auth::id(),
                            'message' => "Ticket đã được chuyển từ {$staff->name} sang {$replacement->name} do nhân viên nghỉ việc.",
                            'is_system_message' => true
                        ]);
                        
                        $transferredCount++;
                        continue;
                    }
                }
                
                // Không tìm được nhân viên thay thế
                $ticket->update([
                    'assigned_staff_id' => null,
                    'assignment_type' => 'unassigned'
                ]);
                
                \App\Models\TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => Auth::id(),
                    'message' => "Ticket đã được bỏ gán do {$staff->name} nghỉ việc. Vui lòng gán lại nhân viên mới.",
                    'is_system_message' => true
                ]);
                
                $unassignedCount++;
            }

            DB::commit();
            
            return [
                'success' => true,
                'transferred' => $transferredCount,
                'unassigned' => $unassignedCount,
                'message' => "Đã xử lý {$activeTickets->count()} ticket(s): {$transferredCount} được chuyển giao, {$unassignedCount} cần gán lại."
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Gán lại tất cả tickets chưa được assign của một nhóm cho primary staff
     */
    public function reassignUnassignedTickets($groupId)
    {
        DB::beginTransaction();
        try {
            $group = CustomerGroup::findOrFail($groupId);
            
            // Tìm primary staff của nhóm
            $primaryStaff = StaffCustomerGroup::where('customer_group_id', $groupId)
                ->where('is_primary', true)
                ->whereHas('staff', function($query) {
                    $query->where('is_active', 1);
                })
                ->first();

            if (!$primaryStaff) {
                return back()->with('error', 'Nhóm chưa có nhân viên chính (primary staff)!');
            }

            // Lấy tất cả tickets chưa được gán của nhóm này
            $unassignedTickets = Ticket::whereHas('user.groups', function($query) use ($groupId) {
                    $query->where('customer_group_id', $groupId);
                })
                ->whereNull('assigned_staff_id')
                ->whereIn('status', [Ticket::STATUS_NEW, Ticket::STATUS_IN_PROGRESS])
                ->get();

            if ($unassignedTickets->isEmpty()) {
                return back()->with('info', 'Không có ticket nào cần gán!');
            }

            foreach ($unassignedTickets as $ticket) {
                $ticket->update([
                    'assigned_staff_id' => $primaryStaff->staff_id,
                    'assignment_type' => Ticket::ASSIGNMENT_INDIVIDUAL,
                    'status' => $ticket->status === Ticket::STATUS_NEW ? Ticket::STATUS_IN_PROGRESS : $ticket->status
                ]);

                TicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'sender_id' => Auth::id(),
                    'message' => "Ticket đã được gán cho {$primaryStaff->staff->name} - nhân viên chính phụ trách nhóm {$group->name}.",
                    'is_system_message' => true
                ]);
            }

            DB::commit();
            return back()->with('success', "Đã gán {$unassignedTickets->count()} ticket(s) cho {$primaryStaff->staff->name}!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}