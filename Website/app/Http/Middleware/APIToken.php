<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class APIToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = User::where('api_token', $request->api_token)->first();
        if ($user == null) {
            return response()->json([
                'success' => false,
                'message' => 'Not a valid API request.',
            ]);
        }

        return $next($request);
    }
}
