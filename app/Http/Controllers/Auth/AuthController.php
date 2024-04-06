<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // Method for user authentication
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => Auth::factory()->getTTL() * 60 // Thời gian sống của token (tùy chọn)
            'username' => auth()->user()->name, // Lấy name của người dùng và thêm vào phản hồi
        ]);

    }

    // Method to get authenticated user
    public function me()
    {
        return response()->json(auth()->user());
    }

    // Method to logout user (invalidate token)
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Method to refresh JWT token
    public function refresh()
    {
        return response()->json(['token' => auth()->refresh()]);
    }
}