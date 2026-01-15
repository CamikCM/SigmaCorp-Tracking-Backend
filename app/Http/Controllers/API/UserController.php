<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\VisitadorMedico;
use App\Models\EstadoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'device' => ['nullable', 'string', 'max:255'],

            // opcional: si registras desde la app móvil
            'tipo' => ['nullable', 'in:visitador_medico,supervisor'],
            'sucursal_id' => ['nullable', 'integer'],
        ]);

        // Persona mínima (todo nullable excepto habilitado)
        $persona = Persona::create([
            'nombre' => $data['name'],
            'habilitado' => true,
        ]);

        $user = Usuario::create([
            'persona_id' => $persona->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'device' => $data['device'] ?? null,
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Si el registro viene del móvil, por defecto lo tratamos como visitador
        $tipo = $data['tipo'] ?? 'visitador_medico';

        if ($tipo === 'visitador_medico') {
            $sucursalId = $data['sucursal_id'] ?? 1;

            $estadoOff = EstadoUser::query()->where('codigo', 'OFF')->first();
            if (! $estadoOff) {
                // Si todavía no existe, no reventamos el registro.
                $estadoOffId = null;
            } else {
                $estadoOffId = $estadoOff->id;
            }

            // Crea el perfil de visitador si no existe
            VisitadorMedico::query()->firstOrCreate(
                ['persona_id' => $persona->id],
                [
                    'sucursal_id' => $sucursalId,
                    'estado_user_id' => $estadoOffId ?? 1,
                    'activo' => true,
                ]
            );
        }

        // Token Sanctum
        $tokenName = $data['device'] ?? 'api';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Registro correcto',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Usuario::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no coinciden.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Usuario deshabilitado',
            ], 403);
        }

        // Actualiza auditoría
        $user->forceFill([
            'device' => $data['device'] ?? $user->device,
            'last_login_at' => now(),
        ])->save();

        // Token
        $tokenName = $data['device'] ?? 'api';
        $token = $user->createToken($tokenName)->plainTextToken;

        return response()->json([
            'message' => 'Login correcto',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Logout correcto',
        ], 200);
    }
}
