<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserValidator
{
	public static function validateOnCreate(Request $request)
	{
        $validator_assocArr = [
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
            'photo' => 'nullable|image|max:8192',
            'type' => [
                'required',
                Rule::in(['C', 'EC', 'EM', 'ED']),
            ]
        ];

        if($request->has('type') && $request->type == 'C')
        {
            $validator_assocArr += [
                'address' => 'required',
                'phone' => 'required',
                'nif' => 'nullable|numeric|digits:9'
            ];
        }

		return Validator::make($request->all(), $validator_assocArr);
    }

    public static function validateOnUpdate(Request $request, User $user)
	{
        $validator_assocArr = [
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => ['required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignoreModel($user)
            ],
            'photo' => 'nullable|image|max:8192'
        ];

        if($user->type == 'C')
        {
            $validator_assocArr += [
                'address' => 'required',
                'phone' => 'required',
                'nif' => 'nullable|numeric|digits:9'
            ];
        }

		return Validator::make($request->all(), $validator_assocArr);
	}
}
