<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ITextMoIncomingSMS extends Model
{
    protected $fillable = [
        'originator', 'gateway', 'message', 'timestamp'
    ];
}
