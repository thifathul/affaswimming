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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect to their default dashboard if unauthorized
        switch ($user->role) {
            case 'master':
                return redirect()->route('master.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'pelatih':
                return redirect()->route('pelatih.dashboard');
            case 'murid':
            default:
                return redirect()->route('murid.dashboard');
        }
    }
}
