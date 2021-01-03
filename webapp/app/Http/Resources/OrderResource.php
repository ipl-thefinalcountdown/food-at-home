<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use App\Models\User;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = User::withTrashed()
            ->where('id', $this->customer_id)->first();
        $cook = User::withTrashed()
            ->where('id', $this->prepared_by)->first();
        $deliverman = User::withTrashed()
            ->where('id', $this->delivered_by)->first();
        return [
			'id' => $this->id,
			'status' => $this->status,
			'customer_id' => $this->customer_id,
			'customer_name' => ($user) ? $user->name : '',
            'total_price' => $this->total_price,
            'date' => $this->date,
            'notes' => $this->notes,
            'prepared_by' => ($cook) ? $cook->name : null,
            'delivered_by' => ($deliverman) ? $deliverman->name : null,
            'items' => OrderItemResource::collection($this->items)
		];
    }
}
