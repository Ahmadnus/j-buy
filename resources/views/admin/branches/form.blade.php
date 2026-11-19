@extends('admin.layouts.app')

@php $isEdit = $branch->exists; @endphp

@section('title', $isEdit ? __('dashboard.branch_edit') : __('dashboard.branch_new'))
@section('page_title', $isEdit ? __('dashboard.branch_edit') : __('dashboard.branch_new'))

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">{{ $isEdit ? __('dashboard.branch_edit') : __('dashboard.branch_new') }}</h2>
        <a href="{{ route('dashboard.branches.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">← {{ __('dashboard.back') }}</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 border border-red-300 bg-red-50 text-red-700 rounded text-sm">
            <ul class="list-disc list-inside space-y-1">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('dashboard.branches.update', $branch) : route('dashboard.branches.store') }}"
          class="space-y-5 bg-white border border-gray-200 rounded-lg p-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.name_ar') }} <span class="text-red-600">*</span></label>
                <input type="text" name="name_ar" required value="{{ old('name_ar', $branch->name_ar) }}" class="w-full px-3 py-2 rounded-md text-sm" placeholder="فرع عمّان - الجاردنز">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.name_en') }}</label>
                <input type="text" name="name_en" value="{{ old('name_en', $branch->name_en) }}" class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.branch_city') }}</label>
                <input type="text" name="city_ar" value="{{ old('city_ar', $branch->city_ar) }}" class="w-full px-3 py-2 rounded-md text-sm" placeholder="عمان">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.branch_phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" class="w-full px-3 py-2 rounded-md text-sm" dir="ltr" placeholder="0790000000">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.branch_address') }} <span class="text-red-600">*</span></label>
            <input type="text" name="address_ar" required value="{{ old('address_ar', $branch->address_ar) }}" class="w-full px-3 py-2 rounded-md text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.branch_hours') }}</label>
            <input type="text" name="working_hours" value="{{ old('working_hours', $branch->working_hours) }}" class="w-full px-3 py-2 rounded-md text-sm" placeholder="يومياً 10:00 ص - 10:00 م">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.branch_location') }}</label>
            <p class="text-xs text-gray-500 mb-2">{{ __('dashboard.branch_location_hint') }}</p>
            <div id="branch-map" class="w-full h-72 rounded-md border border-gray-200 mb-3"></div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="latitude" id="lat" value="{{ old('latitude', $branch->latitude) }}" placeholder="Latitude" class="px-3 py-2 rounded-md text-sm font-mono" dir="ltr">
                <input type="text" name="longitude" id="lng" value="{{ old('longitude', $branch->longitude) }}" placeholder="Longitude" class="px-3 py-2 rounded-md text-sm font-mono" dir="ltr">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5 items-end">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.category_sort_order') }}</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $branch->sort_order ?? 0) }}" class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
            <label class="inline-flex items-center gap-2 cursor-pointer pb-2">
                <input type="checkbox" name="is_active" value="1" class="accent-brand" {{ old('is_active', $isEdit ? $branch->is_active : true) ? 'checked' : '' }}>
                <span class="text-sm font-medium">{{ __('dashboard.active') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="btn-primary px-5 py-2.5 rounded-md text-sm font-semibold">{{ $isEdit ? __('dashboard.update') : __('dashboard.create') }}</button>
            <a href="{{ route('dashboard.branches.index') }}" class="btn-secondary px-5 py-2.5 rounded-md text-sm">{{ __('dashboard.cancel') }}</a>
        </div>
    </form>
</div>

<script>
(function () {
    const latEl = document.getElementById('lat'), lngEl = document.getElementById('lng');
    const lat = parseFloat(latEl.value), lng = parseFloat(lngEl.value);
    const has = !isNaN(lat) && !isNaN(lng);
    // Default view: Amman
    const map = L.map('branch-map').setView(has ? [lat, lng] : [31.9539, 35.9106], has ? 16 : 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, attribution: '&copy; OpenStreetMap'
    }).addTo(map);
    let marker = null;
    function place(ll) {
        latEl.value = ll.lat.toFixed(7);
        lngEl.value = ll.lng.toFixed(7);
        if (!marker) {
            marker = L.marker(ll, {draggable: true}).addTo(map);
            marker.on('dragend', e => place(e.target.getLatLng()));
        } else {
            marker.setLatLng(ll);
        }
    }
    if (has) place(L.latLng(lat, lng));
    map.on('click', e => place(e.latlng));
})();
</script>
@endsection
