@extends('admin.layouts.app')

@section('title', __('dashboard.order_details_title'))
@section('page_title', __('dashboard.order_details_title') . ' — ' . $order->order_number)

@section('content')

<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h2 class="text-2xl font-bold">
            {{ __('dashboard.order_details_title') }}
            <span class="font-mono text-base text-gray-600" dir="ltr">#{{ $order->order_number }}</span>
        </h2>
        <p class="text-xs text-gray-500 mt-1" dir="ltr">
            {{ $order->created_at->format('Y-m-d H:i') }}
        </p>
    </div>
    <a href="{{ route('dashboard.orders.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">
        ← {{ __('dashboard.back') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Left column: items + totals ───────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Items list --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 font-semibold">
                {{ __('dashboard.order_items') }} ({{ $order->items->count() }})
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-600">
                    <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                        <th class="px-4 py-2 font-medium">{{ __('dashboard.product_name_ar') }}</th>
                        <th class="px-4 py-2 font-medium">{{ __('dashboard.order_size') }}</th>
                        <th class="px-4 py-2 font-medium">{{ __('dashboard.order_color') }}</th>
                        <th class="px-4 py-2 font-medium">{{ __('dashboard.order_qty') }}</th>
                        <th class="px-4 py-2 font-medium">{{ __('dashboard.order_price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($item->image_url)
                                        <img src="{{ $item->image_url }}" alt=""
                                             class="w-12 h-12 object-cover rounded border border-gray-200">
                                    @endif
                                    <div class="font-medium">{{ $item->name_ar }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $item->selected_size ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $item->selected_color ?: '—' }}</td>
                            <td class="px-4 py-3 font-mono" dir="ltr">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 font-mono" dir="ltr">{{ number_format($item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('dashboard.order_subtotal') }}</span>
                    <span class="font-mono" dir="ltr">{{ number_format($order->total_amount - $order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">{{ __('dashboard.order_shipping') }}</span>
                    <span class="font-mono" dir="ltr">{{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-200 font-bold text-base">
                    <span>{{ __('dashboard.order_grand_total') }}</span>
                    <span class="font-mono" dir="ltr">{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Notes (if any) --}}
        @if ($order->customer_notes)
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-2">
                    {{ __('dashboard.order_notes') }}
                </div>
                <p class="text-sm">{{ $order->customer_notes }}</p>
            </div>
        @endif
    </div>

    {{-- ── Right column: customer + address + status ──────────────────── --}}
    <div class="space-y-5">

        {{-- Customer --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">
                {{ __('dashboard.order_customer_info') }}
            </div>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-600">{{ __('dashboard.order_customer') }}</dt>
                    <dd class="font-medium">{{ $order->customer_name }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-gray-600">{{ __('dashboard.order_phone') }}</dt>
                    <dd dir="ltr">{{ $order->customer_phone }}</dd>
                </div>
            </dl>
        </div>

        {{-- Address --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">
                {{ __('dashboard.order_delivery_address') }}
            </div>
            <div class="text-sm space-y-1">
                <div class="font-medium">{{ $order->customer_city }}</div>
                <div class="text-gray-700">{{ $order->customer_address }}</div>
            </div>
        </div>

        {{-- Payment + status --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-1">
                    {{ __('dashboard.order_payment_method') }}
                </div>
                <div class="text-sm font-medium">
                    {{ $order->payment_method === 'cod' ? __('dashboard.payment_cod') : $order->payment_method }}
                </div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-1">
                    {{ __('dashboard.order_status') }}
                </div>
                <span class="inline-block px-3 py-1 text-sm border border-black rounded">
                    {{ __('dashboard.order_status_' . $order->status->value) }}
                </span>
            </div>
        </div>

        {{-- Status update form --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">
                {{ __('dashboard.order_update_status') }}
            </div>

            @if (empty($allowedTransitions))
                <p class="text-sm text-gray-500">{{ __('dashboard.no_change_possible') }}</p>
            @else
                <form method="POST" action="{{ route('dashboard.orders.status', $order->id) }}" class="space-y-3">
                    @csrf @method('PUT')
                    <select name="status" required class="w-full px-3 py-2 rounded-md text-sm">
                        @foreach ($allowedTransitions as $s)
                            <option value="{{ $s->value }}">
                                → {{ __('dashboard.order_status_' . $s->value) }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary w-full px-4 py-2 rounded-md text-sm font-semibold">
                        {{ __('dashboard.save') }}
                    </button>
                </form>
            @endif
        </div>

        {{-- Status history --}}
        @if ($order->statusLogs->isNotEmpty())
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">
                    {{ __('dashboard.order_status_history') }}
                </div>
                <ul class="text-sm space-y-2">
                    @foreach ($order->statusLogs as $log)
                        <li class="flex items-start justify-between gap-3 pb-2 border-b border-gray-100 last:border-0">
                            <div>
                                @if ($log->from_status)
                                    <span class="text-gray-500">{{ __('dashboard.order_status_' . $log->from_status) }}</span>
                                    <span class="text-gray-400 mx-1">→</span>
                                @endif
                                <span class="font-medium">{{ __('dashboard.order_status_' . $log->to_status) }}</span>
                            </div>
                            <span class="text-xs text-gray-500" dir="ltr">
                                {{ $log->created_at->format('Y-m-d H:i') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

@endsection