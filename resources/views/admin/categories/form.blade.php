@extends('admin.layouts.app')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? __('dashboard.category_edit') : __('dashboard.category_new'))
@section('page_title', $isEdit ? __('dashboard.category_edit') : __('dashboard.category_new'))

@section('content')

<div class="max-w-2xl">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">
            {{ $isEdit ? __('dashboard.category_edit') : __('dashboard.category_new') }}
        </h2>
        <a href="{{ route('dashboard.categories.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">
            ← {{ __('dashboard.back') }}
        </a>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('dashboard.categories.update', $category->id) : route('dashboard.categories.store') }}"
          enctype="multipart/form-data"
          class="space-y-5 bg-white border border-gray-200 rounded-lg p-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.category_name_ar') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name_ar" required
                       value="{{ old('name_ar', $category->name_ar) }}"
                       class="w-full px-3 py-2 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.category_name_en') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="name_en" required
                       value="{{ old('name_en', $category->name_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.category_slug') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="slug" required
                       value="{{ old('slug', $category->slug) }}"
                       pattern="[a-z0-9_-]+"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr"
                       placeholder="womens">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.category_sort_order') }}</label>
                <input type="number" name="sort_order" min="0"
                       value="{{ old('sort_order', $category->sort_order ?? 0) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.category_icon') }}</label>
            <input type="text" name="icon"
                   value="{{ old('icon', $category->icon) }}"
                   class="w-full px-3 py-2 rounded-md text-sm" dir="ltr"
                   placeholder="checkroom_outlined">
            <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.category_icon_hint') }}</p>
        </div>

        {{-- Image upload --}}
        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.category_image') }}</label>

            @if ($isEdit && $category->image_url)
                <div class="mb-3">
                    <img src="{{ $category->image_url }}" alt=""
                         class="w-24 h-24 object-cover border border-gray-200 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.current_image') }}</p>
                </div>
            @endif

            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   class="w-full px-3 py-2 rounded-md text-sm bg-white">
            @if ($isEdit)
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.leave_blank') }}</p>
            @endif
        </div>

        <div>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $isEdit ? $category->is_active : true) ? 'checked' : '' }}
                       class="accent-black">
                <span class="text-sm font-medium">{{ __('dashboard.category_active') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="btn-primary px-5 py-2.5 rounded-md text-sm font-semibold">
                {{ $isEdit ? __('dashboard.update') : __('dashboard.create') }}
            </button>
            <a href="{{ route('dashboard.categories.index') }}"
               class="btn-secondary px-5 py-2.5 rounded-md text-sm">
                {{ __('dashboard.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection