<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardBannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->paginate(30);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner()]);
    }

    public function edit(int $id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.form', compact('banner'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image_url'] = $request->hasFile('image')
            ? $this->storeImage($request->file('image'))
            : '';

        Banner::create($data);
        return redirect()->route('dashboard.banners.index')
                         ->with('success', __('dashboard.created_success'));
    }

    public function update(Request $request, int $id)
    {
        $banner = Banner::findOrFail($id);
        $data   = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $banner->update($data);
        return redirect()->route('dashboard.banners.index')
                         ->with('success', __('dashboard.updated_success'));
    }

    public function destroy(int $id)
    {
        Banner::findOrFail($id)->delete();
        return redirect()->route('dashboard.banners.index')
                         ->with('success', __('dashboard.deleted_success'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'nullable|string|max:255',
            'subtitle_ar'      => 'required|string|max:255',
            'subtitle_en'      => 'nullable|string|max:255',
            'cta_text_ar'      => 'required|string|max:100',
            'cta_text_en'      => 'nullable|string|max:100',
            'background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'link_type'        => 'nullable|in:product,category,url,none',
            'link_value'       => 'nullable|string|max:255',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'nullable|boolean',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]) + [
            'is_active'        => $request->boolean('is_active', true),
            'sort_order'       => $request->integer('sort_order'),
            'background_color' => $request->input('background_color') ?: '#FFFFFF',
            'link_type'        => $request->input('link_type') ?: 'none',
        ];
    }

    private function storeImage($file): string
    {
        $name = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('banners', $name, 'public');
        return Storage::disk('public')->url('banners/' . $name);
    }
}