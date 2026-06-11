@extends('admin.layouts.app')

@php $isEdit = $product->exists; @endphp

@section('title', $isEdit ? __('dashboard.product_edit') : __('dashboard.product_new'))
@section('page_title', $isEdit ? __('dashboard.product_edit') : __('dashboard.product_new'))

@section('content')

<div class="max-w-4xl">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">
            {{ $isEdit ? __('dashboard.product_edit') : __('dashboard.product_new') }}
        </h2>
        <a href="{{ route('dashboard.products.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">
            ← {{ __('dashboard.back') }}
        </a>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('dashboard.products.update', $product->id) : route('dashboard.products.store') }}"
          enctype="multipart/form-data"
          class="space-y-5 bg-white border border-gray-200 rounded-lg p-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        {{-- ── Bilingual names ────────────────────────────────────────── --}}
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_name_ar') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name_ar" required
                       value="{{ old('name_ar', $product->name_ar) }}"
                       class="w-full px-3 py-2 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_name_en') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name_en" required
                       value="{{ old('name_en', $product->name_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        {{-- ── Code + price + category ─────────────────────────────────── --}}
        <div class="grid md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_code') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="product_code" required
                       value="{{ old('product_code', $product->product_code) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_price') }} <span class="text-red-600">*</span>
                </label>
                <input type="number" name="price" step="0.01" min="0" required
                       value="{{ old('price', $product->price) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.product_currency') }}</label>
                <input type="text" name="currency"
                       value="{{ old('currency', $product->currency ?? 'JOD') }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">
                {{ __('dashboard.product_category') }} <span class="text-red-600">*</span>
            </label>
            <select name="category_id" required class="w-full px-3 py-2 rounded-md text-sm">
                <option value="">—</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name_ar }} / {{ $cat->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ── Bilingual material ─────────────────────────────────────── --}}
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_material_ar') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="material_ar"
                       value="{{ old('material_ar', $product->material_ar) }}"
                       class="w-full px-3 py-2 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_material_en') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="material_en"
                       value="{{ old('material_en', $product->material_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        {{-- ── Bilingual badge + size range ─────────────────────────────── --}}
        <div class="grid md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_badge_ar') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="badge"
                       value="{{ old('badge', $product->badge) }}"
                       class="w-full px-3 py-2 rounded-md text-sm"
                       placeholder="الأعلى تقييماً">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_badge_en') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="badge_en"
                       value="{{ old('badge_en', $product->badge_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr"
                       placeholder="Top Rated">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.product_size_range') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="size_range"
                       value="{{ old('size_range', $product->size_range) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr"
                       placeholder="S - M - L - XL">
            </div>
        </div>

        {{-- ── Image upload ─────────────────────────────────────────────── --}}
        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.product_image') }}</label>

            @if ($isEdit && $product->image_url)
                <div class="mb-3">
                    <img src="{{ $product->image_url }}" alt=""
                         class="w-32 h-32 object-cover border border-gray-200 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.current_image') }}</p>
                </div>
            @endif

            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   class="w-full px-3 py-2 rounded-md text-sm bg-white">
            @if ($isEdit)
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.leave_blank') }}</p>
            @endif
        </div>

        {{-- ── Colors — optional, from predefined dropdown ──────────────── --}}
        <div>
            <label class="block text-sm font-medium mb-1.5">
                {{ __('dashboard.product_colors') }}
                <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
            </label>
            <p class="text-xs text-gray-500 mb-2">{{ __('dashboard.product_colors_hint') }}</p>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-2">
                @foreach ($colors as $color)
                    @php $checked = in_array($color['hex_code'], old('colors', $selectedColors)); @endphp
                    <label class="cursor-pointer flex items-center gap-2 px-3 py-2 border rounded-md text-xs hover:bg-gray-50
                                  {{ $checked ? 'border-black bg-gray-50' : 'border-gray-200' }}">
                        <input type="checkbox" name="colors[]" value="{{ $color['hex_code'] }}"
                               {{ $checked ? 'checked' : '' }}
                               class="accent-black">
                        <span class="inline-block w-4 h-4 rounded-full border border-gray-300 flex-shrink-0"
                              style="background-color: {{ $color['hex_code'] }}"></span>
                        <span>{{ app()->getLocale() === 'ar' ? $color['name_ar'] : $color['name_en'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── Sizes — optional, from predefined checkboxes ─────────────── --}}
        <div>
            <label class="block text-sm font-medium mb-1.5">
                {{ __('dashboard.product_sizes') }}
                <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
            </label>
            <p class="text-xs text-gray-500 mb-2">{{ __('dashboard.product_sizes_hint') }}</p>

            <div class="flex flex-wrap gap-2">
                @foreach ($sizes as $size)
                    @php $checked = in_array($size, old('sizes', $selectedSizes)); @endphp
                    <label class="cursor-pointer px-4 py-2 border rounded-md text-sm font-medium
                                  {{ $checked ? 'bg-black text-white border-black' : 'border-gray-300 hover:bg-gray-50' }}">
                        <input type="checkbox" name="sizes[]" value="{{ $size }}"
                               {{ $checked ? 'checked' : '' }} class="hidden">
                        {{ $size }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ── Active flag ──────────────────────────────────────────────── --}}
        <div>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $isEdit ? $product->is_active : true) ? 'checked' : '' }}
                       class="accent-black">
                <span class="text-sm font-medium">{{ __('dashboard.product_active') }}</span>
            </label>
        </div>

        {{-- ── Submit ───────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="btn-primary px-5 py-2.5 rounded-md text-sm font-semibold">
                {{ $isEdit ? __('dashboard.update') : __('dashboard.create') }}
            </button>
            <a href="{{ route('dashboard.products.index') }}"
               class="btn-secondary px-5 py-2.5 rounded-md text-sm">
                {{ __('dashboard.cancel') }}
            </a>
        </div>
    </form>

</div>

@endsection