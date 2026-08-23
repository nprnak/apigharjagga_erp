<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // The admin panel authenticates on the dedicated "admin" guard, so
        // check that guard here rather than the default (web) guard.
        if ($request->user('admin')?->role !== 'admin') {
            abort(403);
        }

        return $next($request);
    }
}
