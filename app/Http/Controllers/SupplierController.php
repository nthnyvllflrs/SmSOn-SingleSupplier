<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Supplier;

class SupplierController extends Controller
{
    public function index() {
        return response(['success' => ['suppliers' => Supplier::all()]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|max:16|unique:users,username',
            'password' => 'required|string|min:8|confirmed',

            // Supplier Model Data
            'name' => 'required|min:8|max:255',
            'address' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:suppliers,contact_number',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        $supplier = User::create(array_merge($request->toArray(), ['role' => 'Supplier']));
        $supplier = supplier::create(array_merge($request->toArray(), ['user_id' => $supplier->id]));

        return response(['success' => ['message' => 'New Supplier Created']], 200);
    }

    public function show(Request $request, User $supplier) {
        return response(['success' => ['user' => $supplier, 'supplier' => $supplier->information]], 200);
    }

    public function update(Request $request, User $supplier) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|max:16|unique:users,username,'.$supplier->id,
            'password' => 'nullable|string|min:8|confirmed',

            // supplier Model Data
            'name' => 'required|min:8|max:255',
            'address' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:suppliers,contact_number,'.$supplier->information->id,
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        if(empty($request->password) || ($request->password == null)) { $updated_data = $request->except('password');
        } else { $updated_data = $request->all();}

        $supplier->update($updated_data);
        $supplier->information->update($updated_data);

        return response(['success' => ['message' => 'Supplier Updated']], 200);
    }

    public function destroy(Request $request, User $supplier) {
        $supplier->delete();
        return response(['success' => ['message' => 'Supplier Deleted']], 200);
    }
}
