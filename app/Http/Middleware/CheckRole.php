<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role ?? 'member'; // Default ke member jika null
        
        // Check if user has any of the required roles
        if (!in_array($userRole, $roles)) {
            $roleNames = implode(' or ', $roles);
            abort(403, "Unauthorized access. Required role(s): {$roleNames}. Your role: {$userRole}");
        }

        return $next($request);
    }
}