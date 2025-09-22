<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ReservasController extends Controller
{

    public function index(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey)) {
                return $resp;
            }

            $perPage = $request->get('per_page', 1);
            $reservas = Reserva::paginate($perPage);

            if ($reservas->isEmpty()) {
                return response()->json([
                    'status' => 'Sucesso',
                    'message' => 'Nenhuma reserva foi encontrada',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Reservas encontradas com sucesso.',
                'data' => $reservas
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi possível buscar reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey)) {
                return $resp;
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'data' => 'required|date|after_or_equal:today',
                'hora' => 'required|date_format:H:i',
            ], [
                'user_id.required' => 'O campo usuário é obrigatório.',
                'user_id.integer' => 'O ID do usuário deve ser um número inteiro.',
                'user_id.exists' => 'O usuário informado não existe.',

                'restaurant_id.required' => 'O campo restaurante é obrigatório.',
                'restaurant_id.integer' => 'O ID do restaurante deve ser um número inteiro.',
                'restaurant_id.exists' => 'O restaurante informado não existe.',

                'data.required' => 'A data da reserva é obrigatória.',
                'data.date' => 'A data da reserva deve ser uma data válida.',
                'data.after_or_equal' => 'A data da reserva não pode ser no passado.',

                'hora.required' => 'A hora da reserva é obrigatória.',
                'hora.date_format' => 'A hora deve estar no formato HH:MM (24h).',
            ]);

            $dados = $validator->validated();

            $reservaExiste = Reserva::where("data", $dados["data"])
                ->where("hora", $dados["hora"])
                ->where("restaurant_id", $dados["restaurant_id"])
                ->where("user_id", $dados["user_id"])
                ->count();

            if ($reservaExiste > 0) {
                 return response()->json([
                    'status'  => 'error',
                    'message' => 'Dados cadastrais inválidos.',
                    'errors'  => 'Este Usuário já possui reserva para este horario.',
                ], 422);
            }

            $reservasCount = Reserva::where("data", $dados["data"])
                ->where("hora", $dados["hora"])
                ->where("restaurant_id", $dados["restaurant_id"])
                ->count();

            $qtd_mesas = Restaurant::where('id', $dados["restaurant_id"])->value('qtd_mesas');

            if ($reservasCount >= $qtd_mesas) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dados cadastrais inválidos.',
                    'errors'  => 'Não há vagas para este horário.',
                ], 422);
            }

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dados cadastrais inválidos.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $reserva = Reserva::create($dados);

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Reserva cadastrada com sucesso.',
                'data'    => $reserva
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível cadastrar nova reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey)) {
                return $resp;
            }

            $reserva = Reserva::findOrFail($id);

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Reserva encontrada com sucesso.',
                'data'    => $reserva
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível encontrar a reserva',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey)) {
                return $resp;
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:users,id',
                'restaurant_id' => 'required|integer|exists:restaurants,id',
                'data' => 'required|date|after_or_equal:today',
                'hora' => 'required|date_format:H:i',
            ], [
                'user_id.required' => 'O campo usuário é obrigatório.',
                'user_id.integer' => 'O ID do usuário deve ser um número inteiro.',
                'user_id.exists' => 'O usuário informado não existe.',

                'restaurant_id.required' => 'O campo restaurante é obrigatório.',
                'restaurant_id.integer' => 'O ID do restaurante deve ser um número inteiro.',
                'restaurant_id.exists' => 'O restaurante informado não existe.',

                'data.required' => 'A data da reserva é obrigatória.',
                'data.date' => 'A data da reserva deve ser uma data válida.',
                'data.after_or_equal' => 'A data da reserva não pode ser no passado.',

                'hora.required' => 'A hora da reserva é obrigatória.',
                'hora.date_format' => 'A hora deve estar no formato HH:MM (24h).',
            ]);

            $dados = $validator->validated();

            $reservasCount = Reserva::where('data', $dados['data'])
                ->where('hora', $dados['hora'])
                ->where('restaurant_id', $dados['restaurant_id'])
                ->count();

            $qtd_mesas = Restaurant::where('id', $dados['restaurant_id'])->value('qtd_mesas');

            if ($reservasCount >= $qtd_mesas) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dados cadastrais inválidos.',
                    'errors'  => 'Não há vagas para este horário.',
                ], 422);
            }

            $reserva = Reserva::findOrFail($id);
            $reserva->update($dados);

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Reserva atualizada com sucesso.',
                'data'    => $reserva
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível atualizar os dados da reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $apiKey = $request->header('API-KEY');
            if ($resp = ApiKeyController::check($apiKey)) {
                return $resp;
            }

            $reserva = Reserva::findOrFail($id);
            $reserva->delete();

            return response()->json([
                'status'  => 'Sucesso',
                'message' => 'Reserva deletada com sucesso.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Não foi possível deletar a reserva.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
