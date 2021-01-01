<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserPutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user->type != 'C';
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
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignoreModel($this->user)
            ],
            'password' => 'nullable|min:3',
            'photo' => 'nullable|image|max:8192',
            'type' => 'required|in:EC,ED,EM',
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
            'type.in' => 'Type must be one of (EC, ED, EM)',
        ];
    }
}
