<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('dashboard.dashboard_name')) — {{ __('dashboard.app_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Inter'" }}, sans-serif;
            background: #FFFFFF;
            color: #000000;
        }
        /* Black & white only — no accents */
        .btn-primary {
            background: #000000; color: #FFFFFF;
            transition: opacity .15s ease;
        }
        .btn-primary:hover { opacity: .85; }
        .btn-secondary {
            background: #FFFFFF; color: #000000; border: 1px solid #000000;
        }
        .btn-secondary:hover { background: #F5F5F5; }
        .btn-danger {
            background: #FFFFFF; color: #000000; border: 1px solid #000000;
        }
        .btn-danger:hover { background: #000000; color: #FFFFFF; }
        input[type=text], input[type=email], input[type=password],
        input[type=number], input[type=file], select, textarea {
            border: 1px solid #D1D5DB;
            transition: border-color .15s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: #000000;
        }
        .table-cell { padding: 0.75rem 1rem; border-bottom: 1px solid #E5E7EB; }
        .nav-link.active { background: #000000; color: #FFFFFF; }
    </style>
</head>
<body class="min-h-screen">

<div class="flex min-h-screen">

    {{-- ── Sidebar ────────────────────────────────────────────────────── --}}
    <aside class="hidden md:flex md:w-64 flex-col bg-black text-white">
        <div class="px-6 py-6 border-b border-white/10">
            <h1 class="text-xl font-bold">{{ __('dashboard.dashboard_name') }}</h1>
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1">
            @php
                $links = [
                    ['route' => 'dashboard.home',              'label' => 'nav_dashboard',  'icon' => '⌂'],
                    ['route' => 'dashboard.products.index',    'label' => 'nav_products',   'icon' => '▢'],
                    ['route' => 'dashboard.categories.index',  'label' => 'nav_categories', 'icon' => '◫'],
                    ['route' => 'dashboard.banners.index',     'label' => 'nav_banners',    'icon' => '▭'],
                    ['route' => 'dashboard.orders.index',      'label' => 'nav_orders',     'icon' => '☑'],
                    ['route' => 'dashboard.users.index',       'label' => 'nav_users',      'icon' => '☻'],
                ];
            @endphp
            @foreach ($links as $link)
                @php $isActive = request()->routeIs($link['route']) || request()->routeIs(str_replace('.index','.*',$link['route'])); @endphp
                <a href="{{ route($link['route']) }}"
                   class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-md text-sm font-medium hover:bg-white/10 transition {{ $isActive ? 'active' : '' }}">
                    <span class="text-base opacity-80">{{ $link['icon'] }}</span>
                    <span>{{ __('dashboard.' . $link['label']) }}</span>
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