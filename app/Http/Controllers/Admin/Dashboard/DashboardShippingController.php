<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardShippingController extends Controller
{
    /** GET /admin/shipping — list all rates */
    public function index(): View
    {
        $rates = ShippingRate::query()
            ->orderBy('sort_order')
            ->orderBy('city_ar')
            ->get();

        return view('admin.shipping.index', compact('rates'));
    }

    /** GET /admin/shipping/create — new-rate form */
    public function create(): View
    {
        return view('admin.shipping.form', ['rate' => new ShippingRate()]);
    }

    /** POST /admin/shipping — store a new rate */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        ShippingRate::create($data);

        return redirect()->route('dashboard.shipping.index')
            ->with('success', __('dashboard.shipping_created'));
    }

    /** GET /admin/shipping/{rate}/edit */
    public function edit(ShippingRate $rate): View
    {
        return view('admin.shipping.form', compact('rate'));
    }

    /** PUT /admin/shipping/{rate} */
    public function update(Request $request, ShippingRate $rate): RedirectResponse
    {
        $data = $this->validated($request, $rate->id);
        $rate->update($data);

        return redirect()->route('dashboard.shipping.index')
            ->with('success', __('dashboard.shipping_updated'));
    }

    /** DELETE /admin/shipping/{rate} */
    public function destroy(ShippingRate $rate): RedirectResponse
    {
        $rate->delete();
        return redirect()->route('dashboard.shipping.index')
            ->with('success', __('dashboard.shipping_deleted'));
    }

    /**
     * Shared validation — `$ignoreId` lets update() exempt the current row
     * from the `city_ar` uniqueness check.
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $cityArRule = $ignoreId
            ? "required|string|max:100|unique:shipping_rates,city_ar,{$ignoreId}"
            : 'required|string|max:100|unique:shipping_rates,city_ar';

        return $request->validate([
            'city_ar'    => $cityArRule,
            'city_en'    => 'nullable|string|max:100',
            'price'      => 'required|numeric|min:0|max:9999.999',
            'is_active'  => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0|max:65535',
        ]) + [
            'is_active'  => $request->boolean('is_active'),
            'sort_order' => (int) $request->input('sort_order', 0),
        ];
    }
}