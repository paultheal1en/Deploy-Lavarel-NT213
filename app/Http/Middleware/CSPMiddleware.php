<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CSPMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'nonce-<random_nonce>'; img-src 'self'; style-src 'self'; font-src 'self'; frame-ancestors 'none'; upgrade-insecure-requests;");
        return $response;
    }
}