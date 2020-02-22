<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    protected $fillable = [
        'customer_id', 
        // 'supplier_id', 
        'status'
    ];

    public function customer() {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    // public function supplier() {
    //     return $this->belongsTo('App\Supplier', 'supplier_id');
    // }

    public function details() {
        return $this->hasMany('App\OrderRequestDetail', 'order_request_id');
    }
    
    public static function boot()
    {
        parent::boot();
        self::creating(function ($user) {
            $latest_code = \App\OrderRequest::select('code')->orderBy('id', 'desc')->first();

            if(!$latest_code) {
                $user->code = 'SOR000000001';
            } else {
                $code_number = substr($latest_code->code, 3);
                $new_code_number = (integer) $code_number + 1;
                $new_code_number = str_pad($new_code_number, 9, 0, STR_PAD_LEFT);
                $user->code = 'SOR'.$new_code_number;
            }
        });
    }
}
