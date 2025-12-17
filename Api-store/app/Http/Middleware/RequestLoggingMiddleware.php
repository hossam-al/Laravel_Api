<?php

namespace App\Http\Middleware;

use App\Models\RequestLog;
use Closure;
use Illuminate\Http\Request;

class RequestLoggingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $path = '/'.ltrim($request->path(), '/');

        // Skip logging for admin UI, health, and static files
        $excludedPrefixes = ['/admin', '/up', '/favicon.ico', '/robots.txt'];
        foreach ($excludedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $next($request);
            }
        }

        $response = $next($request);

        try {
            RequestLog::create([
                'method' => $request->method(),
                'path' => $path,
                'status_code' => $response->getStatusCode(),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 512),
                'user_id' => optional($request->user())->id,
            ]);
        } catch (\Throwable $e) {
            // Avoid breaking the request if logging fails
        }

        return $response;
    }
}


