<?php
namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subject', 'category', 'priority', 'description', 'status',
        'assigned_staff_id', 'assignment_type' 
    ];

    protected static function boot()
    {
        parent::boot();

        // Tự động gán ticket cho nhân viên phụ trách nhóm khi tạo ticket
        static::created(function ($ticket) {
            try {
                // Load user với relationships
                $user = $ticket->user()->with('groups.staff')->first();
                
                if ($user && $user->groups->count() > 0) {
                    // Lấy nhóm đầu tiên của user
                    $group = $user->groups->first();
                    
                    if ($group) {
                        // Tìm nhân viên chính phụ trách nhóm
                        $primaryStaff = $group->staff()->wherePivot('is_primary', true)->first();
                        
                        if ($primaryStaff) {
                            $ticket->update([
                                'assigned_staff_id' => $primaryStaff->id,
                                'assignment_type' => self::ASSIGNMENT_INDIVIDUAL,
                                'status' => self::STATUS_IN_PROGRESS
                            ]);
                            
                            Log::info("Auto-assigned ticket {$ticket->id} to staff {$primaryStaff->name} for group {$group->name}");
                        } else {
                            Log::warning("No primary staff found for group {$group->name}");
                        }
                    }
                } else {
                    Log::info("User {$user->name} has no groups, ticket {$ticket->id} not auto-assigned");
                }
            } catch (\Exception $e) {
                Log::error("Error auto-assigning ticket {$ticket->id}: " . $e->getMessage());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CLOSED = 'closed';

    const CATEGORY_TECHNICAL = 'technical';
    const CATEGORY_BILLING = 'billing';
    const CATEGORY_GENERAL = 'general';
    const CATEGORY_COMPLAINT = 'complaint';
    const CATEGORY_FEATURE_REQUEST = 'feature_request';
    const CATEGORY_OTHER = 'other';

    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    const ASSIGNMENT_INDIVIDUAL = 'individual';
    const ASSIGNMENT_GROUP = 'group';

    // Get status label
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_NEW => 'Mới tạo',
            self::STATUS_IN_PROGRESS => 'Đang xử lý',
            self::STATUS_COMPLETED => 'Hoàn tất',
            self::STATUS_CLOSED => 'Đóng',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    // Get status badge class
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_NEW => 'bg-info',
            self::STATUS_IN_PROGRESS => 'bg-warning text-dark',
            self::STATUS_COMPLETED => 'bg-success',
            self::STATUS_CLOSED => 'bg-secondary',
        ];
        return $badges[$this->status] ?? 'bg-secondary';
    }
    public function getCategoryLabelAttribute()
    {
        $labels = [
            self::CATEGORY_TECHNICAL => 'Vấn đề kỹ thuật',
            self::CATEGORY_BILLING => 'Thanh toán',
            self::CATEGORY_GENERAL => 'Thắc mắc chung',
            self::CATEGORY_COMPLAINT => 'Khiếu nại',
            self::CATEGORY_FEATURE_REQUEST => 'Yêu cầu tính năng',
            self::CATEGORY_OTHER => 'Khác',
        ];
        return $labels[$this->category] ?? $this->category;
    }

    // Get category badge class
    public function getCategoryBadgeAttribute()
    {
        $badges = [
            self::CATEGORY_TECHNICAL => 'bg-danger',
            self::CATEGORY_BILLING => 'bg-success',
            self::CATEGORY_GENERAL => 'bg-info',
            self::CATEGORY_COMPLAINT => 'bg-warning text-dark',
            self::CATEGORY_FEATURE_REQUEST => 'bg-primary',
            self::CATEGORY_OTHER => 'bg-secondary',
        ];
        return $badges[$this->category] ?? 'bg-secondary';
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            self::PRIORITY_LOW => 'Thấp',
            self::PRIORITY_NORMAL => 'Bình thường',
            self::PRIORITY_HIGH => 'Cao',
            self::PRIORITY_URGENT => 'Khẩn cấp',
        ];
        return $labels[$this->priority] ?? $this->priority;
    }
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            self::PRIORITY_LOW => 'bg-secondary',
            self::PRIORITY_NORMAL => 'bg-info',
            self::PRIORITY_HIGH => 'bg-warning text-dark',
            self::PRIORITY_URGENT => 'bg-danger',
        ];
        return $badges[$this->priority] ?? 'bg-secondary';
    }

     public function scopeAssignedToStaff($query, $staffId)
    {
        return $query->where('assigned_staff_id', $staffId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_staff_id');
    }
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Static methods to get options for forms
    public static function getCategories()
    {
        return [
            self::CATEGORY_TECHNICAL => 'Vấn đề kỹ thuật',
            self::CATEGORY_BILLING => 'Thanh toán',
            self::CATEGORY_GENERAL => 'Thắc mắc chung',
            self::CATEGORY_COMPLAINT => 'Khiếu nại',
            self::CATEGORY_FEATURE_REQUEST => 'Yêu cầu tính năng',
            self::CATEGORY_OTHER => 'Khác',
        ];
    }

    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW => 'Thấp',
            self::PRIORITY_NORMAL => 'Bình thường',
            self::PRIORITY_HIGH => 'Cao',
            self::PRIORITY_URGENT => 'Khẩn cấp',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => 'Mới tạo',
            self::STATUS_IN_PROGRESS => 'Đang xử lý',
            self::STATUS_COMPLETED => 'Hoàn tất',
            self::STATUS_CLOSED => 'Đóng',
        ];
    }

    public static function getAssignmentTypes()
    {
        return [
            self::ASSIGNMENT_INDIVIDUAL => 'Cá nhân',
            self::ASSIGNMENT_GROUP => 'Nhóm',
        ];
    }

    // Get assignment information
    public function getAssignmentInfoAttribute()
    {
        if ($this->assignedStaff) {
            return [
                'type' => 'individual',
                'name' => $this->assignedStaff->name,
                'id' => $this->assignedStaff->id
            ];
        }
        
        return null;
    }

    // Check if ticket is assigned to a specific staff member
    public function isAssignedToStaff($staffId)
    {
        return $this->assigned_staff_id == $staffId;
    }
}
