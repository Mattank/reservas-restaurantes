<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiKeyController;

class DashboardController extends Controller
{

   public function index()
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->filter(fn($route) => str_starts_with($route->uri(), 'api/'))
            ->reject(fn($route) => in_array($route->uri(), ['api/documentation', 'api/oauth2-callback']))
            ->map(function ($route) {
                return [
                    'method' => implode('|', $route->methods()),
                    'uri'    => $route->uri(),
                ];
            });

        // Logs salvos no banco
        $logs = \App\Models\ApiLog::all()->keyBy(function ($log) {
            return $log->method . ' ' . $log->uri; // chave = método + uri
        });

        // Monta estatísticas
        $stats = $routes->map(function ($route) use ($logs) {
            $key = $route['method'] . ' ' . $route['uri'];
            return [
                'method' => $route['method'],
                'uri'    => $route['uri'],
                'count'  => $logs[$key]->count ?? 0
            ];
        });

        return view('index', ['stats' => $stats]);
    }

}
