<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\StmtController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Staff\ChatControllers;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Customer\TicketController;
use App\Http\Controllers\Admin\GroupStaffController;
use App\Http\Controllers\Staff\StaffGroupController;
use App\Http\Controllers\Admin\CustomerGroupController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\NotificationCounterController;
use App\Http\Controllers\Customer\FileManagerController;
use App\Http\Controllers\Customer\ChatCustomerController;
use App\Http\Controllers\Customer\FileCustomerController;
use App\Http\Controllers\Admin\AdminFileManagerController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Staff\StaffNotificationController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerNotificationController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;


Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('forgot-password', [LoginController::class, 'showForgotForm'])->name('password.request');
    Route::post('forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [LoginController::class, 'resetPassword'])->name('password.update');
});
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
// Notifications unread count (for authenticated users)
Route::get('/notifications/unread-count', [NotificationCounterController::class, 'unreadCount'])->middleware('auth')->name('notifications.unread_count');
// route admin
Route::prefix('admin')->middleware(['auth', 'checkRole:1'])->group(function () {

    Route::prefix('file-manager')->name('admin.file_manager.')->group(function () {
        Route::get('/', [AdminFileManagerController::class, 'index'])->name('index');
        Route::post('/upload', [AdminFileManagerController::class, 'upload'])->name('upload'); // ← THÊM
        Route::get('/folders', [AdminFileManagerController::class, 'folders'])->name('folders');
        Route::get('/download-history', [AdminFileManagerController::class, 'downloadHistory'])->name('download_history');
        Route::get('/activities', [AdminFileManagerController::class, 'activities'])->name('activities');
        Route::get('/storage-quota', [AdminFileManagerController::class, 'storageQuota'])->name('storage_quota');
        Route::put('/storage-quota/{userId}', [AdminFileManagerController::class, 'updateQuota'])->name('update_quota');
        Route::get('/files/{id}', [AdminFileManagerController::class, 'showFile'])->name('show_file');
        Route::get('/files/{id}/download', [AdminFileManagerController::class, 'download'])->name('download');
        Route::delete('/files/{id}', [AdminFileManagerController::class, 'deleteFile'])->name('delete_file');
    });

    // Trang dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Quản lý thông tin công ty
    Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
    Route::post('/company', [CompanyController::class, 'store'])->name('company.store');

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{id}/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/{id}/assign', [ChatController::class, 'assign'])->name('assign');
        Route::post('/{id}/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::get('/list-updates', [ChatController::class, 'getListUpdates']);
    });
    Route::prefix('stmt')->name('admin.stmt.')->group(function () {
        Route::get('/', [StmtController::class, 'index'])->name('index');
        Route::post('/update', [StmtController::class, 'update'])->name('update');
    });

    // Quản lý nhân viên
    Route::prefix('staffs')->name('admin.staffs.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/store', [StaffController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [StaffController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StaffController::class, 'update'])->name('update');
        Route::post('/delete-selected', [StaffController::class, 'deleteSelected'])->name('deleteSelected');
        Route::post('/import', [StaffController::class, 'import'])->name('import');
        Route::get('/download-template', [StaffController::class, 'downloadTemplate'])->name('downloadTemplate');
    });
    Route::prefix('email-templates')->name('admin.email_templates.')->group(function () {
        Route::resource('/', EmailTemplateController::class)->parameters([
            '' => 'email_template'
        ]);
    });
    Route::resource('notifications', AdminNotificationController::class)
        ->names([
            'index' => 'admin.notifications.index',
            'create' => 'admin.notifications.create',
            'store' => 'admin.notifications.store',
            'show' => 'admin.notifications.show',
            'edit' => 'admin.notifications.edit',
            'update' => 'admin.notifications.update',
            'destroy' => 'admin.notifications.destroy',
        ]);

    // Quản lý nhóm khách hàng
    Route::prefix('customer-groups')->name('admin.customer-groups.')->group(function () {
        Route::get('/', [CustomerGroupController::class, 'index'])->name('index');
        Route::get('/create', [CustomerGroupController::class, 'create'])->name('create');
        Route::post('/', [CustomerGroupController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CustomerGroupController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CustomerGroupController::class, 'update'])->name('update');
        Route::delete('/{id}', [CustomerGroupController::class, 'destroy'])->name('destroy');
    });

    // Quản lý nhân viên - nhóm khách hàng
    Route::prefix('group-staff')->name('admin.group-staff.')->group(function () {
        Route::get('/', [GroupStaffController::class, 'index'])->name('index');
        Route::post('/assign', [GroupStaffController::class, 'assign'])->name('assign');
        Route::delete('/{groupId}/{staffId}', [GroupStaffController::class, 'remove'])->name('remove');
        
        Route::post('/{groupId}/reassign-tickets', [GroupStaffController::class, 'reassignUnassignedTickets'])->name('reassign-tickets');
    });
});
// route admin, nhân viên

Route::prefix('admin')->middleware(['auth', 'checkRole:1'])->group(function () {
    // QUẢN LÝ FILE THỐNG NHẤT (Báo cáo + Biểu mẫu)
    Route::prefix('files')->name('admin.files.')->group(function () {
        Route::get('/', [FileController::class, 'index'])->name('index');
        Route::get('/reports', [FileController::class, 'reports'])->name('reports');
        Route::get('/templates', [FileController::class, 'templates'])->name('templates');
        Route::get('/create', [FileController::class, 'create'])->name('create');
        Route::get('/create-report', [FileController::class, 'create'])->name('create_report');
        Route::get('/create-template', [FileController::class, 'create'])->name('create_template');
        Route::post('/', [FileController::class, 'store'])->name('store');
        // Tìm kiếm khách hàng/nhóm cho chọn người nhận
        Route::get('/recipients/search', function (\Illuminate\Http\Request $request) {
            $term = trim($request->get('q', ''));
            $users = \App\Models\User::where('role', 3)
                ->where('is_active', 1)
                ->when($term, function ($q) use ($term) {
                    $q->where(function ($qq) use ($term) {
                        $qq->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });
                })
                ->limit(20)
                ->get(['id', 'name', 'email'])
                ->map(function ($u) {
                    return ['id' => $u->id, 'text' => $u->name . ' (' . $u->email . ')'];
                });

            $groups = \App\Models\CustomerGroup::when($term, function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%");
            })
                ->limit(20)
                ->get(['id', 'name'])
                ->map(function ($g) {
                    return ['id' => $g->id, 'text' => $g->name];
                });

            return response()->json([
                'users' => $users,
                'groups' => $groups,
            ]);
        })->name('recipients.search');
        Route::get('/{id}', [FileController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [FileController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FileController::class, 'update'])->name('update');
        Route::get('/{id}/download', [FileController::class, 'download'])->name('download');
        Route::delete('/{id}', [FileController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/resend', [FileController::class, 'resend'])->name('resend');
    });
});
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::prefix('tickets')->middleware('checkRole:1,2')->name('admin.tickets.')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminTicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [AdminTicketController::class, 'reply'])->name('reply');
        Route::patch('/{id}/close', [AdminTicketController::class, 'close'])->name('close');
        Route::get('/{id}/messages', [AdminTicketController::class, 'getMessages'])->name('messages');
        Route::post('/{id}/assign', [AdminTicketController::class, 'assign'])
            ->name('assign')
            ->middleware('checkRole:1');
    });
});
Route::prefix('customers')->name('customers.')->middleware(['auth', 'checkRole:1,2'])->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/store', [CustomerController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [CustomerController::class, 'update'])->name('update');
    Route::post('/import', [CustomerController::class, 'import'])->name('import');
    Route::get('/downTemplates', [CustomerController::class, 'downTemplates'])
        ->name('downTemplates');
    Route::get('/{id}', [CustomerController::class, 'show'])->name('show');
    Route::post('/bulk-action', [CustomerController::class, 'bulkAction'])->name('bulkAction');
    Route::post('/bulk-action', [CustomerController::class, 'bulkAction'])->name('bulkAction');
    Route::get('{id}/reset-password', [CustomerController::class, 'resetPassword'])->name('resetPassword');
});
// route nhân viên
Route::prefix('staff')->name('staff.')->middleware(['auth', 'checkRole:2'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chats', [ChatControllers::class, 'index'])->name('chats.index');
    Route::get('/chats/{id}', [ChatControllers::class, 'show'])->name('chats.show');
    Route::post('/chats/{id}/send', [ChatControllers::class, 'send'])->name('chats.send');
    Route::post('/chats/{id}/mark-read', [ChatControllers::class, 'markAsRead'])->name('chats.mark-read');
    Route::get('/chats/{id}/messages', [ChatControllers::class, 'getMessages'])->name('chats.messages');
    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function () {
            Route::get('/', [StaffNotificationController::class, 'index'])->name('index');
            Route::get('/{id}', [StaffNotificationController::class, 'show'])->name('show');
            Route::get('/datatable/data', [StaffNotificationController::class, 'data'])->name('data');
        });

    Route::get('/groups', [StaffGroupController::class, 'index'])
        ->name('groups.index');
    Route::post('/groups/{groupId}/claim', [StaffGroupController::class, 'claim'])
        ->name('groups.claim');
    Route::delete('/groups/{groupId}/leave', [StaffGroupController::class, 'leave'])
        ->name('groups.leave');
});

Route::prefix('customer')->name('customer.')->middleware(['auth', 'checkRole:3'])->group(function () {
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::get('/chat', [ChatCustomerController::class, 'index'])->name('chatcustomer.index');
    Route::get('/chat/messages', [ChatCustomerController::class, 'getMessages'])->name('chatcustomer.messages');
    Route::post('/chat/send', [ChatCustomerController::class, 'send'])->name('chatcustomer.send');
});
// route khách hàng

Route::prefix('customer')->name('customer.')->middleware(['auth', 'checkRole:3', 'must.update.profile'])->group(function () {

    Route::prefix('files')->name('files.')->group(function () {
        Route::get('/reports', [FileCustomerController::class, 'reports'])->name('reports');
        Route::get('/reports/{id}', [FileCustomerController::class, 'showReport'])->name('show_report');
        Route::get('/reports/{id}/download', [FileCustomerController::class, 'downloadReport'])->name('download_report');
        Route::get('/templates', [FileCustomerController::class, 'templates'])->name('templates');
        Route::get('/templates/{id}/download', [FileCustomerController::class, 'downloadTemplate'])->name('download_template');
        Route::get('/my-downloads', [FileCustomerController::class, 'myDownloads'])->name('my_downloads');
    });

    // 3. FILE MANAGER CÁ NHÂN
    // Route::prefix('files')->name('files.')->group(function () {
    //     Route::get('/', [FileManagerController::class, 'index'])->name('index');
    //     Route::post('/upload', [FileManagerController::class, 'upload'])->name('upload');
    //     Route::post('/create-folder', [FileManagerController::class, 'createFolder'])->name('create_folder');
    //     Route::get('/{id}/download', [FileManagerController::class, 'download'])->name('download');
    //     Route::delete('/{id}', [FileManagerController::class, 'deleteFile'])->name('delete');
    //     Route::post('/{id}/rename', [FileManagerController::class, 'renameFile'])->name('rename');
    //     Route::post('/{id}/move', [FileManagerController::class, 'moveFile'])->name('move');
    //     Route::get('/activities/list', [FileManagerController::class, 'activities'])->name('activities');
    // });
});

Route::prefix('customer')->name('customer.')->middleware(['auth', 'checkRole:3', 'must.update.profile'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{id}', [TicketController::class, 'show'])->name('show');
        Route::post('/{id}/reply', [TicketController::class, 'reply'])->name('reply');
        Route::get('/{id}/messages', [TicketController::class, 'getMessages'])->name('messages');
    });
    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function () {
            Route::get('/', [CustomerNotificationController::class, 'index'])->name('index');
            Route::get('/{id}', [CustomerNotificationController::class, 'show'])->name('show');
            Route::get('/datatable/data', [CustomerNotificationController::class, 'data'])->name('data');
        });
});
