<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logistic extends Model
{
    protected $fillable = [
        'user_id', 'supplier_id', 'name', 'address', 'latitude', 'longitude', 'contact_number'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function supplier() {
        return $this->belongsTo('App\Supplier', 'supplier_id');
    }
}
