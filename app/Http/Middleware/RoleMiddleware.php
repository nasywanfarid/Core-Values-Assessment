<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        // Blokir role HR dari semua aksi penghapusan (DELETE method)
        if (auth()->user()->role === 'hr' && $request->isMethod('delete')) {
            abort(403, 'Role HR tidak memiliki izin untuk menghapus data.');
        }

        return $next($request);
    }
}
