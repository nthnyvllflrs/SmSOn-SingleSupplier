<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use App\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()){
            return response(['errors' => $validator->errors()], 422);
        }

        $user = User::where('username', $request->username)->first();
    
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['success' => [
                    'token' => $token, 
                    'role' => $user->role,
                    'user_id' => $user->id,
                    'information_id' => $user->role != 'Administrator' ? $user->information->id : null,
                    ]
                ];
                \App\SystemLog::create([
                    'type' => 'Authentication',
                    'remarks' => $user->code." Logged In."
                ]);
                return response($response, 200);
            } else {
                $response = "Password missmatch";
                return response($response, 422);
            }
    
        } else {
            $response = 'User does not exist';
            return response($response, 422);
        }
    }

    public function logout(Request $request) {
        \App\SystemLog::create([
            'type' => 'Authentication',
            'remarks' => $request->user()->username." Logged Out."
        ]);

        $token = $request->user()->token();
        $token->revoke();
    
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    }

    // Administrator Special Functions

    public function show(Request $request, User $administrator) {
        return response(['success' => [
            'profile' => [
                'code' => $administrator->code, 'username' => $administrator->username
                ]
            ]
        ], 200);
    }

    public function update(Request $request, User $administrator) {
        $validator = Validator::make($request->all(), [
            // User Model Data
            'username' => 'required|string|min:8|unique:users,username,'.$administrator->id,
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        if(empty($request->password) || ($request->password == null)) { $updated_data = $request->except('password');
        } else { $updated_data = $request->all();}

        $administrator->update($updated_data);

        \App\SystemLog::create([
            'type' => 'User Profile',
            'remarks' => $administrator->username." Updated."
        ]);

        return response(['success' => ['user' => $administrator]], 200);
    }
}
