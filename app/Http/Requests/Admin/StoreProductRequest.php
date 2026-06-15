<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Core fields — both Arabic and English required (bilingual requirement)
            'name_ar'      => 'required|string|max:500',
            'name_en'      => 'required|string|max:500',
            'product_code' => 'required|string|max:100|unique:products,product_code',
            'category_id'  => 'required|integer|exists:categories,id',
            'price'        => 'required|numeric|min:0|max:99999.99',
            'currency'     => 'nullable|string|max:10',

            // Primary thumbnail — required on create
            'image'        => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',

            // Optional descriptive fields
            'material_ar'  => 'nullable|string|max:500',
            'material_en'  => 'nullable|string|max:500',
            'badge'        => 'nullable|string|max:100',
            'badge_en'     => 'nullable|string|max:100',
            'size_range'   => 'nullable|string|max:200',

            // Metrics
            'rating'       => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'is_active'    => 'boolean',

            // Gallery images (optional — multiple allowed)
            'gallery'      => 'nullable|array|max:10',
            'gallery.*'    => 'image|mimes:jpg,jpeg,png,webp|max:5120',

            // Colors (optional inline creation)
            'colors'             => 'nullable|array',
            'colors.*.name_ar'   => 'required_with:colors|string|max:100',
            'colors.*.hex_code'  => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'colors.*.sort_order'=> 'nullable|integer|min:0',

            // Sizes (optional inline creation)
            'sizes'                  => 'nullable|array',
            'sizes.*.label'          => 'required_with:sizes|string|max:20',
            'sizes.*.is_available'   => 'boolean',
            'sizes.*.sort_order'     => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_code.unique'  => 'كود المنتج مستخدم مسبقاً',
            'category_id.exists'   => 'الفئة المحددة غير موجودة',
            'image.required'       => 'يجب رفع صورة رئيسية للمنتج',
            'colors.*.hex_code.regex' => 'يجب أن يكون الكود اللوني بصيغة #RRGGBB',
        ];
    }
}