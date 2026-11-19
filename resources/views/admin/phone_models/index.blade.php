@extends('admin.layouts.app')

@section('title', __('dashboard.nav_phone_models'))
@section('page_title', __('dashboard.phone_models_title'))

@section('content')

<div class="flex justify-between items-center mb-6 flex-wrap gap-3">
    <h2 class="text-2xl font-bold">{{ __('dashboard.phone_models_title') }}</h2>
    <a href="{{ route('dashboard.phone_models.create') }}" class="btn-primary px-4 py-2 rounded text-sm font-semibold">
        + {{ __('dashboard.phone_model_new') }}
    </a>
</div>

<form method="GET" class="flex flex-wrap gap-3 mb-5">
    <input type="text" name="q" value="{{ $q }}" placeholder="{{ __('dashboard.search') }}..."
           class="px-3 py-2 rounded-md text-sm w-60">
    <select name="brand" class="px-3 py-2 rounded-md text-sm">
        <option value="">{{ __('dashboard.phone_model_all_brands') }}</option>
        @foreach ($brands as $b)
            <option value="{{ $b->id }}" @selected($brand == $b->id)>{{ $b->name_ar }}</option>
        @endforeach
    </select>
    <button class="btn-secondary px-4 py-2 rounded-md text-sm">{{ __('dashboard.search') }}</button>
</form>

<div class="bg-white border border-gray-200 rounded-lg overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-600">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="px-4 py-3 font-medium">{{ __('dashboard.phone_model_name') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.phone_model_brand') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.phone_model_year') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.phone_model_parts') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.status') }}</th>
                <th class="px-4 py-3 font-medium text-end">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($models as $m)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if ($m->image_url)
                                <img src="{{ $m->image_url }}" class="w-10 h-10 object-cover rounded border border-gray-200" alt="">
                            @else
                                <div class="w-10 h-10 rounded bg-brand-soft flex items-center justify-center text-brand">📱</div>
                            @endif
                            <div>
                                <div class="font-medium">{{ $m->name_ar }}</div>
                                <div class="text-xs text-gray-500" dir="ltr">{{ $m->name_en }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">{{ $m->category?->name_ar }}</td>
                    <td class="px-4 py-3 font-mono" dir="ltr">{{ $m->release_year ?: '—' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('dashboard.products.index', ['phone_model' => $m->id]) }}" class="text-brand underline">
                            {{ $m->products_count }}
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="badge {{ $m->is_active ? 'badge-on' : 'badge-off' }}">
                            {{ $m->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-end">
                        <div class="inline-flex gap-2">
                            <a href="{{ route('dashboard.phone_models.edit', $m->id) }}"
                               class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">{{ __('dashboard.edit') }}</a>
                            <form method="POST" action="{{ route('dashboard.phone_models.destroy', $m->id) }}"
                                  onsubmit="return confirm(@js(__('dashboard.confirm_delete')))">
                                @csrf @method('DELETE')
                                <button class="text-sm px-3 py-1 border border-red-300 text-red-600 rounded hover:bg-red-50">{{ __('dashboard.delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">{{ __('dashboard.phone_models_empty') }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $models->links() }}</div>

@endsection
