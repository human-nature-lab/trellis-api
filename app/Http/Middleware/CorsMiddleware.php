<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class CorsMiddleware
{
    protected $headers = [
        'Content-Type',
        'Content-Length',
        'X-Key',
        'X-Token',
        'X-Powered-By',
        'Authorization'
    ];

    protected $methods = [
        'GET',
        'HEAD',
        'DELETE',
        'PUT',
        'POST',
        'PATCH'
    ];

    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedMethods = implode(', ', $this->methods);
        $allowedHeaders = implode(', ', $this->headers);

        if ($request->isMethod('OPTIONS')) {
            $response = new Response('', Response::HTTP_NO_CONTENT);
            $response->headers->set('Access-Control-Allow-Methods', $allowedMethods);
            $response->headers->set('Access-Control-Allow-Headers', $allowedHeaders);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Vary', 'Origin');
            return $response;
        }

        $response = $next($request);
        $response->headers->set('Access-Control-Expose-Headers', $allowedHeaders);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Vary', 'Origin');
        return $response;
    }
}
