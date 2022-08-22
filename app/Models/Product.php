<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public function orderitem(){
        return $this->hasMany('App\Models\OrderItem');
    }
}
