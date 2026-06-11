<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardProductController extends Controller
{
    /** Predefined colours the admin can pick from. Hex codes are fixed. */
    public const PREDEFINED_COLORS = [
        ['name_ar' => 'أسود',      'name_en' => 'Black',  'hex_code' => '#000000'],
        ['name_ar' => 'أبيض',      'name_en' => 'White',  'hex_code' => '#FFFFFF'],
        ['name_ar' => 'الأحمر',    'name_en' => 'Red',    'hex_code' => '#D32F2F'],
        ['name_ar' => 'الأزرق',    'name_en' => 'Blue',   'hex_code' => '#1976D2'],
        ['name_ar' => 'الأخضر',    'name_en' => 'Green',  'hex_code' => '#2E7D32'],
        ['name_ar' => 'الأصفر',    'name_en' => 'Yellow', 'hex_code' => '#FBC02D'],
        ['name_ar' => 'الزهري',    'name_en' => 'Pink',   'hex_code' => '#E91E63'],
        ['name_ar' => 'بنفسجي',    'name_en' => 'Purple', 'hex_code' => '#7B1FA2'],
        ['name_ar' => 'برتقالي',   'name_en' => 'Orange', 'hex_code' => '#F57C00'],
        ['name_ar' => 'بني',       'name_en' => 'Brown',  'hex_code' => '#5D4037'],
        ['name_ar' => 'رمادي',     'name_en' => 'Gray',   'hex_code' => '#616161'],
        ['name_ar' => 'بيج',       'name_en' => 'Beige',  'hex_code' => '#D7CCC8'],
        ['name_ar' => 'ذهبي',      'name_en' => 'Gold',   'hex_code' => '#D4AF37'],
        ['name_ar' => 'فضي',       'name_en' => 'Silver', 'hex_code' => '#BDBDBD'],
    ];

    public const PREDEFINED_SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'];

    public function index(Request $request)
    {
        $q        = $request->get('q');
        $products = Product::with('category')
            ->when($q, fn ($qb) => $qb->where('name_ar', 'like', "%$q%")
                                       ->orWhere('name_en', 'like', "%$q%")
                                       ->orWhere('product_code', 'like', "%$q%"))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'q'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product'    => new Product(),
            'categories' => Category::orderBy('sort_order')->get(),
            'colors'     => self::PREDEFINED_COLORS,
            'sizes'      => self::PREDEFINED_SIZES,
            'selectedColors' => [],
            'selectedSizes'  => [],
        ]);
    }

    public function edit(int $id)
    {
        $product = Product::with(['colors', 'sizes'])->findOrFail($id);

        // Selected colors: keyed by hex so the checkbox state survives edits.
        $selectedColors = $product->colors->pluck('hex_code')->all();
        $selectedSizes  = $product->sizes->pluck('label')->all();

        return view('admin.products.form', [
            'product'        => $product,
            'categories'     => Category::orderBy('sort_order')->get(),
            'colors'         => self::PREDEFINED_COLORS,
            'sizes'          => self::PREDEFINED_SIZES,
            'selectedColors' => $selectedColors,
            'selectedSizes'  => $selectedSizes,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, null);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        } else {
            $data['image_url'] = '';
        }

        $product = Product::create($data);
        $this->syncVariants($product, $request);

        return redirect()->route('dashboard.products.index')
                         ->with('success', __('dashboard.created_success'));
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $data    = $this->validated($request, $product->id);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $product->update($data);
        $this->syncVariants($product, $request);

        return redirect()->route('dashboard.products.index')
                         ->with('success', __('dashboard.updated_success'));
    }

    public function destroy(int $id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->route('dashboard.products.index')
                         ->with('success', __('dashboard.deleted_success'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function validated(Request $request, ?int $productId): array
    {
        return $request->validate([
            'name_ar'      => 'required|string|max:500',
            'name_en'      => 'required|string|max:500',
            'product_code' => "required|string|max:100|unique:products,product_code,{$productId}",
            'price'        => 'required|numeric|min:0',
            'currency'     => 'nullable|string|max:10',
            'category_id'  => 'required|exists:categories,id',
            'material_ar'  => 'nullable|string|max:500',
            'material_en'  => 'nullable|string|max:500',
            'badge'        => 'nullable|string|max:100',
            'badge_en'     => 'nullable|string|max:100',
            'size_range'   => 'nullable|string|max:200',
            'is_active'    => 'nullable|boolean',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function storeImage($file): string
    {
        $name = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('products', $name, 'public');
        return Storage::disk('public')->url('products/' . $name);
    }

    /**
     * Re-creates the product's colors and sizes from the form input.
     * Empty / absent input clears the variant set.
     */
    private function syncVariants(Product $product, Request $request): void
    {
        // ── Colors ─────────────────────────────────────────────────────────
        $product->colors()->delete();
        $hexCodes = (array) $request->input('colors', []);
        foreach ($hexCodes as $i => $hex) {
            $preset = collect(self::PREDEFINED_COLORS)
                ->firstWhere('hex_code', $hex);
            if (!$preset) continue;
            ProductColor::create([
                'product_id' => $product->id,
                'name_ar'    => $preset['name_ar'],
                'name_en'    => $preset['name_en'],
                'hex_code'   => $preset['hex_code'],
                'sort_order' => $i,
            ]);
        }

        // ── Sizes ──────────────────────────────────────────────────────────
        $product->sizes()->delete();
        $labels = (array) $request->input('sizes', []);
        foreach ($labels as $i => $label) {
            if (!in_array($label, self::PREDEFINED_SIZES, true)) continue;
            ProductSize::create([
                'product_id'   => $product->id,
                'label'        => $label,
                'is_available' => true,
                'sort_order'   => $i,
            ]);
        }
    }
}