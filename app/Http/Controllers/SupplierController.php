<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Supplier;
use App\Logistic;
use App\OrderRequest;

class SupplierController extends Controller
{
    public function index() {
        $_suppliers = Supplier::all();
        $suppliers = [];
        foreach($_suppliers as $supplier) {
           $suppliers[] = [
               'id' => $supplier->user->id,
               'code' => $supplier->code,
               'username' => $supplier->user->username,
               'name' => $supplier->name,
               'description' => $supplier->description,
               'address' => $supplier->address,
               'contact_number' => $supplier->contact_number,
           ];
        }
        return response(['success' => ['suppliers' => $suppliers]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|unique:users,username',
            'password' => 'required|string|min:8|confirmed',

            // Supplier Model Data
            'name' => 'required|min:8|max:255',
            'description' => 'required|max:500',
            'address' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:suppliers,contact_number',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        $user = User::create(array_merge($request->toArray(), ['role' => 'Supplier']));
        $supplier = supplier::create(array_merge($request->toArray(), ['user_id' => $user->id]));

        return response(['success' => ['user' => $user, 'supplier' => $supplier]], 200);
    }

    public function show(Request $request, User $supplier) {
        $supplier = [
            'name' => $supplier->information->name,
            'description' => $supplier->information->description,
            'address' => $supplier->information->address,
            'product_count' => count($supplier->information->products),
            'logistic_count' => count($supplier->information->logistics),
            'products' => $supplier->information->products,
        ];
        return response(['success' => ['supplier' => $supplier]], 200);
    }

    public function update(Request $request, User $supplier) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|unique:users,username,'.$supplier->id,
            'password' => 'nullable|string|min:8|confirmed',

            // supplier Model Data
            'name' => 'required|min:8|max:255',
            'description' => 'required|max:500',
            'address' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:suppliers,contact_number,'.$supplier->information->id,
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        if(empty($request->password) || ($request->password == null)) { $updated_data = $request->except('password');
        } else { $updated_data = $request->all();}

        $supplier->update($updated_data);
        $supplier->information->update($updated_data);

        return response(['success' => ['user' => $supplier, 'supplier' => $supplier->information]], 200);
    }

    public function destroy(Request $request, User $supplier) {
        $supplier->delete();
        return response(['success' => ['message' => 'Supplier Deleted']], 201);
    }

    public function supplier_logistics(Request $request) {
        $supplier_logistics = Logistic::where('supplier_id', $request->user()->information->id)->get();
        return response(['success' => ['logistics' => $supplier_logistics]], 200);
    }

    public function supplier_order_requests(Request $request) {
        $supplier_order_requests = OrderRequest::where([['supplier_id', $request->user()->information->id], ['status', 'Approved']])->get();
        $order_requests = [];

        foreach($supplier_order_requests as $order_request) {
            $order_requests[] = [
                'id' => $order_request->id,
                'code' => $order_request->code,
                'customer' => $order_request->customer->name,
                'address' => $order_request->customer->address,
            ];
        }

        return response(['success' => ['order_requests' => $order_requests]], 200);
    }
}
