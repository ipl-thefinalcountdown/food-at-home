<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $tokenResult = $request->user()->createToken('authToken')->plainTextToken;
            return response()->json([
                'status_code' => 200,
                'access_token' => $tokenResult
            ]);
        }

        return response()->json([
            'status_code' => 401,
            'message' => 'Unauthorized'
        ], 401);
    }

    public function logout(Request $request)
    {
        if($request->boolean('all'))
        {
            $request->user()->tokens()->delete();
        }
        else
        {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Logout successfully.'
        ]);
    }
}
