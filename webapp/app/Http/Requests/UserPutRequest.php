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
            'name' => 'sometimes|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignoreModel($this->user)
            ],
            'password' => 'sometimes|min:3',
            'photo_url' => 'sometimes|image|max:8192',
            'type' => 'sometimes|in:EC,ED,EM',
        ];
    }
}
