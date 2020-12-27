<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // FIXME: only a manager should be able to do this
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
            'price' => 'required|numeric|max:999999.99|min:0.00',
            'type' => 'in:drink,dessert,hot dish,cold dish',
            'description' => 'required',
            'photo_url' => 'required|image|max:8192'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'A name is required',
            'name.regex' => 'Name contains invalid characters',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.max' => 'Price must be bellow 999999.99',
            'price.min' => 'Price can not be negative',
            'type.in' => 'Product type must be one of (drink, dessert, hot dish, cold dish)',
            'description.required' => 'Description is required, please insert an empty text for no description',
            'photo.required' => 'A product must have a photo',
            'photo_url.image' => 'Photo must be an image',
            'photo_url.max' => 'Maximum photo size is 8192'
        ];
    }
}
