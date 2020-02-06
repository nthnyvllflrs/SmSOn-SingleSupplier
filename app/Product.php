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

    public function stock() {
        return $this->hasOne('App\ProductStock', 'product_id');
    }

    public static function boot() {
        parent::boot();

        self::created(function($model){
            \App\ProductStock::create(['product_id' => $model->id]);
        });
    }
}
