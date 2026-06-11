@extends('admin.layouts.app')

@section('title', __('dashboard.banners_title'))
@section('page_title', __('dashboard.banners_title'))

@section('content')

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
    <h2 class="text-2xl font-bold">{{ __('dashboard.banners_title') }}</h2>
    <a href="{{ route('dashboard.banners.create') }}"
       class="btn-primary inline-block px-4 py-2 rounded-md text-sm font-semibold text-center">
        + {{ __('dashboard.banner_new') }}
    </a>
</div>

<div class="border border-gray-200 rounded-lg overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="table-cell font-semibold">#</th>
                <th class="table-cell font-semibold">{{ __('dashboard.image') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.banner_title_ar') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.banner_cta_ar') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.banner_active') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($banners as $banner)
                <tr>
                    <td class="table-cell">{{ $banner->id }}</td>
                    <td class="table-cell">
                        @if ($banner->image_url)
                            <img src="{{ $banner->image_url }}" class="w-16 h-10 object-cover rounded border border-gray-200">
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="table-cell font-medium">{{ $banner->title_ar }}</td>
                    <td class="table-cell text-gray-600">{{ $banner->cta_text_ar }}</td>
                    <td class="table-cell">
                        @if ($banner->is_active)
                            <span class="inline-block px-2 py-0.5 text-xs bg-black text-white rounded">{{ __('dashboard.active') }}</span>
                        @else
                            <span class="inline-block px-2 py-0.5 text-xs border border-gray-300 rounded">{{ __('dashboard.inactive') }}</span>
                        @endif
                    </td>
                    <td class="table-cell">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard.banners.edit', $banner->id) }}"
                               class="btn-secondary text-xs px-3 py-1 rounded">
                                {{ __('dashboard.edit') }}
                            </a>
                            <form method="POST" action="{{ route('dashboard.banners.destroy', $banner->id) }}"
                                  onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}');" class="inline">
                                @csrf @method('DELETE')
                                <button class="btn-danger text-xs px-3 py-1 rounded">
                                    {{ __('dashboard.delete') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="table-cell text-center text-gray-500 py-8">
                        {{ __('dashboard.no_records') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $banners->links() }}
</div>

@endsection