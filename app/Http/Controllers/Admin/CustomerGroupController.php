<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\GenericMail;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class CustomerGroupController extends Controller
{
    /**
     * Danh sách nhóm khách hàng
     */
    public function index()
    {
        $groups = CustomerGroup::orderByDesc('id')->get();
        return view('backend.customer_groups.index', compact('groups'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        return view('backend.customer_groups.create');
    }

    /**
     * Lưu nhóm mới
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'account_id' => 'required|string|max:20|unique:users',
            'company'    => 'required|string|max:255',
            'address'    => 'nullable|string|max:500',
            'groups'     => 'nullable|array',
            'groups.*'   => 'exists:customer_groups,id',
        ], [
            'name.required'       => 'Họ tên không được để trống.',
            'email.required'      => 'Email không được để trống.',
            'email.unique'        => 'Email này đã được sử dụng.',
            'account_id.required' => 'Số điện thoại không được để trống.',
            'account_id.unique'   => 'Số điện thoại này đã được sử dụng.',
            'company.required'    => 'Tên công ty không được để trống.',
            'groups.*.exists'     => 'Nhóm khách hàng không hợp lệ.',
        ]);

        $password = '123456';

        DB::beginTransaction();
        try {
            // ✅ Tạo user
            $user = User::create([
                'name'                => $validated['name'],
                'email'               => $validated['email'],
                'account_id'          => $validated['account_id'],
                'company'             => $validated['company'],
                'address'             => $validated['address'] ?? null,
                'password'            => Hash::make($password),
                'role'                => 3,
                'is_active'           => $request->boolean('is_active'),
                'must_update_profile' => true,
            ]);


            if (!empty($validated['groups'])) {
                $user->groups()->sync($validated['groups']);
            }

            DB::commit();

    
            $template = EmailTemplate::where('code', 'new_user')
                ->where('is_active', true)
                ->first();

            if ($template) {
                try {
                    Mail::to($user->email)->queue(new GenericMail(
                        $template,
                        [
                            'user_name'    => $user->name,
                            'user_email'   => $user->email,
                            'new_password' => $password,
                            'login_link'   => route('login'),
                            'app_name'     => config('app.name'),
                        ]
                    ));
                } catch (\Exception $e) {
                    Log::error('Mail gửi thất bại: ' . $e->getMessage());
                }
            }

            return redirect()->route('customers.index')
                ->with('success', 'Thêm khách hàng thành công và gửi email thông báo!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo user: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Form sửa nhóm
     */
    public function edit($id)
    {
        $group = CustomerGroup::findOrFail($id);
        return view('backend.customer_groups.edit', compact('group'));
    }

    /**
     * Cập nhật nhóm
     */
    public function update(Request $request, $id)
    {
        $group = CustomerGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:255|unique:customer_groups,name,' . $group->id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên nhóm khách hàng',
            'name.unique' => 'Tên nhóm này đã tồn tại, vui lòng chọn tên khác',
            'name.max' => 'Tên nhóm không được vượt quá 255 ký tự',
            'description.string' => 'Mô tả phải là chuỗi ký tự',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $group->update($validated);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Cập nhật nhóm khách hàng thành công!');
    }

    /**
     * Xóa nhóm
     */
    public function destroy($id)
    {
        $group = CustomerGroup::findOrFail($id);

        // Kiểm tra xem nhóm có khách hàng không (nếu cần)
        if ($group->users()->count() > 0) {
            return redirect()->route('admin.customer-groups.index')
                ->with('error', 'Không thể xóa nhóm vì còn khách hàng thuộc nhóm này!');
        }

        $group->delete();

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Đã xóa nhóm khách hàng!');
    }
}
