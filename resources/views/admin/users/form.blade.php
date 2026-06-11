@extends('admin.layouts.app')

@section('title', __('dashboard.user_edit'))
@section('page_title', __('dashboard.user_edit'))

@section('content')

<div class="max-w-2xl">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">{{ __('dashboard.user_edit') }}</h2>
        <a href="{{ route('dashboard.users.index') }}" class="btn-secondary px-3 py-1.5 rounded text-sm">
            ← {{ __('dashboard.back') }}
        </a>
    </div>

    <form method="POST"
          action="{{ route('dashboard.users.update', $user->id) }}"
          class="space-y-5 bg-white border border-gray-200 rounded-lg p-6">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1.5">
                {{ __('dashboard.user_name') }} <span class="text-red-600">*</span>
            </label>
            <input type="text" name="name_ar" required
                   value="{{ old('name_ar', $user->name_ar) }}"
                   class="w-full px-3 py-2 rounded-md text-sm">
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.user_username') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="username" required
                       value="{{ old('username', $user->username) }}"
                       pattern="[a-zA-Z0-9_.]+"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.user_phone') }} <span class="text-red-600">*</span>
                </label>
                <input type="text" name="phone" required
                       value="{{ old('phone', $user->phone) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr"
                       placeholder="+962791234567">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium mb-1.5">
                    {{ __('dashboard.user_email') }}
                    <span class="text-xs text-gray-500">({{ __('dashboard.optional') }})</span>
                </label>
                <input type="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-3 py-2 rounded-md text-sm" dir="ltr">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.user_region') }}</label>
                <input type="text" name="region"
                       value="{{ old('region', $user->region) }}"
                       class="w-full px-3 py-2 rounded-md text-sm">
            </div>
        </div>

        <div>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                       class="accent-black">
                <span class="text-sm font-medium">{{ __('dashboard.user_enabled') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
            <button type="submit" class="btn-primary px-5 py-2.5 rounded-md text-sm font-semibold">
                {{ __('dashboard.update') }}
            </button>
            <a href="{{ route('dashboard.users.index') }}"
               class="btn-secondary px-5 py-2.5 rounded-md text-sm">
                {{ __('dashboard.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection