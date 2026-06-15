@extends('admin.layouts.app')
@section('title', __('dashboard.complaint_detail_title'))
@section('page_title', __('dashboard.complaint_detail_title'))

@section('content')
@include('admin.components.flash')

<div class="flex items-center justify-between mb-5">
    <h2 class="text-xl font-bold">
        {{ __('dashboard.complaint_detail_title') }} #{{ $complaint->id }}
    </h2>
    <a href="{{ route('dashboard.complaints.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">
        ← {{ __('dashboard.back') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Message panel --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                {{ __('dashboard.complaint_subject') }}
            </div>
            <p class="font-semibold text-base">{{ $complaint->subject }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-2">
                {{ __('dashboard.complaint_message') }}
            </div>
            <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $complaint->message }}</p>
        </div>
    </div>

    {{-- Sidebar: user + status update --}}
    <div class="space-y-4">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-3">
                {{ __('dashboard.complaint_user') }}
            </div>
            <div class="font-medium">{{ $complaint->user->name_ar }}</div>
            <div class="text-sm text-gray-600" dir="ltr">{{ $complaint->user->phone }}</div>
            <div class="text-xs text-gray-500 mt-2" dir="ltr">
                {{ $complaint->created_at->format('Y-m-d H:i') }}
            </div>
        </div>

        <form method="POST" action="{{ route('dashboard.complaints.update', $complaint) }}"
              class="bg-white border border-gray-200 rounded-lg p-5 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">
                    {{ __('dashboard.complaint_status') }}
                </label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                    @foreach (['new','in_review','resolved','closed'] as $s)
                        <option value="{{ $s }}" {{ $complaint->status === $s ? 'selected' : '' }}>
                            {{ __("dashboard.complaint_status_$s") }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs uppercase tracking-wide text-gray-500 mb-1">
                    {{ __('dashboard.complaint_admin_notes') }}
                </label>
                <textarea name="admin_notes" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm">{{ old('admin_notes', $complaint->admin_notes) }}</textarea>
            </div>

            <button type="submit" class="btn-primary w-full px-4 py-2 rounded text-sm font-semibold">
                {{ __('dashboard.save_changes') }}
            </button>
        </form>
    </div>
</div>
@endsection