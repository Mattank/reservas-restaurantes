<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

class ApiKeyController extends Controller
{
    public static function check(?string $apiKey)
    {
        $serverKey = config('app.api_key');

        if (!$apiKey || !$serverKey || $apiKey !== $serverKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key inválida ou ausente.',
                'errors'  => ['api_key' => ['Forneça uma chave válida.']],
                'data'    => null,
            ], 401);
        }

        return false;
    }
}
