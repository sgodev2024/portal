<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Http\Controllers\Controller;


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
            'name' => 'required|unique:customer_groups,name|max:255',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Vui lòng nhập tên nhóm khách hàng',
            'name.unique' => 'Tên nhóm này đã tồn tại, vui lòng chọn tên khác',
            'name.max' => 'Tên nhóm không được vượt quá 255 ký tự',
            'description.string' => 'Mô tả phải là chuỗi ký tự',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        CustomerGroup::create($validated);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Thêm nhóm khách hàng thành công!');
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
