@extends('admin.layouts.app')

@php $isEdit = $model->exists; @endphp

@section('title', $isEdit ? __('dashboard.phone_model_edit') : __('dashboard.phone_model_new'))
@section('page_title', $isEdit ? __('dashboard.phone_model_edit') : __('dashboard.phone_model_new'))

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">{{ $isEdit ? __('dashboard.phone_model_edit') : __('dashboard.phone_model_new') }}</h2>
        <a href="{{ route('dashboard.phone_models.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">← {{ __('dashboard.back') }}</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 border border-red-300 bg-red-50 text-red-700 rounded text-sm">
            <ul class="list-disc list-inside space-y-1">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $isEdit ? route('dashboard.phone_models.update', $model->id) : route('dashboard.phone_models.store') }}"
          class="space-y-5 bg-white border border-gray-200 rounded-lg p-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.phone_model_brand') }} <span class="text-red-600">*</span></label>
            <select name="category_id" required class="w-full px-3 py-2 rounded-md text-sm">
                <option value="">—</option>
                @foreach ($brands as $b)
                    <option value="{{ $b->id }}" @selected(old('category_id', $model->category_id) == $b->id)>{{ $b->name_ar }} / {{ $b->name_en }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.phone_model_brand_hint') }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.name_ar') }} <span class="text-red-600">*</span></label>
                <input type="text" name="name_ar" required value="{{ old('name_ar', $model->name_ar) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" placeholder="آيفون 13 برو">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.name_en') }} <span class="text-red-600">*</span></label>
                <input type="text" name="name_en" required value="{{ old('name_en', $model->name_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr" placeholder="iPhone 13 Pro">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.phone_model_year') }}</label>
                <input type="number" name="release_year" min="2000" max="2100" value="{{ old('release_year', $model->release_year) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.category_sort_order') }}</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $model->sort_order ?? 0) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.category_image') }}</label>
            @if ($isEdit && $model->image_url)
                <img src="{{ $model->image_url }}" alt="" class="w-24 h-24 object-cover border border-gray-200 rounded-md mb-2">
            @endif
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full px-3 py-2 rounded-md text-sm bg-white">
        </div>

        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="accent-brand"
                   {{ old('is_active', $isEdit ? $model->is_active : true) ? 'checked' : '' }}>
            <span class="text-sm font-medium">{{ __('dashboard.active') }}</span>
        </label>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="btn-primary px-5 py-2.5 rounded-md text-sm font-semibold">
                {{ $isEdit ? __('dashboard.update') : __('dashboard.create') }}
            </button>
            <a href="{{ route('dashboard.phone_models.index') }}" class="btn-secondary px-5 py-2.5 rounded-md text-sm">{{ __('dashboard.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
