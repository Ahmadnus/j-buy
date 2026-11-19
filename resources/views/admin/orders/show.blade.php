@extends('admin.layouts.app')

@section('title', __('dashboard.order_details_title'))
@section('page_title', __('dashboard.order_details_title') . ' — ' . $order->order_number)

@section('content')

@php
    // Safely get the status value as a plain string — works whether the
    // model returns an enum object or a raw string.
    $statusValue = $order->status instanceof \App\Enums\OrderStatus
        ? $order->status->value
        : (string) $order->status;
@endphp

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

        {{-- Notes --}}
        @if ($order->customer_notes)
            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-2">{{ __('dashboard.order_notes') }}</div>
                <p class="text-sm">{{ $order->customer_notes }}</p>
            </div>
        @endif
    </div>

    {{-- ── Right column ────────────────────────────────────────────────── --}}
    <div class="space-y-5">

        {{-- Customer --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">{{ __('dashboard.order_customer_info') }}</div>
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

        {{-- Fulfillment: delivery or branch pickup --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs uppercase tracking-wide text-gray-600">{{ __('dashboard.order_fulfillment') }}</div>
                @if ($order->is_pickup)
                    <span class="badge badge-pickup">🏬 {{ __('dashboard.fulfillment_pickup') }}</span>
                @else
                    <span class="badge badge-delivery">🚚 {{ __('dashboard.fulfillment_delivery') }}</span>
                @endif
            </div>

            @if ($order->is_pickup)
                <div class="text-sm space-y-1">
                    <div class="font-medium">{{ $order->branch?->name_ar ?? $order->branch_name }}</div>
                    @if ($order->branch)
                        <div class="text-gray-700">{{ $order->branch->address_ar }}</div>
                        @if ($order->branch->phone)<div dir="ltr" class="text-gray-700">☎ {{ $order->branch->phone }}</div>@endif
                    @endif
                </div>
            @else
                <div class="text-sm space-y-1">
                    <div class="font-medium">{{ $order->customer_city }}</div>
                    <div class="text-gray-700">{{ $order->customer_address }}</div>
                </div>
            @endif
        </div>

        {{-- Buyer location --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">{{ __('dashboard.order_buyer_location') }}</div>
            @if ($order->maps_url)
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <div id="order-map" class="w-full h-56 rounded-md border border-gray-200 mb-3"></div>
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <span class="text-xs font-mono text-gray-500" dir="ltr">{{ number_format($order->latitude, 6) }}, {{ number_format($order->longitude, 6) }}</span>
                    <a href="{{ $order->maps_url }}" target="_blank" rel="noopener" class="btn-primary px-3 py-1.5 rounded text-xs font-semibold">
                        🗺 {{ __('dashboard.open_in_google_maps') }}
                    </a>
                </div>
                <script>
                    (function () {
                        const buyer = [{{ $order->latitude }}, {{ $order->longitude }}];
                        const map = L.map('order-map').setView(buyer, 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 19, attribution: '&copy; OpenStreetMap'}).addTo(map);
                        L.marker(buyer).addTo(map).bindPopup(@js($order->customer_name)).openPopup();
                        @if ($order->is_pickup && $order->branch && $order->branch->latitude !== null)
                            const branch = [{{ $order->branch->latitude }}, {{ $order->branch->longitude }}];
                            L.circleMarker(branch, {radius: 9, color: '#8B1538', fillColor: '#8B1538', fillOpacity: .85})
                             .addTo(map).bindPopup(@js($order->branch->name_ar));
                            map.fitBounds([buyer, branch], {padding: [30, 30]});
                        @endif
                    })();
                </script>
            @else
                <p class="text-sm text-gray-500">{{ __('dashboard.order_no_location') }}</p>
            @endif
        </div>

        {{-- Payment + status --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-1">{{ __('dashboard.order_payment_method') }}</div>
                <div class="text-sm font-medium">
                    {{ $order->payment_method === 'cod' ? __('dashboard.payment_cod') : $order->payment_method }}
                </div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-1">{{ __('dashboard.order_status') }}</div>
                {{-- $statusValue is a plain string computed in @php above --}}
                <span class="inline-block px-3 py-1 text-sm border border-black rounded">
                    {{ __('dashboard.order_status_' . $statusValue) }}
                </span>
            </div>
        </div>

        {{-- Status update form --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">{{ __('dashboard.order_update_status') }}</div>

            @if (empty($allowedTransitions))
                <p class="text-sm text-gray-500">{{ __('dashboard.no_change_possible') }}</p>
            @else
                <form method="POST" action="{{ route('dashboard.orders.status', $order->id) }}" class="space-y-3">
                    @csrf @method('PUT')
                    <select name="status" required class="w-full px-3 py-2 rounded-md text-sm">
                        @foreach ($allowedTransitions as $s)
                            @php
                                $sv = $s instanceof \App\Enums\OrderStatus ? $s->value : (string) $s;
                            @endphp
                            <option value="{{ $sv }}">
                                → {{ __('dashboard.order_status_' . $sv) }}
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
                <div class="text-xs uppercase tracking-wide text-gray-600 mb-3">{{ __('dashboard.order_status_history') }}</div>
                <ul class="text-sm space-y-2">
                    @foreach ($order->statusLogs as $log)
                        @php
                            $fromVal = $log->from_status_text
                                ?? ($log->from_status instanceof \App\Enums\OrderStatus
                                    ? $log->from_status->value
                                    : (string) ($log->from_status ?? ''));

                            $toVal = $log->to_status_text
                                ?? ($log->to_status instanceof \App\Enums\OrderStatus
                                    ? $log->to_status->value
                                    : (string) ($log->to_status ?? ''));
                        @endphp
                        <li class="flex items-start justify-between gap-3 pb-2 border-b border-gray-100 last:border-0">
                            <div>
                                @if ($fromVal)
                                    <span class="text-gray-500">{{ __('dashboard.order_status_' . $fromVal) }}</span>
                                    <span class="text-gray-400 mx-1">→</span>
                                @endif
                                <span class="font-medium">{{ __('dashboard.order_status_' . $toVal) }}</span>
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