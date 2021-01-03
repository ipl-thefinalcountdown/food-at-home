<?php

namespace App\Http\Helpers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class OrdersQueue
{
    public static function removeAssignedOrders(User $user)
    {
        $user->available_at = Carbon::now();

        Order::where('status', 'P')
            ->where('prepared_by', $user->id)
            ->update([
                'prepared_by' => null,
                'status' => 'H',
                'current_status_at' => Carbon::now(),
            ]);
    }

    public static function reassignOrders()
    {
        $users = User::where('type', 'EC')
            ->where('logged_at', '<>', null)
            ->leftJoin('orders', function ($join) {
                $join->on('users.id', '=', 'orders.prepared_by')
                     ->where('orders.status', 'P');
            })
            ->whereNull('orders.prepared_by')
            ->orderBy('users.available_at', 'ASC')
            ->select('users.id')
            ->get();

        $orders = Order::where('status', 'H')
            ->orderBy('opened_at', 'ASC')
            ->limit($users->count())
            ->get();

        foreach($orders as $order)
        {
            $user = $users->pop();
            $user->available_at = null;
            $order->prepared_by = $user->id;
            $order->status = 'P';
            $order->current_status_at = Carbon::now();
            $order->save();
        }
    }
}
