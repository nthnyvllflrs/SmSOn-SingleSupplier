<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'address', 'contact_number'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function products() {
        return $this->hasMany('App\Product', 'supplier_id');
    }

    public function logistics() {
        return $this->hasMany('App\Logistic', 'supplier_id');
    }

    public function orderRequests() {
        return $this->hasMany('App\Supplier', 'supplier_id');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($user) {
            $latest_code = \App\Supplier::select('code')->orderBy('id', 'desc')->first();

            if(!$latest_code) {
                $user->code = 'SS000001';
            } else {
                $code_number = substr($latest_code->code, 2);
                $new_code_number = (integer) $code_number + 1;
                $new_code_number = str_pad($new_code_number, 6, 0, STR_PAD_LEFT);
                $user->code = 'SS'.$new_code_number;
            }
        });
    }
}
