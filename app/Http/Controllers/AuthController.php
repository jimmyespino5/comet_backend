<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar rol por defecto (opcional, requiere Spatie)
        $user->assignRole('user');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Cargamos los roles para que React sepa qué permisos tiene el usuario
        $user->load('roles'); 

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hola ' . $user->name,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        //auth()->user()->tokens()->delete();
        // Revocar el token que se está usando actualmente
        //Auth::user()->currentAccessToken()->delete();

        /** @var \App\Models\User $user */
        $user = $request->user();

        // 2. Verificamos que el usuario exista y tenga un token actual
        if ($user && $user->currentAccessToken()) {
            /** @var \Laravel\Sanctum\PersonalAccessToken $token */
            $token = $user->currentAccessToken();
            $token->delete();
        }

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}