<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;



class AuthController extends Controller
{
   /**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Auth"},
 *     summary="Login do usuário e geração do token JWT",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="andressa@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="123456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login realizado com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Login realizado com sucesso."),
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJI..."),
 *             @OA\Property(property="user", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciais inválidas",
 *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Credenciais inválidas."))
 *     ),
 *     @OA\Response(response=422, description="Erro de validação no login"),
 *     security={}
 * )
 */
    // Login - gera token JWT
   public function login(Request $request)
   {
    try {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciais inválidas.',
            ], 401);
        }

        $user = JWTAuth::user();

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'token' => $token,
            'user' => $user,
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Erro de validação no login.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro interno ao realizar login.',
            'error' => $e->getMessage(),
        ], 500);
    }
    }


    // Logout - invalida token
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    // Pegar usuário logado
    public function me()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return response()->json($user);
    }
}
