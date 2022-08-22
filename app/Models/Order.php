<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public function customer(){
        return $this->belongsTo('App\Models\Customer');
    }

    public function orderitem(){
        return $this->hasMany('App\Models\OrderItem');
    }

    public function payment(){
        return $this->hasMany('App\Models\Payment');
    }
}
