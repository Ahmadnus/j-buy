@extends('admin.layouts.app')

@section('title', __('dashboard.nav_shipping'))
@section('page_title', __('dashboard.shipping_title'))

@section('content')

@include('admin.components.flash')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('dashboard.shipping_title') }}</h2>
    <a href="{{ route('dashboard.shipping.create') }}"
       class="btn-primary px-4 py-2 rounded text-sm font-semibold">
        + {{ __('dashboard.shipping_new') }}
    </a>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-600">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="px-4 py-3 font-medium">#</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.shipping_city_ar') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.shipping_city_en') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.shipping_price') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.shipping_active') }}</th>
                <th class="px-4 py-3 font-medium text-end">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rates as $rate)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-3 font-mono text-gray-500" dir="ltr">{{ $rate->sort_order }}</td>
                    <td class="px-4 py-3 font-medium">{{ $rate->city_ar }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $rate->city_en ?: '—' }}</td>
                    <td class="px-4 py-3 font-mono" dir="ltr">{{ number_format((float) $rate->price, 3) }}</td>
                    <td class="px-4 py-3">
                        @if ($rate->is_active)
                            <span class="inline-block px-2 py-0.5 text-xs bg-black text-white rounded">
                                {{ __('dashboard.shipping_status_on') }}
                            </span>
                        @else
                            <span class="inline-block px-2 py-0.5 text-xs border border-gray-300 text-gray-600 rounded">
                                {{ __('dashboard.shipping_status_off') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-end">
                        <div class="inline-flex gap-2">
                            <a href="{{ route('dashboard.shipping.edit', $rate) }}"
                               class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                                {{ __('dashboard.edit') }}
                            </a>
                            <form method="POST" action="{{ route('dashboard.shipping.destroy', $rate) }}"
                                  onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-sm px-3 py-1 border border-red-300 text-red-600 rounded hover:bg-red-50">
                                    {{ __('dashboard.delete') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                        {{ __('dashboard.shipping_empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection