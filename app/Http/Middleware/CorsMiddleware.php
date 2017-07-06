<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CorsMiddleware
{
    protected $headers = [
        'Content-Type',
        'Content-Length',
        'X-Key',
        'X-Token',
        'X-Powered-By'
    ];

    protected $methods = [
        'GET',
        'HEAD',
        'DELETE',
        'PUT',
        'POST',
        'PATCH'
    ];

    public function handle($request, Closure $next)
    {
        $allowedMethods = implode(', ', $this->methods);
        $allowedHeaders = implode(', ', $this->headers);

        if ($request->isMethod('OPTIONS')) {
            $response = new Response('', Response::HTTP_NO_CONTENT);
            $response->header('Access-Control-Allow-Methods', $allowedMethods);
            $response->header('Access-Control-Allow-Headers', $allowedHeaders);
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Vary', 'Origin');
            return $response;
        }

        $response = $next($request);
        $response->header('Access-Control-Expose-Headers', $allowedHeaders);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Vary', 'Origin');
        return $response;
    }
}
