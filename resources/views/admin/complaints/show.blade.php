@extends('admin.layouts.app')
@section('title', __('dashboard.complaint_detail_title'))
@section('page_title', __('dashboard.complaint_detail_title'))

@section('content')
@include('admin.components.flash')

<div class="flex items-center justify-between mb-5 gap-3 flex-wrap">
    <div>
        <h2 class="text-xl font-bold">
            #{{ $complaint->id }} · {{ $complaint->subject }}
        </h2>
        <p class="text-xs text-gray-500 mt-1" dir="ltr">
            {{ $complaint->created_at->format('Y-m-d H:i') }}
        </p>
    </div>
    <a href="{{ route('dashboard.complaints.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">
        ← {{ __('dashboard.back') }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Conversation ──────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white border border-gray-200 rounded-lg p-5 space-y-4">
            @forelse ($complaint->messages as $m)
                @php $fromAdmin = $m->isFromAdmin(); @endphp
                <div class="flex {{ $fromAdmin ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-lg px-4 py-3 text-sm
                                {{ $fromAdmin ? 'bg-rose-50 border border-rose-200' : 'bg-gray-50 border border-gray-200' }}">
                        <div class="flex items-center gap-2 mb-1 text-xs text-gray-500">
                            <span class="font-semibold {{ $fromAdmin ? 'text-rose-800' : 'text-gray-700' }}">
                                @if ($fromAdmin)
                                    {{ __('dashboard.complaint_from_support') }}{{ $m->admin ? ' · ' . $m->admin->name_ar : '' }}
                                @else
                                    {{ $complaint->contactName() }}
                                @endif
                            </span>
                            <span dir="ltr">{{ $m->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <p class="leading-relaxed whitespace-pre-wrap">{{ $m->body }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $complaint->message }}</p>
            @endforelse
        </div>

        {{-- Reply box --}}
        @if ($complaint->isClosed())
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-600">
                {{ __('dashboard.complaint_closed_hint') }}
            </div>
        @else
            <form method="POST" action="{{ route('dashboard.complaints.reply', $complaint) }}"
                  class="bg-white border border-gray-200 rounded-lg p-5 space-y-3">
                @csrf
                <label class="block text-xs uppercase tracking-wide text-gray-500">
                    {{ __('dashboard.complaint_reply_label') }}
                </label>
                <textarea name="body" rows="4" required maxlength="5000"
                    placeholder="{{ __('dashboard.complaint_reply_placeholder') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded text-sm">{{ old('body') }}</textarea>
                @error('body')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500">{{ __('dashboard.complaint_reply_visible_hint') }}</p>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="btn-primary px-4 py-2 rounded text-sm font-semibold">
                        {{ __('dashboard.complaint_send_reply') }}
                    </button>
                    <button type="submit" name="status" value="resolved"
                            class="btn-secondary px-4 py-2 rounded text-sm">
                        {{ __('dashboard.complaint_send_and_resolve') }}
                    </button>
                </div>
            </form>
        @endif
    </div>

    {{-- ── Sidebar: customer + status ────────────────────────────────── --}}
    <div class="space-y-4">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="text-xs uppercase tracking-wide text-gray-500 mb-3">
                {{ __('dashboard.complaint_user') }}
            </div>
            <div class="font-medium flex items-center gap-2">
                {{ $complaint->contactName() }}
                @if ($complaint->isGuest())
                    <span class="inline-block px-2 py-0.5 text-xs rounded bg-amber-100 text-amber-800">
                        {{ __('dashboard.complaint_guest') }}
                    </span>
                @endif
            </div>
            @if ($phone = $complaint->contactPhone())
                <div class="text-sm text-gray-600 mt-1" dir="ltr">{{ $phone }}</div>
                <div class="flex gap-2 mt-3">
                    <a href="tel:{{ $phone }}" class="btn-secondary px-3 py-1.5 rounded text-xs">
                        {{ __('dashboard.complaint_call') }}
                    </a>
                    <a href="https://wa.me/{{ ltrim($phone, '+') }}" target="_blank" rel="noopener"
                       class="btn-secondary px-3 py-1.5 rounded text-xs">
                        WhatsApp
                    </a>
                </div>
            @endif
            @if ($complaint->isGuest())
                <p class="text-xs text-gray-500 mt-3">{{ __('dashboard.complaint_guest_hint') }}</p>
            @endif
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
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.complaint_notes_hint') }}</p>
            </div>

            <button type="submit" class="btn-primary w-full px-4 py-2 rounded text-sm font-semibold">
                {{ __('dashboard.save_changes') }}
            </button>
        </form>
    </div>
</div>
@endsection
