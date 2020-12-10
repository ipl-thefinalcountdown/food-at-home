<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    public function register(Request $request)
    {
        if(Auth::check())
        {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized register on authenticated user.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
            'address' => 'required',
            'phone' => 'required',
            'nif' => 'nullable|numeric|digits:9',
            'photo' => 'nullable|image|max:8192'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => 'Bad request. Invalid data.',
                'errors' => $validator->errors()
            ], 400);
        }

        $user = new User();
        $user->fill($request->only('name', 'email', 'password'));
        $user->password = Hash::make($user->password);
        $user->save();

        $customer = new Customer();
        $customer->id = $user->id;
        $customer->fill($request->only('address', 'phone', 'nif'));
        $customer->user()->associate($user);

        if ($request->hasFile('photo')) {
            $path = $request->photo->store('public/fotos');
            $customer->photo_url = basename($path);
        }

        $customer->push();

        return response()->json($customer, 201);
    }
}