<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle the verification role user
     *
     *
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->role === 'super_admin') {
            return $next($request);
        }

        if (in_array($request->user()->role, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
