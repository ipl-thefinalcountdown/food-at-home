<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Customer;

class CustomerController extends Controller
{
	public function orders(Request $request)
	{
		$cur_user = $request->user();

		if($cur_user->type == 'C')
			return Customer::where('customers.id', $cur_user->id)
				->join('users', 'customers.id', '=', 'users.id')
				->select('*')
				->first();
		else
			return $cur_user;
	}
}
