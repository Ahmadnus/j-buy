@extends('admin.layouts.app')

@section('title', __('dashboard.categories_title'))
@section('page_title', __('dashboard.categories_title'))

@section('content')

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
    <h2 class="text-2xl font-bold">{{ __('dashboard.categories_title') }}</h2>
    <a href="{{ route('dashboard.categories.create') }}"
       class="btn-primary inline-block px-4 py-2 rounded-md text-sm font-semibold text-center">
        + {{ __('dashboard.category_new') }}
    </a>
</div>

<div class="border border-gray-200 rounded-lg overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="table-cell font-semibold">#</th>
                <th class="table-cell font-semibold">{{ __('dashboard.image') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.category_name_ar') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.category_name_en') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.category_slug') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $cat)
                <tr>
                    <td class="table-cell">{{ $cat->id }}</td>
                    <td class="table-cell">
                        @if ($cat->image_url)
                            <img src="{{ $cat->image_url }}" class="w-10 h-10 object-cover rounded border border-gray-200">
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="table-cell font-medium">{{ $cat->name_ar }}</td>
                    <td class="table-cell" dir="ltr">{{ $cat->name_en }}</td>
                    <td class="table-cell text-gray-600" dir="ltr">{{ $cat->slug }}</td>
                    <td class="table-cell">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard.categories.edit', $cat->id) }}"
                               class="btn-secondary text-xs px-3 py-1 rounded">
                                {{ __('dashboard.edit') }}
                            </a>
                            <form method="POST" action="{{ route('dashboard.categories.destroy', $cat->id) }}"
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
    {{ $categories->links() }}
</div>

@endsection