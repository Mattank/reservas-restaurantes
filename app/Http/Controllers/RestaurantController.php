<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

use App\Http\Controllers\ApiKeyController;

class RestaurantController extends Controller
{

    public function index(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey, $request)) {
                return $resp;
            }


            $perPage = $request->get('per_page', 1);
            $restaurants = Restaurant::paginate($perPage);

            if ($restaurants->isEmpty()) {
                return response()->json([
                    'status' => 'Sucesso',
                    'message' => 'Nenhum restaurante foi encontrado',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Restaurantes encontrados com sucesso.',
                'data' => $restaurants
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi possível buscar restaurantes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey, $request)) {
                return $resp;
            }


            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|digits_between:8,20',
                'qtd_mesas' => 'required|string|regex:/^[0-9]{1,4}$/',
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O campo nome deve ser texto.',
                'address.required' => 'O campo endereço é obrigatório.',
                'address.string' => 'O campo endereço deve ser texto.',
                'phone.required' => 'O campo telefone é obrigatório.',
                'phone.digits_between' => 'O telefone deve conter entre 8 e 20 números.',
                'qtd_mesas.required' => 'O campo quantidade de mesas é obrigatório.',
                'qtd_mesas.regex' => 'A quantidade de mesas deve conter apenas números (máx. 4 dígitos).',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dados cadastrais inválidos.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $restaurant = Restaurant::create($validator->validated());

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Restaurante cadastrado com sucesso.',
                'data'    => $restaurant
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível cadastrar novo restaurante.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey, $request)) {
                return $resp;
            }


            $restaurant = Restaurant::findOrFail($id);

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Restaurante encontrado com sucesso.',
                'data'    => $restaurant
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível encontrar o restaurante',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey, $request)) {
                return $resp;
            }


            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|digits_between:8,20',
                'qtd_mesas' => 'required|string|regex:/^[0-9]{1,4}$/',
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O campo nome deve ser texto.',
                'address.required' => 'O campo endereço é obrigatório.',
                'address.string' => 'O campo endereço deve ser texto.',
                'phone.required' => 'O campo telefone é obrigatório.',
                'phone.digits_between' => 'O telefone deve conter entre 8 e 20 números.',
                'qtd_mesas.required' => 'O campo quantidade de mesas é obrigatório.',
                'qtd_mesas.regex' => 'A quantidade de mesas deve conter apenas números (máx. 4 dígitos).',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Requisição inválida.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $restaurant = Restaurant::findOrFail($request->id);
            $restaurant->update($validator->validated());

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Restaurante atualizado com sucesso.',
                'data'    => $restaurant
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível atualizar os dados do restaurante.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey, $request)) {
                return $resp;
            }


            $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:restaurants,id',
            ], [
                'id.required' => 'O campo ID é obrigatório.',
                'id.integer'  => 'O campo ID deve ser um número inteiro.',
                'id.exists'   => 'O restaurante informado não existe.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Requisição inválida',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $restaurant = Restaurant::findOrFail($id);
            $restaurant->delete();

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Restaurante deletado com sucesso.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível deletar o restaurante.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
