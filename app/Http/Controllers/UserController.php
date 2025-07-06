<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="FEMAQUA API",
 *     description="API para gerenciamento de ferramentas e usuários",
 *     @OA\Contact(
 *         name="Andressa Silva",
 *         email="andressasp68@gmail.com"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id","name","email","type"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Maria Dev"),
 *     @OA\Property(property="email", type="string", example="maria@example.com"),
 *     @OA\Property(property="type", type="string", example="user")
 * )
 */
class UserController extends Controller
{
   /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List all users",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $authUser = Auth::user();

        if ($authUser->type !== 'admin') {
            return response()->json(['message' => 'Apenas administradores podem acessar esta rota.'], 403);
        }

        return response()->json(User::all());
    }


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="Maria Dev"),
     *             @OA\Property(property="email", type="string", example="maria@example.com"),
     *             @OA\Property(property="password", type="string", example="123456"),
     *             @OA\Property(property="type", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate(
                [
                    'name' => 'required|string|max:100',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6',
                    'type' => 'in:user,admin',
                ],
                [
                    'email.unique' => 'Este e-mail já está cadastrado no sistema.',
                    'password.min' => 'A senha deve conter no mínimo 6 caracteres.',
                    'email.required' => 'O campo de e-mail é obrigatório.',
                    'name.required' => 'O campo de nome é obrigatório.',
                    'password.required' => 'O campo de senha é obrigatório.',
                    'type.in' => 'O tipo de usuário deve ser "user" ou "admin".'
                ]
            );

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'type' => $validated['type'] ?? 'user',
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Usuário cadastrado com sucesso.',
                'user' => $user,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação ao cadastrar usuário.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar o usuário.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get a user by ID",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(string $id)
    {
        $authUser = Auth::user();

        if ($authUser->type !== 'admin') {
            return response()->json(['message' => 'Apenas administradores podem acessar esta rota.'], 403);
        }

        $user = User::findOrFail($id);
        return response()->json($user);
    }


    /**
     * @OA\Put(
     *     path="/api/users/update/{id}",
     *     summary="Update user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Maria Dev"),
     *             @OA\Property(property="email", type="string", example="maria@example.com"),
     *             @OA\Property(property="password", type="string", example="123456"),
     *             @OA\Property(property="type", type="string", example="user")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:100',
            'email' => 'email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'type' => 'in:user,admin,superadmin',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/delete/{id}",
     *     summary="Delete user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User deleted successfully"),
     *     @OA\Response(response=404, description="User not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario deletado com sucess.']);
    }
}
