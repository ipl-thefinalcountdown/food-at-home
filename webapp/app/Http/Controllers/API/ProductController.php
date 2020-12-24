<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('products')->where('deleted_at', null);

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

    public function delete(Product $product)
    {
        $product->delete();
    }

    public function put(Request $request, Product $product)
    {
        $this->validate($request, [
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'price' => 'required|numeric',
            'type' => 'in:drink,dessert,hot dish,cold dish',
            'description' => 'required'
            ]);

        if ($request->hasFile('photo_url'))
        {
            $path = 'public/products/'.$product->photo_url;
            if (file_exists(storage_path('app/'.$path))) Storage::delete($path);

            $path = $request->photo_url->store('/public/products');
            $product->photo_url = basename($path);
        }

        $product->fill($request->only('name', 'price', 'type', 'description'))->save();
    }

    public function post(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'price' => 'required|numeric',
            'type' => 'in:drink,dessert,hot dish,cold dish',
            'description' => 'required',
            'photo_url' => 'file'
            ]);

        $path = $request->photo_url->store('/public/products');
        $product = new Product();
        $product->fill($request->only('name', 'price', 'type', 'description'));
        $product->photo_url = basename($path);
        $product->save();
    }
}
