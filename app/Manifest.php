<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    protected $fillable = [
        'code', 'supplier_id', 'logistic_id', 'delivery_date'
    ];

    public function supplier() {
        return $this->belongsTo('App\Supplier', 'supplier_id');
    }

    public function logistic() {
        return $this->belongsTo('App\Logistic', 'logistic_id');
    }

    public function details() {
        return $this->hasMany('App\ManifestDetail', 'manifest_id');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($user) {
            $latest_code = \App\Manifest::select('code')->orderBy('id', 'desc')->first();

            if(!$latest_code) {
                $user->code = 'SM000001';
            } else {
                $code_number = substr($latest_code->code, 2);
                $new_code_number = (integer) $code_number + 1;
                $new_code_number = str_pad($new_code_number, 6, 0, STR_PAD_LEFT);
                $user->code = 'SM'.$new_code_number;
            }
        });
    }
}
