<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopIsRegistered
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->junkshop) {
            return redirect()->route('operator.onboarding')
                ->with('status', 'Finish setting up your shop profile first.');
        }

        return $next($request);

    }
}
