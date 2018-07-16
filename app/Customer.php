<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Customer
 *
 * @property int $id
 * @property string|null $customers_firstname
 * @property string|null $customers_lastname
 * @property string|null $customers_email_address
 * @property string|null $phone
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomersEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomersFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereCustomersLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Customer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Customer extends Model
{
    //
}
