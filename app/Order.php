<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $order_items
 * @property string $environment
 * @property string $product_look_up
 * @property string $customer_details
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $remote_order_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCustomerDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereEnvironment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereOrderItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereProductLookUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereRemoteOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 * @property string|null $remote_order_created_date
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereRemoteOrderCreatedDate($value)
 */
class Order extends Model
{
    //
    protected $guarded = [];
    protected $casts = [
        'order_items' => 'array',
        'customer_details' => 'array',
        'environment' => 'array'
    ];
    
}
