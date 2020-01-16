<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SMSController extends Controller
{
    public function send_sms(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        $result = iTextMo($request->phone_number, $request->message);
        if ($result == ""){
            return response(['msg' => 'iTexMo: No response from server!!!
            Please check the METHOD used (CURL or CURL-LESS). If you are using CURL then try CURL-LESS and vice versa.
            Please CONTACT US for help.'], 200);
        } else if ($result == 0){
            return response(['msg' => 'Message Sent!'], 200);

            \App\SystemLog::create([
                'type' => 'SMS',
                'remarks' => $request->phone_number." Sent."
            ]);
        } else {
            return response(['msg' => "Error Num ". $result . " was encountered!"], 400);
        }
    }
}
