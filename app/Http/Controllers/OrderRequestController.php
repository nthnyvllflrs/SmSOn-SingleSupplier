<?php

namespace App\Http\Controllers;

use Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\OrderRequest;
use App\OrderRequestDetail;

class OrderRequestController extends Controller
{
    public function index(Request $request) {
        $validator = Validator::make($request->all(),  [
            'status' => 'required|in:Disapproved,Pending,Approved,Delivered'
        ]);
        if($validator->fails()) { return response(['errors' => $validator->errors()], 422);}

        if($request->user()->role == 'Customer') {
            $order_requests = OrderRequest::where([
                ['customer_id', $request->user()->information->id], 
                ['status', $request->status]
            ])->orderBy('created_at', 'desc')->get();
        // } elseif ($request->user()->role == 'Supplier') {
        //     $order_requests = OrderRequest::where([
        //         ['supplier_id', $request->user()->information->id], 
        //         ['status', $request->status]
        //     ])->orderBy('created_at', 'desc')->get();
        } elseif ($request->user()->role == 'Administrator') {
            $order_requests = OrderRequest::where([
                ['status', $request->status]
            ])->orderBy('created_at', 'desc')->get();
        }
        
        $data = [];
        foreach($order_requests as $order_request) {
            $data[] = [
                'id' => $order_request->id,
                'code' => $order_request->code,
                'customer' => $order_request->customer->name,
                // 'supplier' => $order_request->supplier->name,
                'datetime' => date('d-m-Y H:m:s', strtotime($order_request->created_at)),
            ];
        }

        return response(['success' => ['order_requests' => $data]], 200);
    }

    public function receivables(Request $request) {
        $receivable_list = \DB::table('order_requests')
                            // ->join('suppliers', 'suppliers.id', 'order_requests.supplier_id')
                            ->join('manifest_details', 'manifest_details.order_request_id', 'order_requests.id')
                            ->join('manifests', 'manifests.id', 'manifest_details.manifest_id')
                            ->join('logistics', 'logistics.id', 'manifests.logistic_id')
                            ->where('order_requests.customer_id', $request->user()->information->id)
                            ->where('order_requests.status', 'Approved')
                            ->select('order_requests.id', 'order_requests.code', 'manifests.delivery_date', 
                                    // 'suppliers.name as supplier', 
                                    'logistics.name as logistic', 
                                    'logistics.code as logistic_code', 'logistics.latitude', 'logistics.longitude')->get();
        return response(['success' => ['order_requests' => $receivable_list]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            // '*.supplier_id' => 'required|exists:suppliers,id',
            '*.product_id' => 'required|exists:products,id',
            '*.quantity' => 'required|numeric|min:1',
            '*.total' => 'required|numeric|min:1',
        ]);

        if($validator->fails()) { return response(['errors' => $validator->errors()], 422);}

        $new_order_request = OrderRequest::create(['customer_id' => $request->user()->information->id]);
        foreach($request->toArray() as $product) {
            OrderRequestDetail::create(array_merge((array) $product, ['order_request_id' => $new_order_request->id]));

            $product_ = \App\Product::find($product['product_id']);
            $product_->stock->pending = (integer) $product_->stock->pending + (integer) $product['quantity'];
            $product_->stock->save();
        }
        // $filtered_order_requests = $this->filter_order_requests($request->toArray());

        // $order_requests_code = [];
        // foreach($filtered_order_requests as $supplier => $order_requests) {
        //     $new_order_request = OrderRequest::create(['customer_id' => $request->user()->information->id, 'supplier_id' => $supplier]);

        //     foreach($order_requests as $order_request) {
        //         OrderRequestDetail::create(array_merge((array) $order_request, ['order_request_id' => $new_order_request->id]));
        //     }
        //     $order_requests_code[] = $new_order_request->code;

        //     $supplier = \App\Supplier::find($supplier);
        //     $supplier->user->notify(new \App\Notifications\OrderRequestCreationNotification($new_order_request));
        //     event(new \App\Events\OrderRequest([
        //         'user_id' => $supplier->user->id, 'code' => $new_order_request->code, 'type' => 'Created'
        //     ]));
        // }

        Notification::send([\App\User::find(1)], new \App\Notifications\OrderRequestCreationNotification($new_order_request));
        event(new \App\Events\OrderRequest([
            'user_id' => \App\User::find(1)->id, 'code' => $new_order_request->code, 'type' => 'Created'
        ]));

        \App\SystemLog::create([
            'type' => 'Order Request',
            'remarks' => $new_order_request->code." Created."
        ]);

        return response(['success' => ['order_request_code' => $new_order_request->code]], 200);
    }

    public function show(Request $request, OrderRequest $order_request) {
        $total = 0; $details = [];
        foreach($order_request->details as $order) {
            $details[] = [
                'id' => $order->id,
                'code' => $order->product->code,
                'name' => $order->product->name,
                'unit' => $order->product->unit,
                'quantity' => $order->quantity,
                'total' => $order->total,
            ];
            $total += (double) $order->total;
        }

        return response(['success' => [
            'order_request_information' => [
                // 'supplier' => $order_request->supplier->name,
                'total' => $total,
                'datetime' => date('d-m-Y H:m:s', strtotime($order_request->created_at)),
                'details' => $details,
                ]
            ]
        ], 200);
    }

    public function destroy(Request $request, OrderRequest $order_request) {
        Notification::send([\App\User::find(1)], new \App\Notifications\OrderRequestDeletionNotification($order_request));
        event(new \App\Events\OrderRequest([
            'user_id' => \App\User::find(1)->id, 'code' => $order_request->code, 'type' => 'Deleted'
        ]));

        \App\SystemLog::create([
            'type' => 'Order Request',
            'remarks' => $order_request->code." Deleted."
        ]);

        foreach($order_request->details as $order_request_detail) {
            $stock = $order_request_detail->product->stock;
            $stock->pending = $stock->pending - $order_request_detail->quantity;
            $stock->save();    
        }

        $order_request->delete();
        // event(new \App\Events\OrderRequest([
        //     'user_id' => $supplier->user()->id, 'code' => $new_order_request->code, 'type' => 'Deleted'
        // ]));
        return response(['success' => ['message' => 'Order Request Deleted']], 201);
    }

    public function update_status(Request $request, OrderRequest $order_request) {
        $previous_status = $order_request->status;
        $order_request->update($request->toArray());
        $order_request->customer->user->notify(new \App\Notifications\OrderRequestStatusNotification($order_request));
        event(new \App\Events\OrderRequestStatus([
            'user_id' => $order_request->customer->user->id, 'code' => $order_request->code, 'status' => $order_request->status
        ]));

        \App\SystemLog::create([
            'type' => 'Order Request',
            'remarks' => $order_request->code." Updated Status."
        ]);

        if($request->status == 'Delivered') {
            foreach($order_request->details as $order_request_detail) {
                $stock = $order_request_detail->product->stock;
                $stock->delivered = $stock->delivered + $order_request_detail->quantity;
                $stock->approved = $stock->approved - $order_request_detail->quantity;
                $stock->save();    
            }

            $customer_phone_number = $order_request->customer->contact_number;
            $message = "Your Order Request with ORID ".$order_request->code." has been delivered.";
            $result = iTextMo($customer_phone_number, $message);

            if ($result == ""){
                return response(['msg' => 'iTexMo: No response from server!!!
                Please check the METHOD used (CURL or CURL-LESS). If you are using CURL then try CURL-LESS and vice versa.
                Please CONTACT US for help.'], 200);
            } else if ($result == 0){
                \App\SystemLog::create([
                    'type' => 'SMS',
                    'remarks' => $request->phone_number." Sent."
                ]);
            } else {
                return response(['msg' => "Error Num ". $result . " was encountered!"], 400);
            }
            
        } else if($request->status == 'Approved') {
            foreach($order_request->details as $order_request_detail) {
                $stock = $order_request_detail->product->stock;
                $stock->approved = $stock->approved + $order_request_detail->quantity;

                if($previous_status == 'Delivered') { 
                    $stock->delivered = $stock->delivered - $order_request_detail->quantity;
                } else if($previous_status == 'Pending') {
                    $stock->pending = $stock->pending - $order_request_detail->quantity;
                    $stock->available = $stock->available - $order_request_detail->quantity;
                }

                $stock->save();    

                if($stock->stock_status == 'Critical') {
                    Notification::send([\App\User::find(1)], new \App\Notifications\StockCriticalNotification($stock));
                    event(new \App\Events\StockStatus([
                        'user_id' => \App\User::find(1)->id,
                        'code' => $stock->product->code,
                        'status' => $stock->stock_status
                    ]));
                }
            }
        } else if($request->status == 'Disapproved') {
            foreach($order_request->details as $order_request_detail) {
                $stock = $order_request_detail->product->stock;
                $stock->pending = $stock->pending - $order_request_detail->quantity;
                $stock->save();    
            }
        } else if($request->status == 'Pending') {
            foreach($order_request->details as $order_request_detail) {
                $stock = $order_request_detail->product->stock;
                $stock->pending = $stock->pending + $order_request_detail->quantity;

                if($previous_status == 'Approved') { 
                    $stock->approved = $stock->approved - $order_request_detail->quantity;
                    $stock->available = $stock->available + $order_request_detail->quantity;
                }
                
                $stock->save();    
            }
        }

        return response(['success' => ['message' => $order_request->code." Status Updated"]], 200);
    }

    public function search_order_request(Request $request) {
        $validator = Validator::make($request->all(), [
            'filter' => 'required|string',
        ]);

        if($validator->fails()) { return response(['errors' => $validator->errors()], 422);}

        return response(['success' => ['result' => []]], 200);
    }

    private function filter_order_requests($order_requests) {
        $suppliers = array_column($order_requests, 'supplier_id');
        $suppliers = array_unique(array_column($order_requests, 'supplier_id'));

        $filtered_order_requests = [];
        foreach($suppliers as $supplier) {
           $supplier_order_requests = [];
           foreach($order_requests as $order_request) {
               if($order_request['supplier_id'] == $supplier) {
                    $supplier_order_requests[] = $order_request;
               }
           }
           $filtered_order_requests[$supplier] = $supplier_order_requests;
        }
        return $filtered_order_requests;
    }
}