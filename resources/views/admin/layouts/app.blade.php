<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('dashboard.dashboard_name')) — {{ __('dashboard.app_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}?v=2">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* White & maroon (خمري) brand palette */
        :root {
            --brand:       #8B1538;
            --brand-dark:  #5E0B24;
            --brand-light: #B23A5E;
            --brand-soft:  #FBEEF2;
            --ink:         #1F1A1C;
        }
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Inter'" }}, sans-serif;
            background: #FAF8F9;
            color: var(--ink);
        }
        .btn-primary {
            background: var(--brand); color: #FFFFFF;
            transition: background .15s ease;
        }
        .btn-primary:hover { background: var(--brand-dark); }
        .btn-secondary {
            background: #FFFFFF; color: var(--brand); border: 1px solid var(--brand);
        }
        .btn-secondary:hover { background: var(--brand-soft); }
        .btn-danger {
            background: #FFFFFF; color: var(--brand); border: 1px solid var(--brand);
        }
        .btn-danger:hover { background: var(--brand); color: #FFFFFF; }
        input[type=text], input[type=email], input[type=password],
        input[type=number], input[type=file], input[type=date], select, textarea {
            border: 1px solid #D1D5DB;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(139, 21, 56, .12);
        }
        .table-cell { padding: 0.75rem 1rem; border-bottom: 1px solid #E5E7EB; }
        .sidebar { background: linear-gradient(180deg, var(--brand) 0%, var(--brand-dark) 100%); }
        .nav-link.active { background: #FFFFFF; color: var(--brand); }
        .text-brand { color: var(--brand); }
        .bg-brand { background: var(--brand); }
        .bg-brand-soft { background: var(--brand-soft); }
        .accent-brand, .accent-black { accent-color: var(--brand); }
        .badge { display: inline-block; padding: 2px 10px; font-size: 12px; border-radius: 999px; }
        .badge-on  { background: var(--brand); color: #fff; }
        .badge-off { border: 1px solid #D1D5DB; color: #6B7280; }
        .badge-pickup   { background: #FFF4E5; color: #B45309; border: 1px solid #FCD9A8; }
        .badge-delivery { background: var(--brand-soft); color: var(--brand); border: 1px solid #F1C6D3; }
        .bg-black { background: var(--brand) !important; }
        .border-black { border-color: var(--brand) !important; }
        .hover\:bg-black:hover { background: var(--brand) !important; }
    </style>
</head>
<body class="min-h-screen">

<div class="flex min-h-screen">

    {{-- ── Sidebar ────────────────────────────────────────────────────── --}}
    <aside class="hidden md:flex md:w-64 flex-col sidebar text-white">
        <div class="px-5 py-5 border-b border-white/10 flex items-center gap-3">
            <img src="{{ asset('images/logo-icon.png') }}?v=2" alt="" class="w-11 h-11 rounded-xl ring-2 ring-white/30">
            <div>
                <h1 class="text-lg font-bold leading-tight">{{ __('dashboard.app_name') }}</h1>
                <p class="text-xs opacity-75">{{ __('dashboard.dashboard_name') }}</p>
            </div>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1">
            @php
                $links = [
                    ['route' => 'dashboard.home',              'label' => 'nav_dashboard',  'icon' => '⌂'],
                    ['route' => 'dashboard.products.index',    'label' => 'nav_products',   'icon' => '▢'],
                    ['route' => 'dashboard.categories.index',  'label' => 'nav_categories', 'icon' => '◫'],
                    ['route' => 'dashboard.phone_models.index','label' => 'nav_phone_models','icon' => '📱'],
                    ['route' => 'dashboard.branches.index',    'label' => 'nav_branches',   'icon' => '📍'],
                    ['route' => 'dashboard.banners.index',     'label' => 'nav_banners',    'icon' => '▭'],
                    ['route' => 'dashboard.orders.index',      'label' => 'nav_orders',     'icon' => '☑'],
                    ['route' => 'dashboard.complaints.index', 'label' => 'nav_complaints', 'icon' => '✉',
                     'badge' => \App\Models\Complaint::where('admin_unread', true)->count()],
                    ['route' => 'dashboard.shipping.index',    'label' => 'nav_shipping',   'icon' => '⚓'],
                    ['route' => 'dashboard.users.index',       'label' => 'nav_users',      'icon' => '☻'],
                ];
            @endphp
            @foreach ($links as $link)
                @php $isActive = request()->routeIs($link['route']) || request()->routeIs(str_replace('.index','.*',$link['route'])); @endphp
                <a href="{{ route($link['route']) }}"
                   class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-md text-sm font-medium hover:bg-white/10 transition {{ $isActive ? 'active' : '' }}">
                    <span class="text-base opacity-80">{{ $link['icon'] }}</span>
                    <span>{{ __('dashboard.' . $link['label']) }}</span>
                    @if (! empty($link['badge']))
                        <span class="ms-auto min-w-[1.25rem] px-1.5 py-0.5 rounded-full bg-white text-rose-800 text-xs font-bold text-center">{{ $link['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
        <div class="px-4 py-4 border-t border-white/10">
            <form action="{{ route('dashboard.logout') }}" method="POST">
                @csrf
                <button class="w-full text-left text-sm font-medium opacity-80 hover:opacity-100">
                    ⎋ {{ __('dashboard.logout') }}
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main column ───────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <div class="md:hidden">
                <h1 class="text-base font-semibold">{{ __('dashboard.dashboard_name') }}</h1>
            </div>
            <div class="text-sm font-medium hidden md:block">@yield('page_title')</div>

            <div class="flex items-center gap-3">
                @php $other = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
                <a href="{{ route('dashboard.locale', $other) }}"
                   class="text-sm px-3 py-1.5 border border-black rounded hover:bg-black hover:text-white transition">
                    {{ $other === 'ar' ? 'العربية' : 'English' }}
                </a>
                @auth
                    <span class="text-sm text-gray-600 hidden sm:inline">{{ Auth::user()->name_ar ?? Auth::user()->email }}</span>
                @endauth
            </div>
        </header>

        {{-- Mobile nav --}}
        <nav class="md:hidden bg-black text-white px-4 py-2 flex items-center gap-4 overflow-x-auto">
            @foreach ($links as $link)
                @php $isActive = request()->routeIs($link['route']) || request()->routeIs(str_replace('.index','.*',$link['route'])); @endphp
                <a href="{{ route($link['route']) }}"
                   class="text-xs whitespace-nowrap px-3 py-1.5 rounded-md {{ $isActive ? 'bg-white text-black' : '' }}">
                    {{ __('dashboard.' . $link['label']) }}
                    @if (! empty($link['badge']))
                        <span class="ms-1 px-1.5 rounded-full bg-rose-600 text-white font-bold">{{ $link['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Flash messages --}}
        @include('admin.components.flash')

        {{-- Page content --}}
        <main class="flex-1 px-4 md:px-8 py-6">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>