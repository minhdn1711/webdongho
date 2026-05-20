<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class AdminLoginController extends Controller
{
    public function create(): Response|RedirectResponse
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Admin/Auth/Login', [
            'status' => session('status'),
            'error'  => session('error'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($request->string('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
            ]);
        }

        try {
            $credentials = $request->only('email', 'password');
            $authenticated = Auth::attempt($credentials, $request->boolean('remember'));
        } catch (RuntimeException) {
            // Password stored with non-bcrypt algorithm (MD5, SHA1, v.v.)
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'email' => 'Tài khoản này cần đặt lại mật khẩu. Vui lòng liên hệ quản trị viên.',
            ]);
        }

        if (!$authenticated) {
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'email' => 'Tài khoản này không có quyền truy cập trang quản trị.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
