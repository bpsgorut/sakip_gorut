<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan request adalah AJAX atau meminta JSON
        if ($request->ajax() || $request->wantsJson()) {
            $response = $next($request);

            // Pastikan respons adalah JSON
            if (!$response->headers->contains('Content-Type', 'application/json')) {
                $response->headers->set('Content-Type', 'application/json');
            }

            return $response;
        }

        return $next($request);
    }
}
