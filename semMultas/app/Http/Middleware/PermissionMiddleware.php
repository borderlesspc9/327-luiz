<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = Auth::guard('api')->user();
        
        if(!$user) return response()->json(['error' => 'Unauthorized'], 401);

        foreach($permissions as $permission)
        {
            if($user->hasPermission($permission)) return $next($request);
        }
        
        return response()->json(['error' => 'Forbidden'], 403);
    }
}
