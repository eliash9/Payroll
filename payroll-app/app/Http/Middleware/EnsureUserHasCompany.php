<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasCompany
{
    /**
     * Reject access if authenticated user has no company_id.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && !$user->company_id) {
            abort(403, 'Forbidden: user has no company scope.');
        }

        return $next($request);
    }
}
