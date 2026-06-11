<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return redirect()->route('dashboard.home');
        }
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, true)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('dashboard.login_failed')]);
        }

        if (! Auth::user()->hasRole('admin')) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('dashboard.login_not_admin')]);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard.home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('dashboard.login');
    }

    /**
     * Toggle locale stored in the session. Accepts 'ar' or 'en'.
     */
    public function switchLocale(Request $request, string $locale)
    {
        if (in_array($locale, ['ar', 'en'], true)) {
            $request->session()->put('dashboard_locale', $locale);
        }
        return back();
    }
}