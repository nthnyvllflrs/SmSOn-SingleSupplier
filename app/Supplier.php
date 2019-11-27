<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'user_id', 'name', 'address', 'contact_number'
    ];

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
