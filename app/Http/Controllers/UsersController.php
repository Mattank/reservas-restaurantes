<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $users = User::paginate($perPage);

            if ($users->isEmpty()) {
                return response()->json([
                    'status' => 'Sucesso',
                    'message' => 'Nenhum usuário encontrado',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Usuários encontrados com sucesso.',
                'data' => $users
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao buscar usuários.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ], [
                'name.required'     => 'O campo nome é obrigatório.',
                'email.required'    => 'O campo e-mail é obrigatório.',
                'email.email'       => 'O e-mail informado não é válido.',
                'email.unique'      => 'Este e-mail já está em uso.',
                'password.required' => 'A senha é obrigatória.',
                'password.min'      => 'A senha deve ter pelo menos 6 caracteres.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dados inválidos.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Usuário cadastrado com sucesso.',
                'data' => $user
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao cadastrar usuário.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Usuário encontrado com sucesso.',
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuário não encontrado.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name'     => 'sometimes|string|max:255',
                'email'    => "sometimes|email|unique:users,email,$id",
                'password' => 'sometimes|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Dados inválidos.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($id);

            $data = $validator->validated();
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            $user->update($data);

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Usuário atualizado com sucesso.',
                'data' => $user
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar usuário.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => 'Sucesso',
                'message' => 'Usuário deletado com sucesso.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao deletar usuário.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
