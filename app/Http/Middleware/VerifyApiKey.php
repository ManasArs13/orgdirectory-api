<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');

        if ($apiKey !== env('API_KEY')) {
            return response()->json([
                'message' => $apiKey ? 'Неверный API ключ' : 'Остутсвует API ключ'
            ], 401);
        }

        return $next($request);
    }
}
