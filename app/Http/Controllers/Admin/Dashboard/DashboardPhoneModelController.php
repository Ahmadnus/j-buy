<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\PhoneModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardPhoneModelController extends Controller
{
    public function index(Request $request)
    {
        $q        = $request->get('q');
        $brandId  = $request->integer('brand') ?: null;

        $models = PhoneModel::with('category')
            ->withCount('products')
            ->when($brandId, fn ($qb) => $qb->where('category_id', $brandId))
            ->when($q, fn ($qb) => $qb->where(fn ($w) => $w->where('name_ar', 'like', "%$q%")
                                                          ->orWhere('name_en', 'like', "%$q%")))
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->orderByDesc('release_year')
            ->paginate(30)
            ->withQueryString();

        return view('admin.phone_models.index', [
            'models' => $models,
            'brands' => Category::orderBy('sort_order')->get(),
            'q'      => $q,
            'brand'  => $brandId,
        ]);
    }

    public function create()
    {
        return view('admin.phone_models.form', [
            'model'  => new PhoneModel(),
            'brands' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function edit(int $id)
    {
        return view('admin.phone_models.form', [
            'model'  => PhoneModel::findOrFail($id),
            'brands' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }
        PhoneModel::create($data);

        return redirect()->route('dashboard.phone_models.index')
                         ->with('success', __('dashboard.created_success'));
    }

    public function update(Request $request, int $id)
    {
        $model = PhoneModel::findOrFail($id);
        $data  = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }
        $model->update($data);

        return redirect()->route('dashboard.phone_models.index')
                         ->with('success', __('dashboard.updated_success'));
    }

    public function destroy(int $id)
    {
        // Parts linked to this model are kept; their phone_model_id becomes null.
        PhoneModel::findOrFail($id)->delete();

        return redirect()->route('dashboard.phone_models.index')
                         ->with('success', __('dashboard.deleted_success'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name_ar'      => 'required|string|max:255',
            'name_en'      => 'required|string|max:255',
            'release_year' => 'nullable|integer|min:2000|max:2100',
            'sort_order'   => 'nullable|integer|min:0|max:65535',
            'is_active'    => 'nullable|boolean',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]) + [
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order'),
        ];
    }

    private function storeImage($file): string
    {
        $name = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('phone-models', $name, 'public');
        return Storage::disk('public')->url('phone-models/' . $name);
    }
}
