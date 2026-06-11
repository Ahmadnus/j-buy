@extends('admin.layouts.app')

@section('title', __('dashboard.nav_dashboard'))
@section('page_title', __('dashboard.home_welcome'))

@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold mb-1">{{ __('dashboard.home_welcome') }}</h2>
    <p class="text-sm text-gray-600">{{ __('dashboard.home_overview') }}</p>
</div>

@php
    $cards = [
        ['key' => 'metric_products',   'value' => $metrics['products'],   'route' => 'dashboard.products.index'],
        ['key' => 'metric_categories', 'value' => $metrics['categories'], 'route' => 'dashboard.categories.index'],
        ['key' => 'metric_users',      'value' => $metrics['users'],      'route' => 'dashboard.users.index'],
        ['key' => 'metric_orders',     'value' => $metrics['orders'],     'route' => null],
        ['key' => 'metric_banners',    'value' => $metrics['banners'],    'route' => 'dashboard.banners.index'],
    ];
@endphp

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
    @foreach ($cards as $card)
        @php
            $contents = '
                <div class="text-3xl font-bold mb-1">' . $card['value'] . '</div>
                <div class="text-xs uppercase tracking-wide text-gray-600">' . __('dashboard.' . $card['key']) . '</div>
            ';
        @endphp
        @if ($card['route'])
            <a href="{{ route($card['route']) }}"
               class="block border border-black rounded-lg px-5 py-6 hover:bg-black hover:text-white transition group">
                <div class="text-3xl font-bold mb-1">{{ $card['value'] }}</div>
                <div class="text-xs uppercase tracking-wide text-gray-600 group-hover:text-white/80">
                    {{ __('dashboard.' . $card['key']) }}
                </div>
            </a>
        @else
            <div class="border border-black rounded-lg px-5 py-6">
                <div class="text-3xl font-bold mb-1">{{ $card['value'] }}</div>
                <div class="text-xs uppercase tracking-wide text-gray-600">
                    {{ __('dashboard.' . $card['key']) }}
                </div>
            </div>
        @endif
    @endforeach
</div>

@endsection