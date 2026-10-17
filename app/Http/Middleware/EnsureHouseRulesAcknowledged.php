<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHouseRulesAcknowledged
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->hasAcknowledgedCurrentHouseRules()) {
            return redirect()->route('house-rules', ['return_to' => 'dashboard']);
        }

        return $next($request);
    }
}