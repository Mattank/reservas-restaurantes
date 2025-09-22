<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('API-KEY');

        if ($apiKey !== config('app.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'API Key inválida ou não fornecida.',
                'errors'  => ['api_key' => ['A chave enviada é inválida.']],
                'data'    => null,
            ], 401);
        }

        return $next($request);
    }
}
