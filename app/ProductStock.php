<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $fillable = [
        'product_id', 'available', 'approved', 'pending', 'delivered', 'threshold'
    ];

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function getStockStatusAttribute() {
        if($this->available <= $this->threshold) {
            return 'Critical';
        } else {
            return 'Good';
        }
    }
}
