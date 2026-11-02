<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors(['email' => 'Silakan login terlebih dahulu sebagai admin/panitia.']);
        }

        if (! Auth::user()->isAdmin()) {
            return redirect()->route('home')->withErrors(['error' => 'Akses ditolak. Halaman ini khusus panitia/admin.']);
        }

        return $next($request);
    }
}
