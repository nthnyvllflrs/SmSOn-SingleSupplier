<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Product;

class ProductController extends Controller
{
    public function index(Request $request) {
        // if($request->user()->role == 'Administrator') {
        //     return response(['success' => ['products' => Product::all()]], 200);
        // // } elseif($request->user()->role == 'Supplier') {
        // //     return response(['success' => [
        // //         'products' => Product::where('supplier_id', $request->user()->information->id)->get()]
        // //     ], 200);
        // } elseif($request->user()->role == 'Customer') {
            $_products = Product::all();
            $products = [];
            foreach($_products as $product) {
                $products[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    // 'supplier_id' => $product->supplier->id,
                    // 'supplier' => $product->supplier->name,
                    'description' => $product->description,
                    'unit' => $product->unit,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'stockStatus' => $product->stock->stock_status,
                ];
            }
            return response(['success' => ['products' => $products]], 200);
        // }
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // Product Model Data
            // 'supplier_id' => 'required|exists:suppliers,id',
            'code' => 'required|string|unique:products,code',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}
        $product = Product::create($request->toArray());

        \App\SystemLog::create([
            'type' => 'Product',
            'remarks' => $product->code." Created."
        ]);
        $product->stock;
        return response(['success' => ['product' => $product]], 200);
    }

    public function show(Request $request, Product $product) {
        return response(['success' => ['product' => $product]], 200);
    }

    public function update(Request $request, Product $product) {
        $validator = Validator::make($request->all(), [
            // Product Model Data
            'code' => 'required|string|unique:products,code,'.$product->id,
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'unit' => 'required|string|max:10',
            'price' => 'required|numeric',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}
        $product->update($request->toArray());

        \App\SystemLog::create([
            'type' => 'Product',
            'remarks' => $product->code." Updated."
        ]);

        return response(['success' => ['product' => $product]], 200);
    }

    public function destroy(Request $request, Product $product) {
        \App\SystemLog::create([
            'type' => 'Product',
            'remarks' => $product->code." Deleted."
        ]);

        $product->delete();
        return response(['success' => ['message' => 'Product Deleted']], 201);
    }

    public function stock(Request $request, Product $product) {
        $validator = Validator::make($request->all(), [
            'available' => 'required|numeric|min:0',
            'threshold' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}
        $product->stock->update($request->toArray());
        return response(['success' => ['message' => 'Product Stock Updated']], 201);
    }
}
