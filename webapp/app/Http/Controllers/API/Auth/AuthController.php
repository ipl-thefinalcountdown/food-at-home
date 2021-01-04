<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\UserValidator;
use App\Http\Requests\AuthPostRequest;

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
                'access_token' => $tokenResult,
                'user' => $request->user()
            ]);
        }

        return response()->json([
            'status_code' => 401,
            'message' => 'Unauthorized'
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $tokens = ($request->boolean('all'))
            ? $user->tokens()
            : $user->currentAccessToken();

        $tokens->delete();

        return response()->json([
            'status_code' => 200,
            'message' => 'Logout successfully.'
        ]);
    }

    public function register(AuthPostRequest $request)
    {
        $request->validated();

        $user = new User();
        $user->fill(array_merge($request->only('name', 'email') + ['type' => 'C']));
        $user->password = Hash::make($request->password);
        $user->save();

        $customer = new Customer();
        $customer->id = $user->id;
        $customer->fill($request->only('address', 'phone', 'nif'));
        $customer->user()->associate($user);

        if ($request->hasFile('photo')) {
            $path = $request->photo->store('public/fotos');
            $user->photo_url = basename($path);
        }

        $customer->push();
    }
}
