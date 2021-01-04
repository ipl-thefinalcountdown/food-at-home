<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // authorization is made on route and controller level
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
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:3',
            'photo' => 'nullable|image|max:8192',
            'address' => 'required',
            'phone' => 'required|max:20|min:9',
            'nif' => 'nullable|digits:9'
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Invalid name format',
            'email.email' => 'Invalid email',
            'email.unique' => 'A registed user already has the same email',
            'password.min.' => 'Password must have a minimum of 3 characters',
            'photo.image' => 'Photo must be an image',
            'photo.size' => 'Photo must not be higher than 8192',
            'phone.alpha_num' => 'Phone must have alpha numeric characters',
            'phone.min' => 'Phone must have at least 9 digits',
            'phone.max' => 'Phone must have a maximum of 20 alpha numeric characters',
            'nif.digits' => 'Nif must have 9 digits',
        ];
    }
}
