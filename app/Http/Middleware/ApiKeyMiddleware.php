<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');

        // You can store multiple keys in database for different branches
        $validKeys = [
            'branch1_key' => 1,  // branch_id => key
            'branch2_key' => 2,
            'branch3_key' => 3,
        ];

        if (!$apiKey || !isset($validKeys[$apiKey])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Attach branch_id to request
        $request->attributes->add(['branch_id' => $validKeys[$apiKey]]);

        return $next($request);
    }
}
