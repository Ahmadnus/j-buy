@extends('admin.layouts.app')

@php $isEdit = $rate->exists; @endphp

@section('title', $isEdit ? __('dashboard.shipping_edit') : __('dashboard.shipping_new'))
@section('page_title', $isEdit ? __('dashboard.shipping_edit') : __('dashboard.shipping_new'))

@section('content')

<div class="max-w-xl">
    @if ($errors->any())
        <div class="mb-4 p-3 border border-red-300 bg-red-50 text-red-700 rounded text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $isEdit ? route('dashboard.shipping.update', $rate) : route('dashboard.shipping.store') }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1">{{ __('dashboard.shipping_city_ar') }} *</label>
            <input type="text" name="city_ar" required maxlength="100"
                   value="{{ old('city_ar', $rate->city_ar) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">{{ __('dashboard.shipping_city_en') }}</label>
            <input type="text" name="city_en" maxlength="100"
                   value="{{ old('city_en', $rate->city_en) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                {{ __('dashboard.shipping_price') }} (JOD) *
            </label>
            <input type="number" name="price" required step="0.001" min="0" max="9999.999"
                   value="{{ old('price', $rate->exists ? number_format((float) $rate->price, 3, '.', '') : '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono" dir="ltr">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">{{ __('dashboard.shipping_sort_order') }}</label>
            <input type="number" name="sort_order" min="0" max="65535"
                   value="{{ old('sort_order', $rate->sort_order ?? 0) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded text-sm font-mono" dir="ltr">
            <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.shipping_sort_hint') }}</p>
        </div>

        <label class="flex items-center gap-2 pt-2">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $rate->exists ? $rate->is_active : true) ? 'checked' : '' }}>
            <span class="text-sm">{{ __('dashboard.shipping_is_active') }}</span>
        </label>

        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="btn-primary px-4 py-2 rounded text-sm font-semibold">
                {{ $isEdit ? __('dashboard.save_changes') : __('dashboard.create') }}
            </button>
            <a href="{{ route('dashboard.shipping.index') }}"
               class="btn-secondary px-4 py-2 rounded text-sm">
                {{ __('dashboard.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection