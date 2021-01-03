<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\Product;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $product = Product::find($this->product_id);
        return [
			'id' => $this->id,
			'product_id' => $this->product_id,
			'product_name' => ($product) ? $product->name : '',
            'quantity' => $this->quantity,
		];
    }
}
