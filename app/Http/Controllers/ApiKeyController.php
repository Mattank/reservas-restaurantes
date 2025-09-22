<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiLog;

class ApiKeyController extends Controller
{
    public static function check(?string $apiKey, ?Request $request = null)
    {
        $serverKey = config('app.api_key');

        // 🔹 valida API key
        if (!$apiKey || !$serverKey || $apiKey !== $serverKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key inválida ou ausente.',
                'errors'  => ['api_key' => ['Forneça uma chave válida.']],
                'data'    => null,
            ], 401);
        }

        // 🔹 contabiliza a chamada
        if ($request && str_starts_with($request->path(), 'api/')) {
            ApiLog::updateOrCreate(
                [
                    'method' => $request->method(),
                    'uri'    => $request->path(),
                ],
                [
                    'count' => DB::raw('count + 1'),
                ]
            );
        }

        return false; // tudo ok, segue fluxo da API
    }
}
