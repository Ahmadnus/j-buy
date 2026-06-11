@extends('admin.layouts.app')

@section('title', __('dashboard.users_title'))
@section('page_title', __('dashboard.users_title'))

@section('content')

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
    <h2 class="text-2xl font-bold">{{ __('dashboard.users_title') }}</h2>
</div>

<form method="GET" class="mb-5">
    <input type="text" name="q" value="{{ $q }}"
           placeholder="{{ __('dashboard.search') }}…"
           class="w-full md:max-w-sm px-3 py-2 rounded-md text-sm">
</form>

<div class="border border-gray-200 rounded-lg overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}">
                <th class="table-cell font-semibold">#</th>
                <th class="table-cell font-semibold">{{ __('dashboard.user_name') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.user_username') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.user_phone') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.user_email') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.user_status') }}</th>
                <th class="table-cell font-semibold">{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td class="table-cell">{{ $user->id }}</td>
                    <td class="table-cell font-medium">{{ $user->name_ar }}</td>
                    <td class="table-cell text-gray-600" dir="ltr">{{ $user->username }}</td>
                    <td class="table-cell text-gray-600" dir="ltr">{{ $user->phone }}</td>
                    <td class="table-cell text-gray-600" dir="ltr">{{ $user->email ?? '—' }}</td>
                    <td class="table-cell">
                        @if ($user->is_active ?? true)
                            <span class="inline-block px-2 py-0.5 text-xs bg-black text-white rounded">
                                {{ __('dashboard.user_enabled') }}
                            </span>
                        @else
                            <span class="inline-block px-2 py-0.5 text-xs border border-gray-300 rounded">
                                {{ __('dashboard.user_disabled') }}
                            </span>
                        @endif
                    </td>
                    <td class="table-cell">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('dashboard.users.edit', $user->id) }}"
                               class="btn-secondary text-xs px-3 py-1 rounded">
                                {{ __('dashboard.edit') }}
                            </a>
                            <form method="POST" action="{{ route('dashboard.users.toggle', $user->id) }}" class="inline">
                                @csrf
                                <button class="btn-secondary text-xs px-3 py-1 rounded">
                                    {{ ($user->is_active ?? true) ? __('dashboard.user_disabled') : __('dashboard.user_enabled') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('dashboard.users.destroy', $user->id) }}"
                                  onsubmit="return confirm('{{ __('dashboard.confirm_delete') }}');" class="inline">
                                @csrf @method('DELETE')
                                <button class="btn-danger text-xs px-3 py-1 rounded">
                                    {{ __('dashboard.delete') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-cell text-center text-gray-500 py-8">
                        {{ __('dashboard.no_records') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>

@endsection