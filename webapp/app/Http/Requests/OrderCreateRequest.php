<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderCreateRequest extends FormRequest
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
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:App\Models\Product,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ];
    }
}
