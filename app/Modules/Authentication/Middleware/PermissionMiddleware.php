<?php
namespace App\Modules\Authentication\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $permissions = is_array($permissions) ? $permissions : explode('|', $permissions);

        if (!$request->user()->hasAnyPermission($permissions)) {
            throw UnauthorizedException::forPermissions($permissions);
        }

        return $next($request);
    }
}
