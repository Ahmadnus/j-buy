<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class DashboardBranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount('orders')->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.form', ['branch' => new Branch()]);
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.form', compact('branch'));
    }

    public function store(Request $request)
    {
        Branch::create($this->validated($request));
        return redirect()->route('dashboard.branches.index')
                         ->with('success', __('dashboard.created_success'));
    }

    public function update(Request $request, Branch $branch)
    {
        $branch->update($this->validated($request));
        return redirect()->route('dashboard.branches.index')
                         ->with('success', __('dashboard.updated_success'));
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('dashboard.branches.index')
                         ->with('success', __('dashboard.deleted_success'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name_ar'       => 'required|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'city_ar'       => 'nullable|string|max:100',
            'address_ar'    => 'required|string|max:500',
            'address_en'    => 'nullable|string|max:500',
            'phone'         => 'nullable|string|max:30',
            'working_hours' => 'nullable|string|max:255',
            'latitude'      => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude'     => 'nullable|numeric|between:-180,180|required_with:latitude',
            'sort_order'    => 'nullable|integer|min:0|max:65535',
            'is_active'     => 'nullable|boolean',
        ]) + [
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order'),
        ];
    }
}
