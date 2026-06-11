<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('sort_order')->paginate(30);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category()]);
    }

    public function edit(int $id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);
        $data['image_url'] = $request->hasFile('image')
            ? $this->storeImage($request->file('image'))
            : null;

        Category::create($data);
        return redirect()->route('dashboard.categories.index')
                         ->with('success', __('dashboard.created_success'));
    }

    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);
        $data     = $this->validated($request, $category->id);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $category->update($data);
        return redirect()->route('dashboard.categories.index')
                         ->with('success', __('dashboard.updated_success'));
    }

    public function destroy(int $id)
    {
        Category::findOrFail($id)->delete();
        return redirect()->route('dashboard.categories.index')
                         ->with('success', __('dashboard.deleted_success'));
    }

    private function validated(Request $request, ?int $id): array
    {
        return $request->validate([
            'name_ar'    => 'required|string|max:255',
            'name_en'    => 'required|string|max:255',
            'slug'       => "required|string|max:100|unique:categories,slug,{$id}|regex:/^[a-z0-9_-]+$/i",
            'icon'       => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]) + [
            'is_active'  => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order'),
        ];
    }

    private function storeImage($file): string
    {
        $name = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('categories', $name, 'public');
        return Storage::disk('public')->url('categories/' . $name);
    }
}