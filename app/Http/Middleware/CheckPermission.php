<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            abort(403);
        }

        if (!auth()->user()->can($permission)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
