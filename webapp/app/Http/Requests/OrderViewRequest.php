<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderViewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->user();
        $order = $this->order;
        return $user->type == 'EM'
            || ($user->type == 'EC' && $user->id == $order->prepared_by || $order->status == 'H')
            || ($user->type == 'C' && $order->customer_id == $user->id)
            || ($user->type == 'ED' && $order->delivered_by == $user->id || $order->status == 'R' );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
