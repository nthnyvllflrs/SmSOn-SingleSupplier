<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Customer;

class CustomerController extends Controller {
 
    public function index() {
        return response(['success' => ['customers' => Customer::all()]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|max:16|unique:users,username',
            'password' => 'required|string|min:8|confirmed',

            // Customer Model Data
            'name' => 'required|min:8|max:255',
            'address' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:customers,contact_number',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        $customer = User::create(array_merge($request->toArray(), ['role' => 'Customer']));
        $customer = Customer::create(array_merge($request->toArray(), ['user_id' => $customer->id]));

        return response(['success' => ['message' => 'New Customer Created']], 200);
    }

    public function show(Request $request, User $customer) {
        return response(['success' => ['user' => $customer, 'customer' => $customer->information]], 200);
    }

    public function update(Request $request, User $customer) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|max:16|unique:users,username,'.$customer->id,
            'password' => 'nullable|string|min:8|confirmed',

            // Customer Model Data
            'name' => 'required|min:8|max:255',
            'address' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:customers,contact_number,'.$customer->information->id,
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        if(empty($request->password) || ($request->password == null)) { $updated_data = $request->except('password');
        } else { $updated_data = $request->all();}

        $customer->update($updated_data);
        $customer->information->update($updated_data);

        return response(['success' => ['message' => 'Customer Updated']], 200);
    }

    public function destroy(Request $request, User $customer) {
        $customer->delete();
        return response(['success' => ['message' => 'Customer Deleted']], 201);
    }
}