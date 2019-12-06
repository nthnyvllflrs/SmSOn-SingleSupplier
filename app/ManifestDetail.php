<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManifestDetail extends Model
{
    protected $fillable = [
        'manifest_id', 'order_request_id'
    ];

    public function manifest() {
        return $this->belongsTo('App\Manifest', 'manifest_id');
    }

    public function orderRequest() {
        return $this->belongsTo('App\OrderRequest', 'order_request_id');
    }
}
