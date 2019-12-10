<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Manifest;
use App\ManifestDetail;
use App\Logistic;
use App\OrderRequest;

class ManifestController extends Controller
{
    public function index(Request $request) {
        if($request->user()->role == 'Administrator') {
            $_manifests = Manifest::all();
        } elseif($request->user()->role == 'Supplier') {
            $_manifests = Manifest::where('supplier_id', $request->user()->information->id)->get();
        } elseif($request->user()->role == 'Logistic') {
            $_manifests = Manifest::where('logistic_id', $request->user()->information->id)->get();
        } else {
            $_manifests = [];
        }

        $manifests = [];
        foreach($_manifests as $manifest) {
            $order_requests = [];
            foreach($manifest->details as $manifest_detail) {
                $order_requests[] = [
                    'id' => $manifest_detail->id, 
                    'code' => $manifest_detail->orderRequest->code, 
                    'customer' => $manifest_detail->orderRequest->customer->name, 
                    'address' => $manifest_detail->orderRequest->customer->address, 
                    'latitude' => $manifest_detail->orderRequest->customer->latitude, 
                    'longitude' => $manifest_detail->orderRequest->customer->longitude, 
                ];
            }
            $manifests[] = [
                'id' => $manifest->id, 'code' => $manifest->code, 'logistic' => $manifest->logistic->name, 'logistic_id' => $manifest->logistic->id,
                'delivery_date' => $manifest->delivery_date, 'order_requests' => $order_requests
            ];
        }

        return response(['success' => ['manifests' => $manifests]], 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'logistic_id' => 'required|exists:logistics,id',
            'delivery_date' => 'required|date',
            'order_requests' => 'required|array|min:1',
            'order_requests.*.id' => 'required|exists:order_requests,id',
        ]);

        if($validator->fails()) { return response(['errors' => $validator->errors()], 422);}
        
        $manifest = Manifest::create($request->toArray());
        $order_requests = $request->order_requests;
        foreach($order_requests as $order_request) {
            ManifestDetail::create(['manifest_id' => $manifest->id, 'order_request_id' => $order_request['id']]);
        }
        $manifest->logistic;
        return response(['success' => ['manifest' => $manifest]], 200);
    }

    public function edit(Request $request, Manifest $manifest) {
        $order_requests = [];
        foreach($manifest->details as $manifest_detail) {
            $order_requests[] = [
                'id' => $manifest_detail->id, 
                'code' => $manifest_detail->orderRequest->code, 
                'customer' => $manifest_detail->orderRequest->customer->name, 
                'address' => $manifest_detail->orderRequest->customer->address, 
            ];
        }
        $manifest = [
            'id' => $manifest->id, 'supplier_id' => $request->user()->information->id, 'code' => $manifest->code, 
            'logistic' => $manifest->logistic->name, 'logistic_id' => $manifest->logistic->id,
            'delivery_date' => $manifest->delivery_date, 'order_requests' => $order_requests
        ];
        return response(['success' => ['manifest' => $manifest]], 200);
    }

    public function update(Request $request, Manifest $manifest) {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'logistic_id' => 'required|exists:logistics,id',
            'delivery_date' => 'required|date',
            'order_requests' => 'required|array|min:1',
            'order_requests.*.id' => 'required|exists:order_requests,id',
        ]);

        if($validator->fails()) { return response(['errors' => $validator->errors()], 422);}

        ManifestDetail::where('manifest_id', $manifest->id)->delete();

        $order_requests = $request->order_requests;
        foreach($order_requests as $order_request) {
            ManifestDetail::create(['manifest_id' => $manifest->id, 'order_request_id' => $order_request['id']]);
        }
        $manifest->logistic;
        return response(['success' => ['manifest' => $manifest]], 200);
    }

    public function delete(Request $request, Manifest $manifest) {
        $manifest->delete();
        return response(['success' => ['message' => 'Manifest Deleted']], 201);
    }

    public function supplier_logistics(Request $request) {
        $_supplier_logistics = Logistic::where('supplier_id', $request->user()->information->id)->get();
        $supplier_logistics = [];
        foreach($_supplier_logistics as $logistic) {
            $supplier_logistics[] = [
                'id' => $logistic->id,
                'name' => $logistic->name,
            ];
        }
        return response(['success' => ['logistics' => $supplier_logistics]], 200);
    }

    public function supplier_order_requests(Request $request) {

        $supplier_order_requests = OrderRequest::select('order_requests.id', 'order_requests.code', 'customers.name as customer', 'customers.address as address')
                                    ->join('customers', 'customers.id', 'order_requests.customer_id')
                                    ->leftJoin('manifest_details', 'order_requests.id', 'manifest_details.order_request_id')
                                    ->where('order_requests.status', 'Approved')->whereNull('manifest_details.order_request_id')->get();
        $order_requests = [];
        foreach($supplier_order_requests as $order_request) {
            $order_requests[] = [
                'id' => $order_request->id,
                'code' => $order_request->code,
                'customer' => $order_request->customer,
                'address' => $order_request->address,
            ];
        }

        return response(['success' => ['order_requests' => $order_requests]], 200);
    }
}
