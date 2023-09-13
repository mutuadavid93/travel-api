<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    //  TIP: $role param is the value after full colon e.g. role:admin the role is admin
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Not logged in
        if (!auth()->check()) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        // Logged in but no permissions
        if (!auth()->user()->roles()->where("name", $role)->exists()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        // Otherwise move on
        return $next($request);
    }
}
