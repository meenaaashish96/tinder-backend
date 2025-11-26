<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(title="Tinder Clone Auth API", version="1.0")
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/register",
     * summary="Register a new user",
     * @OA\Parameter(name="name", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Parameter(name="password", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Response(response="200", description="User registered successfully")
     * )
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('authToken')->accessToken;

        return response()->json(['user' => $user, 'access_token' => $token], 201);
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Login user",
     * @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Parameter(name="password", in="query", required=true, @OA\Schema(type="string")),
     * @OA\Response(response="200", description="Login successful")
     * )
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response()->json(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
}