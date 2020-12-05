<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('products');

        // filter by type
        if($request->has('type'))
            $query = $query->where('type', $request->input('type'));

        // filter by name
        if($request->has('name'))
            $query = $query->where('name', 'like', '%' . $request->input('name') . '%');

        return ProductResource::collection(
            $request->has('page')
                ? $query->paginate()
                : $query->get()
        );
    }

    public function view(Product $product)
    {
        return new ProductResource($product);
    }
}
