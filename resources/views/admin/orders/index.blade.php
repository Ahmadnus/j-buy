@extends('admin.layouts.app')

@section('title', __('dashboard.orders_title'))
@section('page_title', __('dashboard.orders_title'))

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold">{{ __('dashboard.orders_title') }}</h2>
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────── --}}
<form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">

        <div class="lg:col-span-2">
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('dashboard.search') }}</label>
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="{{ __('dashboard.order_search_hint') }}"
                   class="w-full px-3 py-2 rounded-md text-sm">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('dashboard.order_filter_status') }}</label>
            <select name="status" class="w-full px-3 py-2 rounded-md text-sm">
                <option value="">{{ __('dashboard.order_all_statuses') }}</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s->value }}" {{ $status === $s->value ? 'selected' : '' }}>
                        {{ __('dashboard.order_status_' . $s->value) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('dashboard.order_filter_date_from') }}</label>
            <input type="date" name="date_from" value="{{ $date_from }}"
                   class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('dashboard.order_filter_date_to') }}</label>
            <input type="date" name="date_to" value="{{ $date_to }}"
                   class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
        </div>
    </div>

    <div class="flex items-center gap-2 mt-4">
        <button type="submit" class="btn-primary px-4 py-2 rounded-md text-sm font-semibold">
            {{ __('dashboard.search') }}
        </button>
        @if ($q || $status || $date_from || $date_to)
            <a href="{{ route('dashboard.orders.index') }}"
               class="btn-secondary px-4 py-2 rounded-md text-sm">
                {{ __('dashboard.order_clear_filters') }}
            </a>
        @endif
    </div>
</form>

{{-- ── Table ───────────────────────────────────────────────────────────── --}}
<div class="border border-gray-200 rounded-lg overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="table-cell font-semibold">{{ __('dashboard.order_number') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.order_customer') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.order_phone') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.order_date') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.order_total') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.order_payment_method') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.order_status') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td class="table-cell font-mono" dir="ltr">{{ $order->order_number }}</td>
                    <td class="table-cell font-medium">{{ $order->customer_name }}</td>
                    <td class="table-cell text-gray-600" dir="ltr">{{ $order->customer_phone }}</td>
                    <td class="table-cell text-gray-600" dir="ltr">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    <td class="table-cell">{{ number_format($order->total_amount, 2) }}</td>
                    <td class="table-cell text-gray-600">
                        {{ $order->payment_method === 'cod' ? __('dashboard.payment_cod') : $order->payment_method }}
                    </td>
                    <td class="table-cell">
                        <span class="inline-block px-2 py-0.5 text-xs border border-black rounded">
                            {{ __('dashboard.order_status_' . $order->status->value) }}
                        </span>
                    </td>
                    <td class="table-cell">
                        <a href="{{ route('dashboard.orders.show', $order->id) }}"
                           class="btn-secondary text-xs px-3 py-1 rounded">
                            {{ __('dashboard.view') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="table-cell text-center text-gray-500 py-8">
                        {{ __('dashboard.no_records') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>

@endsection