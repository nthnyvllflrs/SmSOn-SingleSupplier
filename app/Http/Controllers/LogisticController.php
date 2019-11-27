<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\User;
use App\Logistic;

class LogisticController extends Controller
{
    public function index() {
        return response(['success' => ['logistics' => Logistic::all()]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|max:16|unique:users,username',
            'password' => 'required|string|min:8|confirmed',

            // logistic Model Data
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:logistics,contact_number',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        $logistic = User::create(array_merge($request->toArray(), ['role' => 'Logistic']));
        $logistic = logistic::create(array_merge($request->toArray(), ['user_id' => $logistic->id]));

        return response(['success' => ['message' => 'New Logistic Created']], 200);
    }

    public function show(Request $request, User $logistic) {
        return response(['success' => ['user' => $logistic, 'logistic' => $logistic->information]], 200);
    }

    public function update(Request $request, User $logistic) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|max:16|unique:users,username,'.$logistic->id,
            'password' => 'nullable|string|min:8|confirmed',

            // logistic Model Data
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|min:8|max:255',
            'contact_number' => 'required|max:11|unique:logistics,contact_number,'.$logistic->information->id,
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        if(empty($request->password) || ($request->password == null)) { $updated_data = $request->except('password');
        } else { $updated_data = $request->all();}

        $logistic->update($updated_data);
        $logistic->information->update($updated_data);

        return response(['success' => ['message' => 'Logistic Updated']], 200);
    }

    public function destroy(Request $request, User $logistic) {
        $logistic->delete();
        return response(['success' => ['message' => 'Logistic Deleted']], 200);
    }
}
