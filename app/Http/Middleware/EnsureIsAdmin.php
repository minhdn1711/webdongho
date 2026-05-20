<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            Auth::guard('web')->logout();
            return redirect()->route('admin.login')
                ->with('error', 'Bạn không có quyền truy cập trang quản trị.');
        }

        return $next($request);
    }
}
