<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Request;

use Illuminate\Support\Facades\Validator;

class SMSController extends Controller
{
    public function webhook(Request $request) {
        return response('Webhook Successful!', 200);
    }

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

    public function sms_webhook(Request $request) {
        try {
            // $user_check = \App\UserInformation::join('users', 'users.id', 'user_information.user_id')
            //                                     ->join('user_types', 'user_types.id', 'users.user_type')
            //                                     ->where([['user_information.phone_number', $request->phone_number], ['user_types.name', 'Customer']])->exists();
            $customer = \App\Customer::where('contact_number', $request->phone_number)->exists();
            if(!$customer) { return response('Phone number not tied to any registered customer. Please use a registered phone number. Thank you.');}

            $sms_request = explode(' ', $request->body);
            if(strtoupper($sms_request[0]) == 'PRODUCTLIST') {
                return $this->product_list();
            } else if (strtoupper($sms_request[0]) == 'ORDER'){
                return $this->order_request($sms_request, $request->phone_number);
            } else if (strtoupper($sms_request[0]) == 'CANCEL'){
                return $this->cancel_request($sms_request, $request->phone_number);
            } else {
                return response('You have entered an invalid keyword. Please make sure your keyword is correct. Thank you.');
            }
        } catch (\Exception $e) {
            return response('You have entered an invalid keyword. Please make sure your keyword is correct. Thank you.');
        }
    }

    private function order_request($sms_request, $customer_phone_number) {
        if(count(array_slice($sms_request, 1)) != 0) {
            try {
                $orders = [];
                foreach(array_slice($sms_request, 1) as $order) {
                    $order_ = explode('_', $order);
                    $product = \App\Product::where('code', $order_[0])->first();
                    $quantity = is_numeric($order_[1]);

                    if(!$product) { 
                        // return response($order_[0].' .A non-existing product code was detected. Please double check order code and then try again. Thank you.');
                        return reply_to_customer(
                            $customer_phone_number, 
                            $order_[0].' .A non-existing product code was detected. Please double check order code and then try again. Thank you.'
                        );
                    }

                    if(!$quantity) { 
                        // return response($order_[1].' .Incorrect quantity format. Please double check order quantity and then try again. Thank you.');
                        return reply_to_customer(
                            $customer_phone_number, 
                            $order_[1].' .Incorrect quantity format. Please double check order quantity and then try again. Thank you.'
                        );
                    }

                    if($quantity > $product->stock->available) { 
                        // return response($order_[0].' .Quantity exceeds stock availability. Please double check order quantity and then try again. Thank you.');
                        return reply_to_customer(
                            $customer_phone_number, 
                            $order_[0].' .Quantity exceeds stock availability. Please double check order quantity and then try again. Thank you.'
                        );
                    }

                    $orders[] = [ 'product_id' => $product->id, 'quantity' => (integer) $order_[1],];
                }

                $customer = \App\Customer::where('contact_number', $customer_phone_number)->first();

                $new_order_request = \App\OrderRequest::create(['customer_id' => $customer->id]);
                foreach ($orders as $order) {
                    if($order['quantity'] > 0) {
                        $product_ = \App\Product::find($order['product_id']);

                        \App\OrderRequestDetail::create([
                            'order_request_id' => $new_order_request->id, 
                            'product_id' => $order['product_id'], 
                            'quantity' => $order['quantity'],
                            'total' => $order['quantity'] * $product_->price,
                        ]);

                        $product_->stock->pending = (integer) $product_->stock->pending + (integer) $order['quantity'];
                        $product_->stock->save();
                    }
                }

                \Notification::send([\App\User::find(1)], new \App\Notifications\OrderRequestCreationNotification($new_order_request));
                event(new \App\Events\OrderRequest([
                    'user_id' => \App\User::find(1)->id, 'code' => $new_order_request->code, 'type' => 'Created'
                ]));
                
                \App\SystemLog::create([
                    'type' => 'Order Request',
                    'remarks' => $new_order_request->code." Created."
                ]);

                // return response('Order Request Successful. ('.$new_order_request->code.') Order Request is now on pending status. A text message will be sent if your order request was approved. Thank you.');
                return reply_to_customer(
                    $customer_phone_number, 
                    'Order Request Successful. ('.$new_order_request->code.') Order Request is now on pending status. A text message will be sent if your order request was approved. Thank you.'
                );
            } catch (\Exception $e) {
                // return response('You have entered an invalid keyword. Please make sure your keyword is correct. Thank you.!!!!');
                return reply_to_customer(
                    $customer_phone_number, 
                    'You have entered an invalid keyword. Please make sure your keyword is correct. Thank you.!!!!'
                );
            }
        } else {
            // return response('Order Request empty. Please add your order request. E.g. ORDER PROD01_100 PROD02_200. Thank you.');
            return reply_to_customer(
                $customer_phone_number, 
                'Order Request empty. Please add your order request. E.g. ORDER PROD01_100 PROD02_200. Thank you.'
            );
        }
    }

    private function cancel_request($sms_request, $phone_number) {
        if(count(array_slice($sms_request, 1)) != 0) {
            try {
                $customer = \App\Customer::where('contact_number', $phone_number)->first();
                $order_request = \App\OrderRequest::where([['code', $sms_request[1]], ['customer_id', $customer->id]])->exists();
                if(!$order_request) { 
                    // return response('Request code non-existing. Please add your correct request code. E.g. CANCEL R9999-99999. Only pending request are cancelable. Thank you.');
                    return reply_to_customer(
                        $phone_number, 
                        'Request code non-existing. Please add your correct request code. E.g. CANCEL R9999-99999. Only pending request are cancelable. Thank you.'
                    );
                }

                $order_request = \App\OrderRequest::where('code', $sms_request[1])->first();
                if($order_request->status == 'Pending') {

                    foreach($order_request->details as $order_request_detail) {
                        $stock = $order_request_detail->product->stock;
                        $stock->pending = $stock->pending - $order_request_detail->quantity;
                        $stock->save();    
                    }

                    $order_request->delete();

                    \Notification::send([\App\User::find(1)], new \App\Notifications\OrderRequestDeletionNotification($order_request));
                    event(new \App\Events\OrderRequest([
                        'user_id' => \App\User::find(1)->id, 'code' => $order_request->code, 'type' => 'Deleted'
                    ]));
                    
                    \App\SystemLog::create([
                        'type' => 'Order Request',
                        'remarks' => $order_request->code." Deleted."
                    ]);

                    // return response('('.$order_request->code.') Request successfuly canceled. Thank you.');
                    return reply_to_customer(
                        $phone_number, 
                        '('.$order_request->code.') Request successfuly canceled. Thank you.'
                    );
                } else {
                    // return response('Request cannot be canceled. Request status already '.$order_request->status.'. For more information, contact 999-9999 / 09999999999. Thank you.');
                    return reply_to_customer(
                        $phone_number, 
                        'Request cannot be canceled. Request status already '.$order_request->status.'. For more information, contact 999-9999 / 09999999999. Thank you.'
                    );
                }
            } catch (\Exception $e) {
                // return response('You have entered an invalid keyword. Please make sure your keyword is correct. Thank you.');
                return reply_to_customer(
                    $phone_number, 
                    'You have entered an invalid keyword. Please make sure your keyword is correct. Thank you.'
                );
            }
        } else {
            // return response('Request code empty. Please add your request code. E.g. CANCEL R9999-99999. Only pending request are cancelable. Thank you.');
            return reply_to_customer(
                $phone_number, 
                'Request code empty. Please add your request code. E.g. CANCEL R9999-99999. Only pending request are cancelable. Thank you.'
            );
        }
    }

    private function product_list() {
        $product_list = \App\Product::all();
        $response = 'Product Code List\n';
        foreach ($product_list as $product) {
            $response = $response.$product->code.'\n';
        }
        $response = $response.'To order send ORDER PRODUCTCODE_QUANTITY. E.g. ORDER PROD01_100 PROD02_200';

        $timesToSend = ceil(strlen($response) / 150);
        
        $startPosition = 0; $lenghtToCut = 150;
        while($timesToSend > 0) {
            reply_to_customer($phone_number, substr($response, $startPosition, $lenghtToCut));
            $startPosition = $lenghtToCut;
            $lenghtToCut*=2;
            $timesToSend-=1;
        }
    }

    public function itextmo_webhook(Request $request) {
        try {

            $request = $request->toArray();
            $originator = $request['msisdn']; 
            $gateway = $request['to']; 
            $message = $request['text']; 
            $timestamp = $request['timestamp']; 

            \App\ITextMoIncomingSMS::create([
                'originator' => $originator,
                'gateway' => $gateway,
                'message' => $message,
                'timestamp' => $timestamp,
            ]);

            if(!(isEmptyOrNullString($originator)) && !(isEmptyOrNullString($message))) {
                $this->sms_filter($originator, $message);
            }
            
            echo "SUCCESS";
        
        } catch (\Exception $error) {
            report($error);
            echo "ERROR";
        }
    }

    public function sms_filter($customer_phone_number, $message) {

        // Check if contact number exist
        $customer = \App\Customer::where('contact_number', $customer_phone_number)->exists();
        if(!$customer) { 
            return reply_to_customer(
                $customer_phone_number, 
                'Phone number not tied to any registered customer. Please use a registered phone number. Thank you.'
            );
        }

        $sms_request = explode(' ', $message);
        if(strtoupper($sms_request[0]) == 'PRODUCTLIST') {
            return $this->product_list();
        } else if (strtoupper($sms_request[0]) == 'ORDER'){
            return $this->order_request($sms_request, $request->phone_number);
        } else if (strtoupper($sms_request[0]) == 'CANCEL'){
            return $this->cancel_request($sms_request, $request->phone_number);
        } else {
            return reply_to_customer(
                $customer_phone_number, 
                'You have entered an invalid keyword. Please make sure your keyword is correct. Thank you.'
            );
        }
    }

    public function reply_to_customer($customer_phone_number, $message) {
        $result = iTextMo($customer_phone_number, $message);

        if ($result == ""){
            \App\SystemLog::create([ 'type' => 'SMS', 'remarks' => "No response from itextmo server." ]);
        } else if ($result == 0){
            \App\SystemLog::create([ 'type' => 'SMS', 'remarks' => $request->phone_number." Sent." ]);
        } else {
            \App\SystemLog::create([ 'type' => 'SMS', 'remarks' => "Error Num ". $result . " was encountered!" ]);
        }
    }
}