<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Info(title="Tracking API", version="1.0.0")
 * @OA\Server(url="/api")
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token"
 * )
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints de autenticación"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Post(
     *   path="/register",
     *   tags={"Auth"},
     *   summary="Registro de usuario",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password"},
     *       @OA\Property(property="name", type="string", example="Ander Code"),
     *       @OA\Property(property="email", type="string", example="ander@example.com"),
     *       @OA\Property(property="password", type="string", example="Secret123!"),
     *       @OA\Property(property="device", type="string", example="Xiaomi Redmi")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Registrado"),
     *   @OA\Response(response=422, description="Validación")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required', Password::min(8)],
            'device'   => ['nullable','string','max:255'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'device'   => $data['device'] ?? null,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     *   path="/login",
     *   tags={"Auth"},
     *   summary="Login de usuario",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", example="ander@example.com"),
     *       @OA\Property(property="password", type="string", example="Secret123!")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Autenticado"),
     *   @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        // Opcional: revocar tokens antiguos
        $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Login correcto',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/logout",
     *   tags={"Auth"},
     *   summary="Cerrar sesión (revoca el token actual)",
     *   security={{"sanctum":{}}},
     *   @OA\Response(response=200, description="Token revocado")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}
