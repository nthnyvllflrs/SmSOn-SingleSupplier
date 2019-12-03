<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderRequestDetail extends Model
{
    protected $fillable = [
        'order_request_id', 'product_id', 'quantity', 'total'
    ];

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
