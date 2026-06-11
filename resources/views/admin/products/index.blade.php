@extends('admin.layouts.app')

@section('title', __('dashboard.products_title'))
@section('page_title', __('dashboard.products_title'))

@section('content')

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
    <h2 class="text-2xl font-bold">{{ __('dashboard.products_title') }}</h2>
    <a href="{{ route('dashboard.products.create') }}"
       class="btn-primary inline-block px-4 py-2 rounded-md text-sm font-semibold text-center">
        + {{ __('dashboard.product_new') }}
    </a>
</div>

{{-- Search bar --}}
<form method="GET" class="mb-5">
    <input type="text" name="q" value="{{ $q }}"
           placeholder="{{ __('dashboard.search') }}…"
           class="w-full md:max-w-sm px-3 py-2 rounded-md text-sm">
</form>

<div class="border border-gray-200 rounded-lg overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="table-cell font-semibold">#</th>
                <th class="table-cell font-semibold">{{ __('dashboard.product_name_ar') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.product_code') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.product_category') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.product_price') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td class="table-cell">{{ $product->id }}</td>
                    <td class="table-cell font-medium">{{ $product->name_ar }}</td>
                    <td class="table-cell text-gray-600">{{ $product->product_code }}</td>
                    <td class="table-cell text-gray-600">{{ $product->category->name_ar ?? '—' }}</td>
                    <td class="table-cell">{{ number_format($product->price, 2) }} {{ $product->currency }}</td>
                    <td class="table-cell">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard.products.edit', $product->id) }}"
                               class="btn-secondary text-xs px-3 py-1 rounded">
                                {{ __('dashboard.edit') }}
                            </a>
                            <form method="POST" action="{{ route('dashboard.products.destroy', $product->id) }}"
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
    {{ $products->links() }}
</div>

@endsection