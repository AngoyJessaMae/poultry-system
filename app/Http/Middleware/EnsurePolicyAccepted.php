<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePolicyAccepted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->hasAcceptedCurrentPolicy()) {
            return redirect()->route('account-policy', ['return_to' => 'dashboard']);
        }

        return $next($request);
    }
}