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
}
