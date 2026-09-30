<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Keep administrator sessions persistent until the explicit Sign Out action.
        config([
            'session.lifetime' => (int) env('ADMIN_SESSION_LIFETIME', 5256000),
            'session.expire_on_close' => false,
        ]);

        if (! session()->has('admin_id')) {
            return redirect()->route('admin.login')->with('admin_error', 'Sign in as an administrator to continue.');
        }

        return $next($request);
    }
}
