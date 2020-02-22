<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logistic extends Model
{
    protected $fillable = [
        'user_id', 
        // 'supplier_id', 
        'name', 'address', 'latitude', 'longitude', 'contact_number'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    // public function supplier() {
    //     return $this->belongsTo('App\Supplier', 'supplier_id');
    // }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($user) {
            $latest_code = \App\Logistic::select('code')->orderBy('id', 'desc')->first();

            if(!$latest_code) {
                $user->code = 'SL000001';
            } else {
                $code_number = substr($latest_code->code, 2);
                $new_code_number = (integer) $code_number + 1;
                $new_code_number = str_pad($new_code_number, 6, 0, STR_PAD_LEFT);
                $user->code = 'SL'.$new_code_number;
            }
        });
    }
}
