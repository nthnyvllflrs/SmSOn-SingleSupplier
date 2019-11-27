<?php

namespace App;

use Laravel\Passport\HasApiTokens;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'username', 'password', 'code', 'role', 'active'
    ];

    protected $hidden = [
        'password',
    ];

    public function information() {
        if($this->role == 'Customer') {
            return $this->hasOne('App\Customer', 'user_id');
        } elseif($this->role == 'Logistic') {
            return $this->hasOne('App\Logistic', 'user_id');
        } elseif($this->role == 'Supplier') {
            return $this->hasOne('App\Supplier', 'user_id');
        }
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($user) {
            $latest_code = \App\User::select('code')->orderBy('id', 'desc')->first();

            if(!$latest_code) {
                $user->code = 'SU000001';
            } else {
                $code_number = substr($latest_code->code, 2);
                $new_code_number = (integer) $code_number + 1;
                $new_code_number = str_pad($new_code_number, 6, 0, STR_PAD_LEFT);
                $user->code = 'SU'.$new_code_number;
            }
        });
    }
}
