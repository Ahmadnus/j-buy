@extends('admin.layouts.app')

@php $isEdit = $banner->exists; @endphp

@section('title', $isEdit ? __('dashboard.banner_edit') : __('dashboard.banner_new'))
@section('page_title', $isEdit ? __('dashboard.banner_edit') : __('dashboard.banner_new'))

@section('content')

<div class="max-w-3xl">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">
            {{ $isEdit ? __('dashboard.banner_edit') : __('dashboard.banner_new') }}
        </h2>
        <a href="{{ route('dashboard.banners.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">
            ← {{ __('dashboard.back') }}
        </a>
    </div>

    <form method="POST"
          action="{{ $isEdit ? route('dashboard.banners.update', $banner->id) : route('dashboard.banners.store') }}"
          enctype="multipart/form-data"
          class="space-y-5 bg-white border border-gray-200 rounded-lg p-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        {{-- Titles --}}
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.banner_title_ar') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="title_ar" required
                       value="{{ old('title_ar', $banner->title_ar) }}"
                       class="w-full px-3 py-2 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.banner_title_en') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="title_en"
                       value="{{ old('title_en', $banner->title_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        {{-- Subtitles --}}
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.banner_subtitle_ar') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="subtitle_ar" required
                       value="{{ old('subtitle_ar', $banner->subtitle_ar) }}"
                       class="w-full px-3 py-2 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.banner_subtitle_en') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="subtitle_en"
                       value="{{ old('subtitle_en', $banner->subtitle_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        {{-- CTA text --}}
        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.banner_cta_ar') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="cta_text_ar" required
                       value="{{ old('cta_text_ar', $banner->cta_text_ar) }}"
                       class="w-full px-3 py-2 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.banner_cta_en') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="text" name="cta_text_en"
                       value="{{ old('cta_text_en', $banner->cta_text_en) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        {{-- Image --}}
        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.banner_image') }}</label>

            @if ($isEdit && $banner->image_url)
                <div class="mb-3">
                    <img src="{{ $banner->image_url }}" alt=""
                         class="w-full max-w-md h-32 object-cover border border-gray-200 rounded-md">
                    <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.current_image') }}</p>
                </div>
            @endif

            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                   class="w-full px-3 py-2 rounded-md text-sm bg-white">
            @if ($isEdit)
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.leave_blank') }}</p>
            @endif
        </div>

        {{-- Style + link --}}
        <div class="grid md:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.banner_bg_color') }}</label>
                <input type="text" name="background_color"
                       value="{{ old('background_color', $banner->background_color ?? '#FFFFFF') }}"
                       pattern="#[0-9A-Fa-f]{6}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr"
                       placeholder="#FFFFFF">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.banner_link_type') }}</label>
                <select name="link_type" class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
                    @foreach (['none', 'product', 'category', 'url'] as $type)
                        <option value="{{ $type }}"
                                {{ old('link_type', $banner->link_type ?? 'none') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.banner_link_value') }}</label>
                <input type="text" name="link_value"
                       value="{{ old('link_value', $banner->link_value) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        <div>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $isEdit ? $banner->is_active : true) ? 'checked' : '' }}
                       class="accent-black">
                <span class="text-sm font-medium">{{ __('dashboard.banner_active') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="btn-primary px-5 py-2.5 rounded-md text-sm font-semibold">
                {{ $isEdit ? __('dashboard.update') : __('dashboard.create') }}
            </button>
            <a href="{{ route('dashboard.banners.index') }}"
               class="btn-secondary px-5 py-2.5 rounded-md text-sm">
                {{ __('dashboard.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection