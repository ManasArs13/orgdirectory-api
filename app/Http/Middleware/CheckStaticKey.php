<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStaticKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validKey = env('API_KEY');

        if (empty($validKey)) {
            return response()->json([
                'message' => 'Статический ключ не сгенерирован, используйте команду php artisan app:set-api-key'
            ], 501); 
        }

        return $next($request);
    }
}
