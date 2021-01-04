<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthPutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignoreModel($this->user())
            ],
            'password' => 'sometimes|min:3',
            'photo' => 'sometimes|nullable|image|max:8192',
            'address' => 'sometimes|string',
            'nif' => 'sometimes|nullable|digits:9',
            'phone' => 'sometimes|max:20|min:9',
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Invalid name format',
            'email.email' => 'Invalid email',
            'email.unique' => 'A registed user already has the same email',
            'password.min' => 'Password must have a minimum of 3 characters',
            'photo.image' => 'Photo must be an image',
            'photo.max' => 'Photo must not be higher than 8192',
            'phone.alpha_num' => 'Phone must have alpha numeric characters',
            'phone.min' => 'Phone must have at least 9 digits',
            'phone.max' => 'Phone must have a maximum of 20 alpha numeric characters',
            'nif.digits' => 'Nif must have 9 digits',
        ];
    }
}
