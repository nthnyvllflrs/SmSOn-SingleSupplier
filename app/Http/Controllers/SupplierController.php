<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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
            'contact_number' => 'required|max:12|unique:suppliers,contact_number',

            'image' => 'required',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        // Image Upload
        \Cloudder::upload($request->image, null);
        $response = \Cloudder::getResult();

        $user = User::create(array_merge($request->toArray(), ['role' => 'Supplier']));
        $supplier = supplier::create(array_merge($request->toArray(), ['user_id' => $user->id, 'image_url' => $response['secure_url']]));

        Notification::send([User::find(1)], new \App\Notifications\UserCreationNotification($user));

        \App\SystemLog::create([
            'type' => 'Supplier',
            'remarks' => $user->code." Created."
        ]);

        return response(['success' => ['image' => \Cloudder::getPublicId(), 'user' => $user, 'supplier' => $supplier]], 200);
    }

    public function show(Request $request, User $supplier) {
        $supplier = [
            'profile' => [
                'code' => $supplier->code, 'username' => $supplier->username,
                'name' => $supplier->information->name, 'contact_number' => $supplier->information->contact_number,
                'address' => $supplier->information->address, 'description' => $supplier->information->description,
                'image_url' => $supplier->information->image_url,
            ],

            'name' => $supplier->information->name, 'description' => $supplier->information->description, 'image_url' => $supplier->information->image_url,
            'address' => $supplier->information->address, 'product_count' => count($supplier->information->products),
            'logistic_count' => count($supplier->information->logistics), 'products' => $supplier->information->products,
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
            'contact_number' => 'required|max:12|unique:suppliers,contact_number,'.$supplier->information->id,
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        if(empty($request->password) || ($request->password == null)) { $updated_data = $request->except('password');
        } else { $updated_data = $request->all();}

        if(!empty($request->image) || ($request->image != null)) { 
            // Image Upload
            \Cloudder::upload($request->image, null);
            $response = \Cloudder::getResult();
            $updated_data = array_merge($updated_data, ['image_url' => $response['secure_url']]);
        }

        $supplier->update($updated_data);
        $supplier->information->update($updated_data);

        \App\SystemLog::create([
            'type' => 'Supplier',
            'remarks' => $supplier->code." Updated."
        ]);

        return response(['success' => ['user' => $supplier, 'supplier' => $supplier->information]], 200);
    }

    public function destroy(Request $request, User $supplier) {
        \App\SystemLog::create([
            'type' => 'Supplier',
            'remarks' => $supplier->code." Deleted."
        ]);

        Notification::send([User::find(1)], new \App\Notifications\UserDeletionNotification($supplier));
        $supplier->delete();
        return response(['success' => ['message' => 'Supplier Deleted']], 201);
    }
}
