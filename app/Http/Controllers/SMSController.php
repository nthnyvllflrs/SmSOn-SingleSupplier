<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SMSController extends Controller
{
    public function send_sms(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) { return response(['errors'=>$validator->errors()], 422);}

        $result = $this->itexmo($request->phone_number, $request->message, "TR-AL-FA668030_IKY9F");
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
        } else{
            return response(['msg' => "Error Num ". $result . " was encountered!"], 400);
        }
    }

    function itexmo($number, $message, $apicode){
        $url = 'https://www.itexmo.com/php_api/api.php';
        $itexmo = array('1' => $number, '2' => $message, '3' => $apicode);
        $param = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($itexmo),
            ),
        );
        $context  = stream_context_create($param);
        return file_get_contents($url, false, $context);
    }
}
