<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // FIXME: only a manager can access this
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
            'price' => 'sometimes|numeric|max:999999.99|min:0.00',
            'type' => 'sometimes|in:drink,dessert,hot dish,cold dish',
            'description' => 'sometimes|string',
            'photo_url' => 'sometimes|image|max:8192'
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Name contains invalid characters',
            'price.numeric' => 'Price must be a number',
            'price.max' => 'Price must be bellow 999999.99',
            'price.min' => 'Price can not be negative',
            'type.in' => 'Product type must be one of (drink, dessert, hot dish, cold dish)',
            'photo_url.image' => 'Photo must be an image',
            'photo_url.max' => 'Maximum photo size is 8192'
        ];
    }
}
