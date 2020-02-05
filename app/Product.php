<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        // 'supplier_id', 
        'code', 'name', 'description', 'unit', 'price'
    ];

    // public function supplier() {
    //     return $this->belongsTo('App\Supplier', 'supplier_id');
    // }

    public function orderRequestDetail() {
        return $this->hasMany('App\OrderRequestDetail', 'product_id');
    }
}
