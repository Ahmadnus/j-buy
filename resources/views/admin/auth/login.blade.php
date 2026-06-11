<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('dashboard.login_title') }} — {{ __('dashboard.dashboard_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Inter'" }}, sans-serif;
            background: #FFFFFF; color: #000000;
        }
        input { border: 1px solid #D1D5DB; transition: border-color .15s ease; }
        input:focus { outline: none; border-color: #000000; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Language switcher in the corner --}}
    <div class="absolute top-4 {{ app()->getLocale() === 'ar' ? 'left-4' : 'right-4' }}">
        @php $other = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
        <a href="{{ route('dashboard.locale', $other) }}"
           class="text-sm px-3 py-1.5 border border-black rounded hover:bg-black hover:text-white transition">
            {{ $other === 'ar' ? 'العربية' : 'English' }}
        </a>
    </div>

    <div class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            {{-- Logo / brand --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-black text-white rounded-full text-xl font-bold mb-4">
                    J
                </div>
                <h1 class="text-2xl font-bold">{{ __('dashboard.login_title') }}</h1>
                <p class="text-sm text-gray-600 mt-2">{{ __('dashboard.login_subtitle') }}</p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="border border-black text-sm px-4 py-3 mb-5">
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
                        class="w-full bg-black text-white py-2.5 rounded-md text-sm font-semibold hover:opacity-85 transition mt-2">
                    {{ __('dashboard.login') }}
                </button>
            </form>

        </div>
    </div>

</body>
</html>