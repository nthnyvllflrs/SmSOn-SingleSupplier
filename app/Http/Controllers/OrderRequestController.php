<?php

namespace App\Http\Controllers;

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
        } elseif ($request->user()->role == 'Supplier') {
            $order_requests = OrderRequest::where([
                ['supplier_id', $request->user()->information->id], 
                ['status', $request->status]
            ])->orderBy('created_at', 'desc')->get();
        }
        
        $data = [];
        foreach($order_requests as $order_request) {
            $data[] = [
                'id' => $order_request->id,
                'code' => $order_request->code,
                'customer' => $order_request->customer->name,
                'supplier' => $order_request->supplier->name,
                'datetime' => date('d-m-Y H:m:s', strtotime($order_request->created_at)),
            ];
        }

        return response(['success' => ['order_requests' => $data]], 200);
    }

    public function receivables(Request $request) {
        $receivable_list = \DB::table('order_requests')
                            ->join('suppliers', 'suppliers.id', 'order_requests.supplier_id')
                            ->join('manifest_details', 'manifest_details.order_request_id', 'order_requests.id')
                            ->join('manifests', 'manifests.id', 'manifest_details.manifest_id')
                            ->join('logistics', 'logistics.id', 'manifests.logistic_id')
                            ->where('order_requests.customer_id', $request->user()->information->id)
                            ->select('order_requests.id', 'order_requests.code', 'manifests.delivery_date', 
                                    'suppliers.name as supplier', 'logistics.name as logistic')->get();
        return response(['success' => ['order_requests' => $receivable_list]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            '*.supplier_id' => 'required|exists:suppliers,id',
            '*.product_id' => 'required|exists:products,id',
            '*.quantity' => 'required|numeric|min:1',
            '*.total' => 'required|numeric|min:1',
        ]);

        if($validator->fails()) { return response(['errors' => $validator->errors()], 422);}

        $filtered_order_requests = $this->filter_order_requests($request->toArray());

        $order_requests_code = [];
        foreach($filtered_order_requests as $supplier => $order_requests) {
            $new_order_request = OrderRequest::create(['customer_id' => $request->user()->information->id, 'supplier_id' => $supplier]);

            foreach($order_requests as $order_request) {
                OrderRequestDetail::create(array_merge((array) $order_request, ['order_request_id' => $new_order_request->id]));
            }
            $order_requests_code[] = $new_order_request->code;

            $supplier = \App\Supplier::find($supplier);
            $supplier->user->notify(new \App\Notifications\OrderRequestCreationNotification($new_order_request));
        }

        return response(['success' => ['order_request_codes' => $order_requests_code]], 200);
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
                'supplier' => $order_request->supplier->name,
                'total' => $total,
                'datetime' => date('d-m-Y H:m:s', strtotime($order_request->created_at)),
                'details' => $details,
                ]
            ]
        ], 200);
    }

    public function destroy(Request $request, OrderRequest $order_request) {
        $order_request->delete();
        return response(['success' => ['message' => 'Order Request Deleted']], 201);
    }

    public function update_status(Request $request, OrderRequest $order_request) {
        $order_request->update($request->toArray());
        $order_request->customer->user->notify(new \App\Notifications\OrderRequestStatusNotification($order_request));
        return response(['success' => ['message' => $order_request->code." Status Updated"]], 200);
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