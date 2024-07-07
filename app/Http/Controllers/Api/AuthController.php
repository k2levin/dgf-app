<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            abort(response()->json(['message' => 'User not found.'], 401));
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            abort(response()->json(['message' => 'Invalid password.'], 401));
        }

        $datas = [
            'email' => $user->email,
            'token' => $user->createToken('api')->plainTextToken,
        ];

        return response()->json($datas, 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
        ]);

        return response()->json(['message' => 'OK'], 200);
    }
}
