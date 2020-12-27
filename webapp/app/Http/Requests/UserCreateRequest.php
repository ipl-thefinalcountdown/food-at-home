<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCreateRequest extends FormRequest
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

        if($this->has('type') && $this->type == 'C')
        {
            $validator_assocArr += [
                'address' => 'required',
                'phone' => 'required',
                'nif' => 'nullable|numeric|digits:9'
            ];
        }
        return $validator_assocArr;
    }
}
