<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $guarded = [];
    protected $casts =[
        'order_items' => 'array',
        'customer_details' => 'array'
    ];
}
