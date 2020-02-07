<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id', 'available', 'approved', 'pending', 'delivered'
    ];

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
