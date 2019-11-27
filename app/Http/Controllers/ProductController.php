<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Product;

class ProductController extends Controller
{
    public function index() {
        return response(['success' => ['products' => Product::all()]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // Product Model Data
            'supplier_id' => 'required|exists:suppliers,id',
            'code' => 'required|string|unique:products,code',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}
        $product = Product::create($request->toArray());
        return response(['success' => ['message' => 'New Product Created']], 200);
    }

    public function show(Request $request, Product $product) {
        return response(['success' => ['product' => $product]], 200);
    }

    public function update(Request $request, Product $product) {
        $validator = Validator::make($request->all(), [
            // Product Model Data
            'code' => 'required|string|unique:products,code',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}
        $product->update($request->toArray());
        return response(['success' => ['message' => 'Product Updated']], 200);
    }

    public function destroy(Request $request, Product $product) {
        $product->delete();
        return response(['success' => ['message' => 'product Deleted']], 201);
    }
}
