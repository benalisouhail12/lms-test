<?php

namespace App\Modules\Authentication\Middleware;

use App\Modules\Authentication\Models\UserSession;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && $request->has('session_id')) {
            $sessionId = $request->session_id;
            $user = $request->user();

            $session = UserSession::where('id', $sessionId)
                ->where('user_id', $user->id)
                ->first();

            if ($session) {
                // Check if session is expired
                if ($session->expires_at < Carbon::now()) {
                    // Session expired, logout the user
                    Auth::logout();
                    return response()->json(['message' => 'Session expired'], 401);
                }

                // Update last activity
                $session->last_activity = Carbon::now();
                $session->save();
            }
        }

        return $next($request);
    }
}
