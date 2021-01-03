<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Helpers\OrdersQueue;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderViewRequest;
use App\Http\Requests\OrderCancelRequest;
use App\Http\Requests\OrderPrepareRequest;
use App\Http\Requests\OrderPickupRequest;
use App\Http\Requests\OrderDeliverRequest;
use App\Models\OrderItem;
use App\Http\Resources\OrderResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
	public function create(OrderCreateRequest $request)
	{
		$request->validated();

		$order = new Order();
		$order->notes = $request->notes;
		$order->customer_id = $request->user()->id;
		$order->status = 'H';

		// date management
		$order->date = Carbon::now();
		$order->opened_at = $order->date;
		$order->current_status_at = $order->date;

		// updated on the next save
		$order->total_price = 0;
		$order->save();

		$total_price = 0.0;

		foreach ($request->items as $item) {
			$order_item = new OrderItem();
			$order_item->order_id = $order->id;

			$order_item->product_id = $item['id'];
			$order_item->quantity = $item['quantity'];

			$product = Product::find($item['id']);

			$order_item->unit_price = $product->price;
			$total_price += ($order_item->sub_total_price =
				$order_item->unit_price * $order_item->quantity);
			$order_item->save();
		}

		// needed to only update existing DB object
		$e_order = Order::find($order->id);
		$e_order->total_price = $total_price;
		$e_order->save();

		OrdersQueue::reassignOrders();
	}

	public function listCustomer(Request $request)
	{
		return $this->list($request, $request->user());
	}

	private function list(Request $request, User $user)
	{
		$query = Order::where('customer_id', $user->id);

        return OrderResource::collection($request->has('page')
                ? $query->paginate()
                : $query->get()
        );
    }

    public function listCook(Request $request)
    {
		return $this->listEmployee(
			$request,
			['status1' => 'H', 'status2' => 'P'],
			'prepared_by');
	}

	public function listDeliverman(Request $request)
    {
		return $this->listEmployee(
			$request,
			['status1' => 'R', 'status2' => 'T'],
			'delivered_by');
    }

	private function listEmployee(Request $request, $status, $field)
    {
		$query = Order::where('status', $status['status1'])
			->orWhere(function ($query) use ($request, $status, $field) {
				$query->where('status', $status['status2'])
					->where($field, $request->user()->id);
			})
			->orderBy('status', 'DESC')
			->orderBy('current_status_at', 'ASC');

        return OrderResource::collection($request->has('page')
                ? $query->paginate()
                : $query->get()
        );
	}

	public function listAll(Request $request)
	{
		$query = Order::orderBy('current_status_at', 'DESC')
			->orderBy('status', 'DESC');

        return OrderResource::collection($request->has('page')
                ? $query->paginate()
                : $query->get()
        );
	}

	public function view(OrderViewRequest $request, Order $order)
	{
		return OrderResource::make($order);
	}

	public function cancel(OrderCancelRequest $request, Order $order)
	{
		$order->status = 'C';
		$order->current_status_at = Carbon::now();
		$order->closed_at = $order->current_status_at;
		$order->total_time = $order->closed_at->diffInSeconds($order->opened_at);
		$order->save();

		OrdersQueue::reassignOrders();
	}

	public function prepare(OrderPrepareRequest $request, Order $order)
	{
		$user = $request->user();

		$order->status = 'R';
		$start_date = $order->current_status_at;
		$order->current_status_at = Carbon::now();
		$order->preparation_time = $order->current_status_at->diffInSeconds($start_date);
		$order->save();

		$user->available_at = Carbon::now();
		$user->save();

		OrdersQueue::reassignOrders();
	}

	public function pickup(OrderPickupRequest $request, Order $order)
	{
		$user = $request->user();

		$order->status = 'T';
		$order->current_status_at = Carbon::now();
		$order->delivered_by = $user->id;
		$order->save();
	}

	public function deliver(OrderDeliverRequest $request, Order $order)
	{
		$order->status = 'D';
		$start_date = $order->current_status_at;
		$order->current_status_at = Carbon::now();
		$order->delivery_time = $order->current_status_at->diffInSeconds($start_date);
		$order->closed_at = $order->current_status_at;
		$order->total_time = $order->closed_at->diffInSeconds($order->opened_at);
		$order->save();
	}
}
