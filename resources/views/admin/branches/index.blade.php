@extends('admin.layouts.app')

@section('title', __('dashboard.nav_branches'))
@section('page_title', __('dashboard.branches_title'))

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">{{ __('dashboard.branches_title') }}</h2>
    <a href="{{ route('dashboard.branches.create') }}" class="btn-primary px-4 py-2 rounded text-sm font-semibold">
        + {{ __('dashboard.branch_new') }}
    </a>
</div>

<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse ($branches as $branch)
        <div class="bg-white border border-gray-200 rounded-lg p-5 flex flex-col gap-3">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <div class="font-bold text-base">{{ $branch->name_ar }}</div>
                    @if ($branch->city_ar)<div class="text-xs text-gray-500">{{ $branch->city_ar }}</div>@endif
                </div>
                <span class="badge {{ $branch->is_active ? 'badge-on' : 'badge-off' }}">
                    {{ $branch->is_active ? __('dashboard.active') : __('dashboard.inactive') }}
                </span>
            </div>
            <div class="text-sm text-gray-700">📍 {{ $branch->address_ar }}</div>
            @if ($branch->phone)<div class="text-sm" dir="ltr">☎ {{ $branch->phone }}</div>@endif
            @if ($branch->working_hours)<div class="text-sm text-gray-600">🕘 {{ $branch->working_hours }}</div>@endif
            <div class="text-xs text-gray-500">{{ __('dashboard.branch_orders_count') }}: {{ $branch->orders_count }}</div>
            <div class="flex gap-2 mt-auto pt-3 border-t border-gray-100 flex-wrap">
                @if ($branch->maps_url)
                    <a href="{{ $branch->maps_url }}" target="_blank" rel="noopener" class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">🗺 {{ __('dashboard.open_map') }}</a>
                @endif
                <a href="{{ route('dashboard.branches.edit', $branch) }}" class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">{{ __('dashboard.edit') }}</a>
                <form method="POST" action="{{ route('dashboard.branches.destroy', $branch) }}" onsubmit="return confirm(@js(__('dashboard.confirm_delete')))">
                    @csrf @method('DELETE')
                    <button class="text-sm px-3 py-1 border border-red-300 text-red-600 rounded hover:bg-red-50">{{ __('dashboard.delete') }}</button>
                </form>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white border border-gray-200 rounded-lg px-4 py-10 text-center text-gray-500">
            {{ __('dashboard.branches_empty') }}
        </div>
    @endforelse
</div>

@endsection
