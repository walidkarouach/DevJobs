<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
{
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'message' => 'Compte créé avec succès.',
        'token' => $token,
        'user' => $user,
    ],201);
}

public function login(LoginRequest $request)
{
    if (!Auth::attempt($request->only('email','password'))) {

        return response()->json([
            'message'=>'Email ou mot de passe incorrect.'
        ],401);

    }

    $user = Auth::user();

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'token'=>$token,
        'user'=>$user
    ]);
}


public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message'=>'Déconnexion réussie.'
    ]);
}
}
