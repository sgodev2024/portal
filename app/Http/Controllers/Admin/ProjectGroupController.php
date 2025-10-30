<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectGroup;
use Illuminate\Http\Request;

class ProjectGroupController extends Controller
{
    /**
     * Generate mã dự án tự động: 00001 + 001 + XX (2 số random)
     */
    private function generateProjectCode()
    {
        do {
            // 00001 + 001 + random 2 số (00-99)
            $randomSuffix = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
            $code = '00001001' . $randomSuffix;
        } while (ProjectGroup::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Danh sách nhóm dự án
     */
    public function index()
    {
        $projectGroups = ProjectGroup::orderByDesc('id')->get();
        return view('backend.project_groups.index', compact('projectGroups'));
    }

    /**
     * Form thêm mới
     */
    public function create()
    {
        return view('backend.project_groups.create');
    }

    /**
     * Lưu nhóm dự án mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'total_units' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Vui lòng nhập tên nhóm dự án',
            'name.max' => 'Tên nhóm không được vượt quá 255 ký tự',
            'location.max' => 'Vị trí không được vượt quá 255 ký tự',
            'total_units.integer' => 'Tổng số căn hộ phải là số nguyên',
            'total_units.min' => 'Tổng số căn hộ phải lớn hơn hoặc bằng 0',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        // Auto-generate mã dự án
        $validated['code'] = $this->generateProjectCode();

        ProjectGroup::create($validated);

        return redirect()->route('admin.project-groups.index')
            ->with('success', 'Thêm nhóm dự án thành công! Mã dự án: ' . $validated['code']);
    }

    /**
     * Form sửa nhóm dự án
     */
    public function edit($id)
    {
        $projectGroup = ProjectGroup::findOrFail($id);
        return view('backend.project_groups.edit', compact('projectGroup'));
    }

    /**
     * Cập nhật nhóm dự án
     */
    public function update(Request $request, $id)
    {
        $projectGroup = ProjectGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'total_units' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Vui lòng nhập tên nhóm dự án',
            'name.max' => 'Tên nhóm không được vượt quá 255 ký tự',
            'location.max' => 'Vị trí không được vượt quá 255 ký tự',
            'total_units.integer' => 'Tổng số căn hộ phải là số nguyên',
            'total_units.min' => 'Tổng số căn hộ phải lớn hơn hoặc bằng 0',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
        ]);

        $projectGroup->update($validated);

        return redirect()->route('admin.project-groups.index')
            ->with('success', 'Cập nhật nhóm dự án thành công!');
    }

    /**
     * Xóa nhóm dự án
     */
    public function destroy($id)
    {
        $projectGroup = ProjectGroup::findOrFail($id);

        // Kiểm tra xem nhóm có khách hàng không
        if ($projectGroup->users()->count() > 0) {
            return redirect()->route('admin.project-groups.index')
                ->with('error', 'Không thể xóa nhóm dự án vì còn khách hàng thuộc nhóm này!');
        }

        $projectGroup->delete();
        return redirect()->route('admin.project-groups.index')
            ->with('success', 'Xóa nhóm dự án thành công!');
    }
}
