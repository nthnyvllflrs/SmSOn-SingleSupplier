<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    protected $fillable = [
        'customer_id', 'supplier_id', 'status'
    ];

    public function details() {
        return $this->hasMany('App\OrderRequestDetails', 'order_request_id');
    }
}
