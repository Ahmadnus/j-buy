<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('dashboard.login_title') }} — {{ __('dashboard.dashboard_name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}?v=2">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Inter'" }}, sans-serif;
            background: linear-gradient(160deg, #FFFFFF 55%, #FBEEF2 100%); color: #1F1A1C;
        }
        input { border: 1px solid #D1D5DB; transition: border-color .15s ease, box-shadow .15s ease; }
        input:focus { outline: none; border-color: #8B1538; box-shadow: 0 0 0 3px rgba(139,21,56,.12); }
        .btn-brand { background: #8B1538; color: #fff; }
        .btn-brand:hover { background: #5E0B24; }
        .lang-btn { border: 1px solid #8B1538; color: #8B1538; }
        .lang-btn:hover { background: #8B1538; color: #fff; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Language switcher in the corner --}}
    <div class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }}">
        @php $other = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
        <a href="{{ route('dashboard.locale', $other) }}"
           class="lang-btn text-sm px-3 py-1.5 rounded transition">
            {{ $other === 'ar' ? 'العربية' : 'English' }}
        </a>
    </div>

    <div class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            {{-- Logo / brand --}}
            <div class="text-center mb-10">
                <img src="{{ asset('images/logo.png') }}?v=2" alt="{{ __('dashboard.app_name') }}"
                     class="mx-auto h-32 w-auto mb-6">
                <h1 class="text-2xl font-bold">{{ __('dashboard.login_title') }}</h1>
                <p class="text-sm text-gray-600 mt-2">{{ __('dashboard.login_subtitle') }}</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="border border-red-300 bg-red-50 text-red-700 rounded-md text-sm px-4 py-3 mb-5">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('dashboard.login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="email"
                           class="w-full px-3 py-2.5 rounded-md text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ __('dashboard.password') }}</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full px-3 py-2.5 rounded-md text-sm">
                </div>

                <button type="submit"
                        class="btn-brand w-full py-2.5 rounded-md text-sm font-semibold transition mt-2">
                    {{ __('dashboard.login') }}
                </button>
            </form>

        </div>
    </div>

</body>
</html>