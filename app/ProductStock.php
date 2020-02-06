<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id', 'available'
    ];

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
