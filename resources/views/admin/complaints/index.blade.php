@extends('admin.layouts.app')
@section('title', __('dashboard.complaints_title'))
@section('page_title', __('dashboard.complaints_title'))

@section('content')
@include('admin.components.flash')

<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <h2 class="text-2xl font-bold">{{ __('dashboard.complaints_title') }}</h2>
</div>

{{-- Search + filter --}}
<form method="GET" action="{{ route('dashboard.complaints.index') }}"
      class="flex flex-wrap gap-3 mb-5">
    <input type="text" name="q" value="{{ $q }}"
           placeholder="{{ __('dashboard.complaints_search') }}"
           class="px-3 py-2 border border-gray-300 rounded text-sm w-64">
    <select name="status" class="px-3 py-2 border border-gray-300 rounded text-sm">
        <option value="">{{ __('dashboard.all_statuses') }}</option>
        @foreach (['new','in_review','resolved','closed'] as $s)
            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>
                {{ __("dashboard.complaint_status_$s") }}
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary px-4 py-2 rounded text-sm">
        {{ __('dashboard.search') }}
    </button>
    @if($q || $status)
        <a href="{{ route('dashboard.complaints.index') }}"
           class="btn-secondary px-4 py-2 rounded text-sm">✕</a>
    @endif
</form>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-600">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="px-4 py-3 font-medium">#</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.complaint_user') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.complaint_subject') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.complaint_status') }}</th>
                <th class="px-4 py-3 font-medium">{{ __('dashboard.complaint_date') }}</th>
                <th class="px-4 py-3 font-medium text-end">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($complaints as $c)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-3 text-gray-500 font-mono" dir="ltr">{{ $c->id }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $c->user->name_ar }}</div>
                        <div class="text-xs text-gray-500" dir="ltr">{{ $c->user->phone }}</div>
                    </td>
                    <td class="px-4 py-3">{{ Str::limit($c->subject, 50) }}</td>
                    <td class="px-4 py-3">
                        @php $color = match($c->status) {
                            'new'       => 'bg-yellow-100 text-yellow-800',
                            'in_review' => 'bg-blue-100 text-blue-800',
                            'resolved'  => 'bg-green-100 text-green-800',
                            'closed'    => 'bg-gray-100 text-gray-600',
                            default     => ''
                        }; @endphp
                        <span class="inline-block px-2 py-0.5 text-xs rounded {{ $color }}">
                            {{ __("dashboard.complaint_status_$c->status") }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs" dir="ltr">
                        {{ $c->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-4 py-3 text-end">
                        <a href="{{ route('dashboard.complaints.show', $c) }}"
                           class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
                            {{ __('dashboard.view') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                        {{ __('dashboard.complaints_empty') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if ($complaints->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $complaints->links() }}
        </div>
    @endif
</div>
@endsection