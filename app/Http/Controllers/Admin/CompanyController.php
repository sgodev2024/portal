<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    public function index()
    {
        $page = "Thông tin công ty";
        $title = "Thông tin công ty";
        $company = Company::first();
        return view('backend.company.index', compact('company', 'page', 'title'));
    }


    public function store(CompanyRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('company_logo')) {
            if (!empty($request->id)) {
                $oldCompany = Company::find($request->id);
                if ($oldCompany && $oldCompany->company_logo) {
                    Storage::disk('public')->delete($oldCompany->company_logo);
                }
            }
            $data['company_logo'] = $request->file('company_logo')->store('company', 'public');
        }
        Company::updateOrCreate(['id' => $request->input('id')], $data);

        toastr()->success('Cập nhật thông tin công ty thành công.');
        return redirect()->back();
    }
}
