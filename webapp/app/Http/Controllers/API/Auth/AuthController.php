<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\UserValidator;
use App\Http\Helpers\OrdersQueue;
use App\Http\Requests\UserCreateRequest;
use Carbon\Carbon;
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
            $user = $request->user();
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user->logged_at = Carbon::now();

            if(is_null($user->available_at))
                $user->available_at = Carbon::now();

            $user->save();

            if($user->type == 'EC')
                OrdersQueue::reassignOrders();

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
        $user->logged_at = null;
        $user->save();

        if($user->type == 'EC')
        {
            OrdersQueue::removeAssignedOrders($user);
            OrdersQueue::reassignOrders();
        }

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
