<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Customer;

class CustomerController extends Controller {
 
    public function index() {
        $_customers = Customer::all();
        $customers = [];
        foreach($_customers as $customer) {
           $customers[] = [
               'id' => $customer->user->id,
               'code' => $customer->code,
               'username' => $customer->user->username,
               'name' => $customer->name,
               'address' => $customer->address,
               'latitude' => $customer->latitude,
               'longitude' => $customer->longitude,
               'contact_number' => $customer->contact_number,
           ];
        }
        return response(['success' => ['customers' => $customers]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|unique:users,username',
            'password' => 'required|string|min:8|confirmed',

            // Customer Model Data
            'name' => 'required|min:8|max:255',
            'address' => 'required|min:8|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'contact_number' => 'required|max:11|unique:customers,contact_number',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        $user = User::create(array_merge($request->toArray(), ['role' => 'Customer']));
        $customer = Customer::create(array_merge($request->toArray(), ['user_id' => $user->id]));

        Notification::send([User::find(1)], new \App\Notifications\UserCreationNotification($user));

        return response(['success' => ['user' => $user, 'customer' => $customer]], 200);
    }

    public function show(Request $request, User $customer) {
        return response(['success' => [
            'profile' => [
                'code' => $customer->code, 'username' => $customer->username,
                'name' => $customer->information->name, 'contact_number' => $customer->information->contact_number,
                'address' => $customer->information->address,
            ]
        ]], 200);
    }

    public function update(Request $request, User $customer) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|unique:users,username,'.$customer->id,
            'password' => 'nullable|string|min:8|confirmed',

            // Customer Model Data
            'name' => 'required|min:8|max:255',
            'address' => 'required|min:8|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'contact_number' => 'required|max:11|unique:customers,contact_number,'.$customer->information->id,
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        if(empty($request->password) || ($request->password == null)) { $updated_data = $request->except('password');
        } else { $updated_data = $request->all();}

        $customer->update($updated_data);
        $customer->information->update($updated_data);

        return response(['success' => ['user' => $customer, 'customer' => $customer]], 200);
    }

    public function destroy(Request $request, User $customer) {
        Notification::send([User::find(1)], new \App\Notifications\UserDeletionNotification($customer));
        $customer->delete();
        return response(['success' => ['message' => 'Customer Deleted']], 201);
    }
}
