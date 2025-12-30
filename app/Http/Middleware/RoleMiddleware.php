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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Allow multiple roles separated by comma
        $roles = explode(',', $role);
        
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}